<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blacklist;

class BlacklistController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string',
            'ip_address' => 'nullable|string',
            'reason' => 'nullable|string'
        ]);

        Blacklist::create($request->only(['phone', 'ip_address', 'reason']));

        return back()->with('success', 'Berhasil ditambahkan ke daftar hitam (Blacklist).');
    }
}
