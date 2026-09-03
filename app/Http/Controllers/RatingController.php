<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Rating;

class RatingController extends Controller
{
    /**
     * Menampilkan form rating melalui token unik.
     */
    public function showForm($token)
    {
        $order = Order::where('rating_token', $token)
            ->where('status', 'completed')
            ->firstOrFail();

        if ($order->rating()->exists()) {
            return view('rating-expired', compact('order'));
        }

        return view('rating-form', compact('order'));
    }

    /**
     * Menyimpan data rating dari form.
     */
    public function store(Request $request, $token)
    {
        $order = Order::where('rating_token', $token)
            ->where('status', 'completed')
            ->firstOrFail();

        if ($order->rating()->exists()) {
            return back()->with('error', 'Rating sudah diberikan sebelumnya untuk pesanan ini.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        Rating::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return view('rating-success', compact('order'));
    }
}
