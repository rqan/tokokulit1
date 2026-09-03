<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;

class InvoiceController extends Controller
{
    /**
     * Menampilkan faktur (invoice) pesanan.
     */
    public function show($invoiceNumber)
    {
        $order = Order::with(['items.product', 'user'])
            ->where('invoice_number', $invoiceNumber)
            ->firstOrFail();

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('invoice', compact('order', 'paymentMethods'));
    }

    /**
     * Cetak / Stream PDF Invoice yang dioptimalkan untuk cetak & unduh.
     */
    public function pdf($invoiceNumber)
    {
        $order = Order::with(['items.product', 'user'])
            ->where('invoice_number', $invoiceNumber)
            ->firstOrFail();

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('invoice-pdf', compact('order', 'paymentMethods'));
    }
}
