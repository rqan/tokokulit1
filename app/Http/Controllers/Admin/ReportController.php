<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Show sales report summary.
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());

        $ordersQuery = Order::with(['user', 'items', 'payments'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        $totalOrders = (clone $ordersQuery)->count();
        $completedOrders = (clone $ordersQuery)->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])->count();
        $totalRevenue = (clone $ordersQuery)->whereIn('status', ['paid', 'processing', 'shipped', 'completed'])->sum('grand_total');

        $orders = $ordersQuery->latest()->get();

        return view('admin.report', compact('orders', 'startDate', 'endDate', 'totalOrders', 'completedOrders', 'totalRevenue'));
    }

    /**
     * Export sales report to CSV / Excel.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfDay()->toDateString());

        $fileName = "laporan-penjualan-{$startDate}-sd-{$endDate}.csv";

        $orders = Order::with(['user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['No Invoice', 'Tanggal', 'Nama Pelanggan', 'Email', 'Telepon', 'Status', 'Subtotal', 'Ongkir', 'Voucher', 'Grand Total'];

        $callback = function() use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->invoice_number ?? "ORDER-{$order->id}",
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->shipping_name,
                    $order->shipping_email,
                    $order->shipping_phone,
                    strtoupper($order->status),
                    $order->subtotal,
                    $order->shipping_fee ?? 0,
                    $order->voucher_code ? "{$order->voucher_code} (-{$order->discount_amount})" : '-',
                    $order->grand_total ?? $order->subtotal,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
