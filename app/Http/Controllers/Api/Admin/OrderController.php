<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $orders = Order::with(['user', 'orderItems'])
            ->when($search, function ($query, $search) {
                return $query->where('order_number', 'like', "%{$search}%")
                             ->orWhereHas('user', function($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                             });
            })
            ->when($status, function ($query, $status) {
                if ($status !== 'all') {
                    return $query->where('status', $status);
                }
            })
            ->latest()
            ->paginate(15);

        return response()->json($orders);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending_confirmation,awaiting_payment,payment_uploaded,processing,shipped,completed,cancelled',
            'tracking_number' => 'nullable|string'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->input('status');
        
        if ($request->has('tracking_number')) {
            $order->tracking_number = $request->input('tracking_number');
        }

        $order->save();

        return response()->json(['message' => 'Status pesanan berhasil diperbarui', 'order' => $order]);
    }
}
