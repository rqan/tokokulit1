<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();

        return view('admin.products.index', [
            'title' => 'Manajemen Produk - Panel Admin',
            'products' => $products
        ]);
    }

    public function create(Request $request)
    {
        if ($request->session()->get('role') !== 'superadmin' && optional(Auth::user())->role !== 'superadmin') {
            return redirect('/admin/products')->with('error', 'Akses ditolak: Hanya Superadmin yang bisa menambah produk.');
        }

        return view('admin.products.create', ['title' => 'ADD PRODUCT']);
    }

    public function store(Request $request)
    {
        if ($request->session()->get('role') !== 'superadmin' && optional(Auth::user())->role !== 'superadmin') {
            return redirect('/admin/products')->with('error', 'Akses ditolak.');
        }

        $sizes = $request->input('sizes') ? array_map('trim', explode(',', $request->input('sizes'))) : null;
        $colors = $request->input('colors') ? array_map('trim', explode(',', $request->input('colors'))) : null;

        Product::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'category_id' => \App\Models\Category::firstOrCreate(['name' => $request->input('new_category') ?: ($request->input('category') ?? 'Uncategorized')], ['slug' => \Illuminate\Support\Str::slug($request->input('new_category') ?: ($request->input('category') ?? 'Uncategorized'))])->id,
            'gender' => $request->input('gender'),
            'stock' => $request->input('stock') ?? 0,
            'image_url' => $request->input('image_url'),
            'sizes' => $sizes,
            'colors' => $colors,
        ]);

        return redirect('/admin/products')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $product = Product::with('variants', 'category')->findOrFail($id);
        return view('admin.products.edit', ['title' => 'EDIT PRODUCT', 'product' => $product]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $sizes = $request->input('sizes') ? array_map('trim', explode(',', $request->input('sizes'))) : null;
        $colors = $request->input('colors') ? array_map('trim', explode(',', $request->input('colors'))) : null;

        $product->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'category_id' => \App\Models\Category::firstOrCreate(['name' => $request->input('new_category') ?: $request->input('category', optional($product->category)->name ?? 'Uncategorized')], ['slug' => \Illuminate\Support\Str::slug($request->input('new_category') ?: $request->input('category', optional($product->category)->name ?? 'Uncategorized'))])->id,
            'gender' => $request->input('gender', $product->gender),
            'stock' => $request->input('stock', $product->stock),
            'sizes' => $sizes,
            'colors' => $colors,
            'availability_status' => $request->input('availability_status', $product->availability_status),
            'provenance' => $request->input('provenance', $product->provenance),
        ]);

        // Handle Dynamic Variants
        if ($request->has('variants')) {
            $product->variants()->delete(); // clear old ones
            $totalStock = 0;
            foreach ($request->input('variants') as $variant) {
                if (!empty($variant['size']) || !empty($variant['color']) || !empty($variant['stock'])) {
                    $product->variants()->create([
                        'size' => $variant['size'] ?? null,
                        'color' => $variant['color'] ?? null,
                        'stock' => $variant['stock'] ?? 0,
                    ]);
                    $totalStock += (int)($variant['stock'] ?? 0);
                }
            }
            if (count($request->input('variants')) > 0) {
                $product->update(['stock' => $totalStock]);
            }
        }

        if ($request->filled('image_url')) {
            $product->update(['image_url' => $request->input('image_url')]);
        }
        // Handle images if uploaded via file input
        if ($request->hasFile('images')) {
            // minimal implementation for file upload if needed, keeping it as is since it wasn't implemented before
        }

        return redirect('/admin/products')->with('success', 'Produk berhasil diubah.');
    }

    public function delete(Request $request, $id)
    {
        if ($request->session()->get('role') !== 'superadmin' && optional(Auth::user())->role !== 'superadmin') {
            return redirect('/admin/products')->with('error', 'Akses ditolak: Hanya Superadmin yang bisa menghapus produk.');
        }

        $product = Product::findOrFail($id);
        $product->delete();

        return redirect('/admin/products')->with('success', 'Produk berhasil dihapus.');
    }
}
