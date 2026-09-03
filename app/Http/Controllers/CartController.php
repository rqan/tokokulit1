<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang.
     */
    public function index()
    {
        return view('cart');
    }

    /**
     * Menambahkan produk ke keranjang.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = session()->get('cart', []);
        
        $size = $request->size ?: '';
        $color = $request->color ?: '';
        $cartKey = $product->id . ($size ? '_s_' . $size : '') . ($color ? '_c_' . $color : '');

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $request->quantity;
        } else {
            $cart[$cartKey] = [
                'product' => $product,
                'quantity' => $request->quantity,
                'size' => $size,
                'color' => $color,
            ];
        }

        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan ke keranjang.', 'cart_count' => count($cart)]);
        }

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Memperbarui kuantitas produk di keranjang.
     */
    public function update(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
            'quantity' => 'required|integer',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->cart_key])) {
            if ($request->quantity <= 0) {
                unset($cart[$request->cart_key]);
            } else {
                $cart[$request->cart_key]['quantity'] = $request->quantity;
            }
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    /**
     * Menghapus produk dari keranjang.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'cart_key' => 'required|string',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$request->cart_key])) {
            unset($cart[$request->cart_key]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }
}

