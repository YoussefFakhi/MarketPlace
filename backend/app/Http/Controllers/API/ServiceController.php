<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ServiceController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Start with only active services (is_active = true)
        $query = Service::where('is_active',true);

        // Filter by category if user sends category_id parameter
        if($request->has('category_id')){
            $query->where('category_id',$request->category_id);
        }

        // Load related data (category and user info) and paginate results
        // Paginate means we show 10 services per page instead of all at once
        $services = $query->with(['category','user'])->paginate(10);

        return response()->json(['success'=>true,'message'=>$services]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $this->authorize('create',Service::class);

        $service = Service::create([
            'title' => $request->title,           // Service title from request
            'description' => $request->description, // Service description
            'price' => $request->price,           // Price set by freelancer
            'category_id' => $request->category_id, // Which category it belongs to
            'user_id' => $request->user()->id, // Current logged-in user (the freelancer)
            'is_active' => true,                  // Default to active (visible to customers)
        ]);
        return response()->json([
            'success'=>true,
            'message'=>'Service created successfully',
            'data'=>$service->load(['category','user']) // Include related data in response
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        // Laravel automatically loads the service based on ID (Route Model Binding)
        // But we need to load related data (category and user) to show full details
        $service->load(['category','user']);

        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    /**
     * UPDATE EXISTING SERVICE (OWNER OR ADMIN ONLY)
     * Purpose: Allow service owner or admin to edit service details
     */
    public function update(Request $request, Service $service)
    {
        // Check if current user can update this specific service
        // Policy allows: service owner OR admin
        $this->authorize('update',$service);


        $service->update([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service->load(['category', 'user']) // Return updated service with relations
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Service $service)
    {
        // Check if current user can delete this specific service
        // Policy allows: service owner OR admin
        $this->authorize('delete',$service);

        // Remove service from database
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully'
        ]);
    }


    public function myServices(Request $request){
        $services = Service::where('user_id',$request->user()->id)
                            ->with(['category','user'])
                            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }


}
