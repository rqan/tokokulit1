<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Address;

class AddressController extends Controller
{
    /**
     * Tampilkan daftar alamat pengguna.
     */
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderByDesc('is_primary')
            ->latest()
            ->get();
            
        return view('addresses.index', compact('addresses'));
    }

    /**
     * Tampilkan form tambah alamat.
     */
    public function create()
    {
        return view('addresses.create');
    }

    /**
     * Simpan alamat baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'nullable|string|max:255',
            'full_address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'is_primary' => 'nullable|boolean',
        ]);

        // Jika ini alamat pertama atau di-set sebagai utama, matikan primary yang lain
        $isPrimary = $request->boolean('is_primary');
        $addressCount = Address::where('user_id', Auth::id())->count();
        
        if ($isPrimary || $addressCount === 0) {
            $isPrimary = true;
            Address::where('user_id', Auth::id())->update(['is_primary' => false]);
        }

        Address::create([
            'user_id' => Auth::id(),
            'label' => $request->label,
            'full_address' => $request->full_address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'is_primary' => $isPrimary,
        ]);

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit alamat.
     */
    public function edit(Address $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);
        return view('addresses.edit', compact('address'));
    }

    /**
     * Perbarui alamat.
     */
    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);

        $request->validate([
            'label' => 'nullable|string|max:255',
            'full_address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'is_primary' => 'nullable|boolean',
        ]);

        $isPrimary = $request->boolean('is_primary');

        if ($isPrimary) {
            Address::where('user_id', Auth::id())->where('id', '!=', $address->id)->update(['is_primary' => false]);
        }

        $address->update([
            'label' => $request->label,
            'full_address' => $request->full_address,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'is_primary' => $isPrimary,
        ]);

        return redirect()->route('addresses.index')->with('success', 'Alamat berhasil diperbarui.');
    }

    /**
     * Jadikan alamat sebagai utama.
     */
    public function setPrimary(Address $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);

        Address::where('user_id', Auth::id())->update(['is_primary' => false]);
        $address->update(['is_primary' => true]);

        return back()->with('success', 'Alamat utama berhasil diperbarui.');
    }

    /**
     * Hapus alamat.
     */
    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);
        
        $wasPrimary = $address->is_primary;
        $address->delete();

        // Jika alamat utama dihapus, jadikan alamat pertama lainnya sebagai utama
        if ($wasPrimary) {
            $newPrimary = Address::where('user_id', Auth::id())->first();
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Alamat berhasil dihapus.');
    }
}
