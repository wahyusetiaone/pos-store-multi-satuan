<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['store', 'images', 'category'])
                       ->where('status', true);

        // Search by name
        if ($request->search) {
            $query->whereRaw('LOWER(name) like ?', ['%' . strtolower($request->search) . '%']);
        }

        // Filter by category
        if ($request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('id', $request->category);
            });
        }
        $products = $query->paginate(12)->withQueryString();

        $categories = Category::all();

        return view('shop.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load(['store', 'images', 'category']);

        // Get related products from same category, excluding current product
        $relatedProducts = Product::with(['store', 'images', 'category'])
            ->where('status', true)
            ->where('id', '!=', $product->id)
            ->when($product->category_id, function($query) use ($product) {
                return $query->where('category_id', $product->category_id);
            })
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}

