<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    /**
     * Menampilkan daftar metode pembayaran.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::all();
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    /**
     * Menyimpan metode pembayaran baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        PaymentMethod::create($validated);

        return back()->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    /**
     * Memperbarui metode pembayaran.
     */
    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');

        $paymentMethod->update($validated);

        return back()->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    /**
     * Menghapus metode pembayaran.
     */
    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->delete();

        return back()->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
