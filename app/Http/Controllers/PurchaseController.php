<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\User;
use App\Models\PurchaseItem;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Purchase::with(['user', 'store']);

        // Filter by store if user doesn't have global access
        if (!auth()->user()->hasGlobalAccess()) {
            $query->where('store_id', auth()->user()->current_store_id);
        }

        $purchases = $query->orderByDesc('id')->paginate(15);
        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $categories = auth()->user()->hasGlobalAccess() ? [] : auth()->user()->current_store->categories;
        $stores = auth()->user()->hasGlobalAccess() ? Store::where('is_active', true)->get() : [];
        $products = Product::when(!auth()->user()->hasGlobalAccess(), function($query) {
            return $query->where('store_id', auth()->user()->current_store_id);
        })->get();

        // Handle pre-filled data from product detail page
        $preSelectedStore = null;
        $preSelectedProduct = null;

        if ($request->has('store_id') && $request->has('product_id')) {
            $preSelectedStore = Store::find($request->store_id);
            $preSelectedProduct = Product::find($request->product_id);
        }

        return view('purchases.create', compact('stores', 'products', 'categories', 'preSelectedStore', 'preSelectedProduct'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'purchase_date' => 'required|date',
                'supplier' => 'required|string|max:255',
                'total' => 'required|numeric',
                'note' => 'nullable|string',
                'store_id' => auth()->user()->hasGlobalAccess() ? 'required|exists:stores,id' : 'prohibited',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.buy_price' => 'required|numeric|min:0',
            ]);

            // Set store_id based on user access
            if (auth()->user()->hasGlobalAccess()) {
                $storeId = $request->store_id;
            } else {
                $storeId = auth()->user()->current_store_id;
            }

            // Create purchase
            $purchase = Purchase::create([
                'store_id' => $storeId,
                'user_id' => auth()->id(),
                'purchase_date' => $request->purchase_date,
                'supplier' => $request->supplier,
                'total' => $request->total,
                'status' => 'drafted',
                'note' => $request->note
            ]);

            // Create purchase items
            foreach ($request->items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'buy_price' => $item['buy_price'],
                    'subtotal' => $item['quantity'] * $item['buy_price']
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pembelian berhasil disimpan',
                'data' => $purchase->load('items.product')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan pembelian: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        if (request()->wantsJson()) {
            $purchase->load(['items.product']);  // Explicitly load the relationships
            return response()->json($purchase);
        }

        $purchase->load(['user', 'items']);
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        $users = User::all();
        return view('purchases.edit', compact('purchase', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'user_id' => 'exists:users,id',
            'purchase_date' => 'date',
            'supplier' => 'string|max:255',
            'status' => 'string|max:255',
            'shipping_date' => 'date|nullable',
            'total' => 'numeric',
            'note' => 'nullable|string',
        ]);
        $purchase->update($validated);
        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil dihapus.');
    }

    // Add method untuk update status
    public function updateStatus(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'status' => 'required|in:drafted,shipped,completed',
            'ship_date' => 'required_if:status,shipped|nullable|date'
        ]);

        $purchase->update([
            'status' => $request->status,
            'ship_date' => $request->ship_date
        ]);

        // Jika status completed, update stok produk
        if ($request->status === 'completed') {
            foreach ($purchase->items as $item) {
                $product = $item->product;
                $product->update([
                    'stock' => $product->stock + $item->quantity
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pembelian berhasil diperbarui'
        ]);
    }
}
