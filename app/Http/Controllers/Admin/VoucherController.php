<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::latest()->get();
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vouchers,code',
            'type' => 'required|in:fixed,percent',
            'amount' => 'required|numeric|min:0',
            'min_spend' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        Voucher::create([
            'code' => strtoupper(trim($request->code)),
            'type' => $request->type,
            'amount' => $request->amount,
            'min_spend' => $request->min_spend ?? 0,
            'max_discount' => $request->max_discount,
            'usage_limit' => $request->usage_limit,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        return back()->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();
        return back()->with('success', 'Voucher berhasil dihapus.');
    }
}
