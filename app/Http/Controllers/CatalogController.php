<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class CatalogController extends Controller
{
    public function index()
    {
        try {
            $products = Product::all()->map(fn($item) => $item->toArray());
        } catch (\Exception $e) {
            $products = [];
        }

        // Load verified ratings for landing page section
        try {
            $ratings = Rating::visible()
                ->with(['user:id,name', 'order:id,invoice_number'])
                ->latest()
                ->take(6)
                ->get();
            $averageRating = Rating::visible()->avg('rating');
            $totalRatings = Rating::visible()->count();
        } catch (\Exception $e) {
            $ratings = collect([]);
            $averageRating = 0;
            $totalRatings = 0;
        }

        return view('catalog', [
            'products' => $products,
            'ratings' => $ratings,
            'averageRating' => round($averageRating, 1),
            'totalRatings' => $totalRatings,
        ]);
    }

    public function katalog(Request $request)
    {
        $query = Product::query();

        if ($request->has('q') && $request->q != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->has('category') && $request->category != '') {
            $query->whereHas('category', function ($q) use ($request) { $q->where('name', $request->category)->orWhere('slug', $request->category); });
        }

        if ($request->has('gender') && $request->gender != '') {
            $gender = strtolower($request->gender);
            $query->whereRaw('LOWER(gender) = ?', [$gender]);
        }

        $products = $query->get()->map(fn($item) => $item->toArray());

        $categories = \App\Models\Category::pluck('name');

        return view('katalog', [
            'products' => $products,
            'categories' => $categories,
            'q' => $request->q,
            'selectedCategory' => $request->category,
            'gender' => $request->gender
        ]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('product', ['product' => $product->toArray()]);
    }
}
