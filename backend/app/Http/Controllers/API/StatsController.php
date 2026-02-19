<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        // Guard: Only admins can see global stats
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized Access'], 403);
        }

        // 1. Basic Totals
        $totalRevenue = Order::where('status', 'completed')->sum('montant');
        $totalOrders = Order::count();
        $totalServices = Service::count();
        $totalUsers = User::count();

        // 2. Chart Data: Orders for the last 7 days
        $days = [];
        $orderCounts = [];
        $revenueData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i); //Carbon is a date library in Laravel  //  subDays($i) => (-)
            $days[] = $date->format('D'); // Mon, Tue, etc.
            
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $orderCounts[] = Order::whereBetween('created_at', [$dayStart, $dayEnd])->count();
            $revenueData[] = Order::where('status', 'completed')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->sum('montant');
        }

        // 3. Status Distribution (optional but cool for charts)
        $orderStatusDistribution = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'totals' => [
                    'revenue' => (float)$totalRevenue,
                    'orders' => $totalOrders,
                    'services' => $totalServices,
                    'users' => $totalUsers
                ],
                'charts' => [
                    'labels' => $days,
                    'orders' => $orderCounts,
                    'revenue' => $revenueData
                ],
                'status_distribution' => $orderStatusDistribution
            ]
        ]);
    }
}
