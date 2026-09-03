<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;

class SalesController extends Controller
{
    /**
     * Menampilkan laporan penjualan.
     */
    public function index()
    {
        $monthly_revenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Order::whereIn('status', ['processing', 'shipped', 'completed'])
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('grand_total');
                
            $monthly_revenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => (float) $revenue
            ];
        }

        $total_completed_orders = Order::where('status', 'completed')->count();
        
        $total_revenue_completed = Order::where('status', 'completed')->sum('grand_total');
        $average_order_value = $total_completed_orders > 0 ? $total_revenue_completed / $total_completed_orders : 0;

        return view('admin.sales.index', compact('monthly_revenue', 'total_completed_orders', 'average_order_value'));
    }
}
