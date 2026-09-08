<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function stats(Request $request)
    {
        $range = $request->input('range', '30d'); // 7d, 30d, 90d, all

        $days = 30;
        if ($range === '7d') $days = 7;
        if ($range === '90d') $days = 90;
        if ($range === 'all') $days = 365; // just arbitrary limit

        $startDate = Carbon::now()->subDays($days)->startOfDay();
        $previousStartDate = Carbon::now()->subDays($days * 2)->startOfDay();
        $previousEndDate = Carbon::now()->subDays($days)->endOfDay();

        // Current Period Data
        $ordersCurrent = Order::where('created_at', '>=', $startDate)->count();
        $revenueCurrent = Order::where('created_at', '>=', $startDate)
            ->whereIn('status', ['paid', 'shipped', 'completed'])
            ->sum('total_amount');
            
        $canceledOrdersCurrent = Order::where('created_at', '>=', $startDate)
            ->whereIn('status', ['canceled'])->count();

        // Previous Period Data
        $ordersPrev = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
        $revenuePrev = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->whereIn('status', ['paid', 'shipped', 'completed'])
            ->sum('total_amount');
        
        $canceledOrdersPrev = Order::whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->whereIn('status', ['canceled'])->count();

        // Calc Delta (%)
        $calcDelta = function($curr, $prev) {
            if ($prev == 0) return $curr > 0 ? 100 : 0;
            return round((($curr - $prev) / $prev) * 100, 1);
        };

        $ordersDelta = $calcDelta($ordersCurrent, $ordersPrev);
        $revenueDelta = $calcDelta($revenueCurrent, $revenuePrev);
        $canceledDelta = $calcDelta($canceledOrdersCurrent, $canceledOrdersPrev);

        // AOV (Average Order Value)
        $aovCurrent = $ordersCurrent > 0 ? $revenueCurrent / $ordersCurrent : 0;
        $aovPrev = $ordersPrev > 0 ? $revenuePrev / $ordersPrev : 0;
        $aovDelta = $calcDelta($aovCurrent, $aovPrev);

        // Chart Data (Trend)
        $chartData = [];
        // if range is 7d, show 7 days. If 30d, group by 3 days or just show 30.
        // For simplicity, we'll return daily data up to the requested days
        $trendDays = min($days, 30); // limit to 30 points to not overcrowd chart
        
        for ($i = $trendDays - 1; $i >= 0; $i--) {
            $dateStart = Carbon::now()->subDays($i)->startOfDay();
            $dateEnd = Carbon::now()->subDays($i)->endOfDay();
            
            $dailyRevenue = Order::whereBetween('created_at', [$dateStart, $dateEnd])
                ->whereIn('status', ['paid', 'shipped', 'completed'])
                ->sum('total_amount');
            
            $chartData[] = [
                'name' => Carbon::now()->subDays($i)->format('d M'),
                'revenue' => $dailyRevenue
            ];
        }

        // Distribusi Status Pesanan
        $orderStatuses = Order::where('created_at', '>=', $startDate)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => ucfirst($item->status),
                    'value' => $item->count
                ];
            });

        return response()->json([
            'summary' => [
                'orders' => [
                    'value' => $ordersCurrent,
                    'delta' => $ordersDelta
                ],
                'revenue' => [
                    'value' => $revenueCurrent,
                    'delta' => $revenueDelta
                ],
                'canceled' => [
                    'value' => $canceledOrdersCurrent,
                    'delta' => $canceledDelta
                ],
                'aov' => [
                    'value' => round($aovCurrent),
                    'delta' => $aovDelta
                ]
            ],
            'revenue_trend' => $chartData,
            'order_statuses' => $orderStatuses
        ]);
    }
}
