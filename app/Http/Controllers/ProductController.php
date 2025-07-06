<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Store;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Image;
use Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use App\Exports\ProductExport;
use PDF;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'store']);

        // Filter by store if user doesn't have global access
        if (!auth()->user()->hasGlobalAccess()) {
            $query->where('store_id', auth()->user()->current_store_id);
        }

        // Filter by category if provided
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name if provided
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort by stock
        $sort = $request->get('sort', 'stock_asc');
        if ($sort === 'stock_desc') {
            $query->orderBy('stock', 'desc');
        } else {
            $query->orderBy('stock', 'asc');
        }

        // Handle pagination
        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all') {
            $products = $query->get();
            // Convert collection to LengthAwarePaginator to maintain consistency
            $products = new \Illuminate\Pagination\LengthAwarePaginator(
                $products,
                $products->count(),
                $products->count(),
                1
            );
        } else {
            $products = $query->paginate($perPage)->withQueryString();
        }

        // Get categories based on store access
        $categoryQuery = Category::query();
        if (!auth()->user()->hasGlobalAccess()) {
            $categoryQuery->where('store_id', auth()->user()->current_store_id);
        }
        $categories = $categoryQuery->get();

        $stores = [];

        if (auth()->user()->hasGlobalAccess()) {
            $stores = Store::where('is_active', true)->get();
        }

        if ($request->ajax() || ($request->acceptsJson() && $request->isJson())) {
            return response()->json([
                'products' => $products,
                'html' => view('products.partials.product_grid', compact('products'))->render()
            ]);
        }

        return view('products.index', compact('products', 'categories', 'stores'));
    }

    public function create()
    {
        $categories = Category::query();
        $stores = [];
        $units = Unit::all();
        if (auth()->user()->hasGlobalAccess()) {
            $stores = Store::where('is_active', true)->get();
        } else {
            // Filter categories by current store
            $categories->where('store_id', auth()->user()->current_store_id);
        }
        $categories = $categories->get();
        return view('products.create', compact('categories', 'stores', 'units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|boolean',
            'store_id' => auth()->user()->hasGlobalAccess() ? 'required|exists:stores,id' : 'prohibited',
            'selected_images' => 'nullable|json',
            'default_unit_id' => 'required|exists:units,id',
            'product_units' => 'nullable|array',
            'product_units.*.unit_id' => 'required_with:product_units|exists:units,id',
            'product_units.*.conversion_factor' => 'required_with:product_units|numeric|min:0.0001',
        ]);

        try {
            DB::beginTransaction();

            if (!auth()->user()->hasGlobalAccess()) {
                $validated['store_id'] = auth()->user()->current_store_id;
            }
            $product = Product::create(collect($validated)->except(['selected_images', 'product_units'])->toArray());

            // Handle product units (pivot)
            if ($request->has('product_units')) {
                $syncData = [];
                foreach ($request->product_units as $unitRow) {
                    if (!empty($unitRow['unit_id']) && !empty($unitRow['conversion_factor'])) {
                        $syncData[$unitRow['unit_id']] = ['conversion_factor' => $unitRow['conversion_factor']];
                    }
                }
                $product->units()->sync($syncData);
            }

            // Handle selected images from gallery
            if ($request->selected_images) {
                $selectedImages = json_decode($request->selected_images, true);
                foreach ($selectedImages as $imageId) {
                    $galleryImage = Image::find($imageId);

                    if ($galleryImage) {
                        // Copy the image file
                        $extension = pathinfo($galleryImage->path, PATHINFO_EXTENSION);
                        $newPath = 'products/' . Str::slug($product->name) . '-' . uniqid() . '.' . $extension;

                        Storage::copy('public/' . $galleryImage->path, 'public/' . $newPath);

                        // Create product image record
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image_path' => $newPath,
                        ]);
                    }
                }
            }

            DB::commit();
           if ($request->ajax() || ($request->acceptsJson() && $request->isJson() && $request->wantsJson())) {
                return response()->json(['success' => true, 'data' => $product, 'message' => 'Produk berhasil ditambahkan.']);
            }
            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollback();
            if ($request->ajax() || ($request->acceptsJson() && $request->isJson() && $request->wantsJson())) {
                return response()->json(['success' => false, 'message' => $e->getMessage()]);
            }
            return back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function show(Product $product)
    {
        $product->load('category', 'images');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $units = Unit::all();
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4048',
            'default_unit_id' => 'required|exists:units,id',
            'product_units' => 'nullable|array',
            'product_units.*.unit_id' => 'required_with:product_units|exists:units,id',
            'product_units.*.conversion_factor' => 'required_with:product_units|numeric|min:0.0001',
        ]);

        $productData = collect($validated)->except(['image', 'product_units'])->toArray();
        $product->update($productData);

        // Handle product units (pivot)
        if ($request->has('product_units')) {
            $syncData = [];
            foreach ($request->product_units as $unitRow) {
                if (!empty($unitRow['unit_id']) && !empty($unitRow['conversion_factor'])) {
                    $syncData[$unitRow['unit_id']] = ['conversion_factor' => $unitRow['conversion_factor']];
                }
            }
            $product->units()->sync($syncData);
        } else {
            $product->units()->detach();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->images()->exists()) {
                $oldImage = $product->images()->first();
                if (Storage::exists('public/' . $oldImage->image_path)) {
                    Storage::delete('public/' . $oldImage->image_path);
                }
                $oldImage->delete();
            }

            // Upload and save new image
            $imagePath = $request->file('image')->store('public/products');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => str_replace('public/', '', $imagePath),
                'is_primary' => true
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'store_id' => 'required|exists:stores,id'
        ]);

        try {
            Excel::import(new ProductImport($request->store_id), $request->file('file'));

            return redirect()->route('products.index')->with('success', 'Produk berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor produk: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'category',
            'name',
            'sku',
            'buy_price',
            'price',
            'stock',
            'description',
            'status'
        ];

        // Add example data
        $data = [
            [
                'category' => 'elektronik',
                'name' => 'Contoh Produk',
                'sku' => 'SKU001',
                'buy_price' => '90000',
                'price' => '100000',
                'stock' => '10',
                'description' => 'Deskripsi produk',
                'status' => '1'
            ]
        ];

        return Excel::download(new ProductExport($headers, $data), 'template_import_produk.xlsx');
    }

    public function generateBarcodePdf(Product $product)
    {
        $barcodes = array_fill(0, $product->stock, [
            'sku' => $product->sku,
            'name' => $product->name,
            'price' => $product->price
        ]);

        $pdf = PDF::loadView('products.barcode-pdf', [
            'barcodes' => $barcodes,
            'product' => $product
        ]);

        return $pdf->download('barcode-' . Str::slug($product->name) . '.pdf');
    }

    public function generateMultipleBarcodePdf(Request $request)
    {
        $productIds = explode(',', $request->product_ids);
        $products = Product::whereIn('id', $productIds)->get();

        $allBarcodes = [];
        foreach ($products as $product) {
            // Skip products with 0 or negative stock
            if ($product->stock <= 0) {
                continue;
            }

            // Generate array of barcode data based on product quantity
            $productBarcodes = array_fill(0, $product->stock, [
                'sku' => $product->sku,
                'name' => $product->name,
                'price' => $product->price
            ]);
            $allBarcodes = array_merge($allBarcodes, $productBarcodes);
        }

        // If no valid barcodes, redirect back with message
        if (empty($allBarcodes)) {
            return back()->with('error', 'Tidak ada produk dengan stock yang valid untuk dicetak');
        }

        $pdf = \PDF::loadView('products.multiple-barcode', [
            'barcodes' => $allBarcodes
        ]);

        return $pdf->download('barcodes.pdf');
    }

    public function getProduct(Request $request)
    {
        $product = Product::find($request->id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'sku' => $product->sku,
            ]
        ]);
    }

    public function getVariants(Request $request)
    {
        $productId = $request->id;
        $product = Product::with(['variants.productUnit.unit'])->find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }
        $variants = $product->variants->map(function($variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->name,
                'unit' => $variant->productUnit && $variant->productUnit->unit ? $variant->productUnit->unit->name : '-',
                'price' => $variant->price,
                'qty' => $variant->qty,
                'status' => $variant->status,
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $variants
        ]);
    }

    // API: Get product variants with unit info for a given product
    public function getVariantsWithUnits(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::with(['variants.productUnit.unit'])->find($productId);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan', 'data' => []], 404);
        }
        $variants = $product->variants->map(function($variant) {
            return [
                'id' => $variant->id,
                'name' => $variant->name,
                'unit_name' => $variant->productUnit && $variant->productUnit->unit ? $variant->productUnit->unit->name : '-',
                'conversion_factor' => $variant->productUnit ? $variant->productUnit->conversion_factor : null,
                'conversion_factor_cash' => $variant->productUnit ? $variant->productUnit->conversion_factor_cash : null,
                'price' => $variant->price,
                'qty' => $variant->qty,
                'status' => $variant->status,
            ];
        });
        return response()->json(['success' => true, 'data' => $variants]);
    }
}
