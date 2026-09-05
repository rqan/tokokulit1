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

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) { 
                $q->where('name', $request->category)->orWhere('slug', $request->category); 
            });
        }

        if ($request->filled('gender')) {
            $gender = strtolower($request->gender);
            $query->whereRaw('LOWER(gender) = ?', [$gender]);
        }

        if ($request->filled('min_price')) $query->where('price', '>=', $request->min_price);
        if ($request->filled('max_price')) $query->where('price', '<=', $request->max_price);

        $sort = $request->get('sort', 'newest');
        switch($sort) {
            case 'price_asc':  $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'newest':
            default:           $query->latest(); break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = \App\Models\Category::pluck('name');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'products' => $products->items(),
                'links'    => (string) $products->links('vendor.pagination.tailwind'),
                'total'    => $products->total()
            ]);
        }

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
