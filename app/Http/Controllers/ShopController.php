<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with(['product.store', 'product.images', 'product.category', 'product.defaultUnit', 'productUnit'])
            ->where('status', true)
            ->whereHas('product', function($q) {
                $q->where('status', true);
            });

        // Search by product name
        if ($request->search) {
            $query->whereHas('product', function($q) use ($request) {
                $q->whereRaw('LOWER(name) like ?', ['%' . strtolower($request->search) . '%']);
            });
        }

        // Filter by category
        if ($request->category) {
            $query->whereHas('product.category', function($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        $variants = $query->paginate(12)->withQueryString();
        $categories = Category::all();
        return view('shop.index', compact('variants', 'categories'));
    }

    public function show($variant)
    {
        $variant = ProductVariant::with(['product.store', 'product.images', 'product.category', 'product.defaultUnit', 'product.variants.productUnit'])->findOrFail($variant);
        $product = $variant->product;
        $variants = $product->variants()->with('productUnit')->get();

        // Get related variants from same category, excluding current product
        $relatedVariants = ProductVariant::with(['product.store', 'product.images', 'product.category'])
            ->where('status', true)
            ->where('id', '!=', $variant->id)
            ->whereHas('product', function($q) use ($product) {
                $q->where('category_id', $product->category_id)->where('status', true);
            })
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('shop.show', compact('variant', 'product', 'variants', 'relatedVariants'));
    }
}
