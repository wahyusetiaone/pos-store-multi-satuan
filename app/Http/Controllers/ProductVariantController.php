<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function index(Request $request)
    {
        $variants = ProductVariant::with(['product', 'productUnit'])
            ->when($request->search, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhereHas('product', function($q2) use ($request) {
                          $q2->where('name', 'like', '%' . $request->search . '%');
                      });
                });
            })
            ->orderByDesc('id')
            ->paginate(20);
        return view('product_variants.index', compact('variants'));
    }

    public function create()
    {
        $products = Product::all();
        $productUnits = ProductUnit::with('product', 'unit')->get();
        return view('product_variants.create', compact('products', 'productUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_unit_id' => 'required|exists:product_unit,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);
        ProductVariant::create($validated);
        return redirect()->route('product-variants.index')->with('success', 'Variant berhasil ditambahkan.');
    }

    public function show(ProductVariant $productVariant)
    {
        $productVariant->load('product', 'productUnit');
        return view('product_variants.show', compact('productVariant'));
    }

    public function edit(ProductVariant $productVariant)
    {
        $products = Product::all();
        $productUnits = ProductUnit::with(['product', 'unit'])->where('product_id', $productVariant->product_id)->get();
        return view('product_variants.edit', compact('productVariant', 'products', 'productUnits'));
    }

    public function update(Request $request, ProductVariant $productVariant)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_unit_id' => 'required|exists:product_unit,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);
        $productVariant->update($validated);
        return redirect()->route('product-variants.index')->with('success', 'Variant berhasil diubah.');
    }

    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();
        return redirect()->route('product-variants.index')->with('success', 'Variant berhasil dihapus.');
    }
}
