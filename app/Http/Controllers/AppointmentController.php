<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'notes' => 'nullable|string|max:500',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to book a private viewing.');
        }

        Appointment::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Your private viewing request has been submitted. Our concierge will contact you shortly.');
    }
}
