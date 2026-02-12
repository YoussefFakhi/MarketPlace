<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Jobs\SendOrderConfirmationToClient;
use App\Models\Service;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Jobs\SendOrderCreatedNotification;
use App\Jobs\SendOrderStatusUpdatedNotification;

class OrderController extends Controller
{    use AuthorizesRequests;
    public function store(CreateOrderRequest  $request){
        $user = $request->user();

        // Authorize using policy
        $this->authorize('create', Order::class);

        //check if we have that service or not
        $service = Service::findOrFail($request->service_id);
        if(!$service->is_active){
            return response()->json([
                'message' => 'Cannot order inactive service.'
            ], 400);
        }

        DB::beginTransaction();  // Everything from now on must succeed together, or fail together

        try{
            $order = Order::create([
                'service_id'=> $request->service_id,
                'client_id'=> $user->id,
                'freelancer_id'=>$service->user_id,
                'status' => 'pending',
                'montant' => $service->price  // Store the service price at time of order

            ]);

            // Dispatch job to send email notification to freelancer
            SendOrderCreatedNotification::dispatch($order);

            // Dispatch job to send email notification to freelancer
            SendOrderConfirmationToClient::dispatch($order);

            DB::commit();  // all good all will be saved

            return response()->json([
                'message' => 'Order created successfully',
                'data' => $order->load('service', 'client', 'freelancer')
            ], 201);
        }catch(\Exception $e){
            DB::rollback();  // error hapend we will not save
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage()
            ], 500);
        }


    }

        public function update(UpdateOrderStatusRequest $request, Order $order)
    {
        $user = $request->user();

        // Authorize using policy
        if ($request->has('status')) {
            $this->authorize('updateStatus', $order);
        } else {
            $this->authorize('update', $order);
        }

        $oldStatus = $order->status;

        // Prevent status changes after completion/cancellation
        if ($request->has('status') && in_array($order->status, ['completed', 'cancelled'])) {
            return response()->json([
                'message' => 'Cannot update status of completed or cancelled order.'
            ], 400);
        }

        $order->update($request->only('status'));

        if($request->has('status')){
            SendOrderStatusUpdatedNotification::dispatch($order,$oldStatus,$order->status);
        }

        return response()->json([
            'message' => 'Order updated successfully',
            'data' => $order->load('service', 'client', 'freelancer')
        ]);
    }

        public function index(Request $request)
    {
        $user = $request->user();

        // Show orders based on user role
        if ($user->role === 'client') {
            $orders = Order::where('client_id', $user->id)

                ->with('service', 'freelancer')
                ->orderBy('created_at', 'desc')
                ->get();

        } elseif ($user->role === 'freelancer') {

            $orders = Order::where('freelancer_id', $user->id)
                ->with('service', 'client')
                ->orderBy('created_at', 'desc')
                ->get();

        } else {
            return response()->json([
                'message' => 'Unauthorized access.'
            ], 403);
        }

        return response()->json([
            'data' => $orders
        ]);
    }

    public function show(Order $order, Request $request)
    {
        // Authorize using policy
        $this->authorize('view', $order);

        return response()->json([
            'data' => $order->load('service', 'client', 'freelancer')
        ]);
    }
}
