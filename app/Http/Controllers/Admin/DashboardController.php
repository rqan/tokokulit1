<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard admin.
     */
    public function index()
    {
        $total_users = User::count();
        $total_products = Product::count();
        $total_orders = Order::count();
        $pending_orders = Order::where('status', 'pending_confirmation')->count();
        
        $total_revenue = Order::whereIn('status', ['processing', 'shipped', 'completed'])
            ->sum('grand_total');

        $recent_orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'total_users', 
            'total_products', 
            'total_orders', 
            'pending_orders', 
            'total_revenue', 
            'recent_orders'
        ));
    }
}
