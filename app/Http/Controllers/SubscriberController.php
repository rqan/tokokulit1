<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255|unique:subscribers,email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('error', 'Format email tidak valid atau sudah berlangganan.');
        }

        // Menyimpan ke database
        \App\Models\Subscriber::create(['email' => $request->email]);

        return back()->with('success', 'Terima kasih telah berlangganan newsletter kami!');
    }
}
