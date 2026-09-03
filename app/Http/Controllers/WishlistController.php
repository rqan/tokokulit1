<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    /**
     * Tampilkan halaman wishlist pengguna.
     */
    public function index()
    {
        $wishlists = Wishlist::with('product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
            
        return view('wishlist', compact('wishlists'));
    }

    /**
     * Tambah produk ke wishlist.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Produk ditambahkan ke wishlist.']);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke wishlist.');
    }

    /**
     * Hapus produk dari wishlist.
     */
    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $wishlist->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Produk dihapus dari wishlist.']);
        }

        return back()->with('success', 'Produk dihapus dari wishlist.');
    }
}
