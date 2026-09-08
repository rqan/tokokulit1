<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\Admin\ProductStoreRequest;

class ProductController extends Controller
{
    /**
     * Tampilkan data produk dengan pagination & search
     */
    public function index(Request $request)
    {
        // Pastikan role middleware sudah diterapkan di level route
        $search = $request->input('search');

        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhereHas('category', function($q) use ($search) {
                                 $q->where('name', 'like', "%{$search}%");
                             });
            })
            ->latest()
            ->paginate(10);

        return response()->json($products);
    }

    /**
     * Simpan data produk yang tervalidasi
     */
    public function store(ProductStoreRequest $request)
    {
        $data = $request->validated();
        
        $categoryId = null;
        if (!empty($data['new_category']) || !empty($data['category'])) {
            $catName = !empty($data['new_category']) ? $data['new_category'] : $data['category'];
            $category = \App\Models\Category::firstOrCreate(
                ['name' => $catName],
                ['slug' => \Illuminate\Support\Str::slug($catName)]
            );
            $categoryId = $category->id;
        }

        $product = Product::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'category_id' => $categoryId,
            'gender' => $data['gender'] ?? 'Unisex',
            'stock' => $data['stock'],
            'image_url' => $data['image_url'] ?? null,
            'sizes' => $data['sizes'] ?? [],
            'colors' => $data['colors'] ?? [],
        ]);

        if (!empty($data['variants']) && is_array($data['variants'])) {
            $totalStock = 0;
            foreach ($data['variants'] as $variant) {
                $product->variants()->create([
                    'size' => $variant['size'] ?? null,
                    'color' => $variant['color'] ?? null,
                    'stock' => $variant['stock'] ?? 0,
                ]);
                $totalStock += (int)($variant['stock'] ?? 0);
            }
            // Update master stock based on variants
            if ($totalStock > 0) {
                $product->update(['stock' => $totalStock]);
            }
        }

        return response()->json(['message' => 'Produk berhasil ditambahkan', 'product' => $product], 201);
    }

    /**
     * Hapus produk
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}
