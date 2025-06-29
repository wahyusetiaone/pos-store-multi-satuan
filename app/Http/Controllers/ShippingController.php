<?php

namespace App\Http\Controllers;

use App\Models\Shipping;
use App\Models\ShippingItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\Purchase;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShippingController extends Controller
{
    public function index()
    {
        $query = Shipping::with(['user', 'store']);

        if (!auth()->user()->hasGlobalAccess()) {
            $query->where('store_id', auth()->user()->current_store_id);
        }

        $shippings = $query->orderByDesc('id')->paginate(15);
        return view('shippings.index', compact('shippings'));
    }

    public function create()
    {
        $stores = [];
        if (auth()->user()->hasGlobalAccess()) {
            $stores = Store::where('is_active', true)->get();
        }

        $products = Product::query();
        if (!auth()->user()->hasGlobalAccess()) {
            $products->where('store_id', auth()->user()->current_store_id);
        }
        $products = $products->get();

        return view('shippings.create', compact('stores', 'products'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate request
            $validated = $request->validate([
                'purchase_id' => 'required|exists:purchases,id',
                'number_shipping' => 'required|string|unique:shippings',
                'shipping_date' => 'required|date',
                'status' => 'required|in:drafted,shipped,completed',
                'store_id' => auth()->user()->hasGlobalAccess() ? 'required|exists:stores,id' : 'nullable',
                'total' => 'required|numeric|min:0',
                'note' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.buy_price' => 'required|numeric|min:0',  // Tambahkan validasi buy_price
            ]);

            // Get purchase data
            $purchase = Purchase::findOrFail($request->purchase_id);

            // Create shipping
            $shipping = new Shipping();
            $shipping->store_id = $purchase->store_id;
            $shipping->user_id = auth()->id();
            $shipping->number_shipping = $validated['number_shipping'];
            $shipping->shipping_date = $validated['shipping_date'];
            $shipping->supplier = $purchase->supplier;
            $shipping->status = $validated['status'];
            $shipping->total = $validated['total'];
            $shipping->note = $validated['note'] ?? null;
            $shipping->save();

            // Create shipping items
            foreach ($request->items as $item) {
                if (!isset($item['selected'])) continue; // Skip unselected items

                ShippingItem::create([
                    'shipping_id' => $shipping->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'buy_price' => $item['buy_price'],  // Tambahkan buy_price
                    'subtotal' => $item['quantity'] * $item['price']
                ]);
            }

            // Update purchase status to shipped
            $purchase->update([
                'status' => 'shipped',
                'ship_date' => $validated['shipping_date']
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengiriman berhasil dibuat',
                'data' => $shipping->load('items.product')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Shipping $shipping)
    {
        $shipping->load(['user', 'items.product']);
        return view('shippings.show', compact('shipping'));
    }

    public function edit(Shipping $shipping)
    {
        $stores = [];
        if (auth()->user()->hasGlobalAccess()) {
            $stores = Store::where('is_active', true)->get();
        }
        return view('shippings.edit', compact('shipping', 'stores'));
    }

    public function update(Request $request, Shipping $shipping)
    {
        $validated = $request->validate([
            'shipping_date' => 'required|date',
            'supplier' => 'required|string|max:255',
            'total' => 'required|numeric',
            'note' => 'nullable|string',
            'store_id' => auth()->user()->hasGlobalAccess() ? 'required|exists:stores,id' : 'prohibited'
        ]);

        $shipping->update($validated);
        return redirect()->route('shippings.index')->with('success', 'Pengiriman berhasil diubah.');
    }

    public function destroy(Shipping $shipping)
    {
        $shipping->delete();
        return redirect()->route('shippings.index')->with('success', 'Pengiriman berhasil dihapus.');
    }

    public function updateStatus(Request $request, Shipping $shipping)
    {
        $validated = $request->validate([
            'status' => 'required|in:drafted,shipped,completed',
            'ship_date' => 'required_if:status,shipped|nullable|date'
        ]);

        $shipping->update([
            'status' => $request->status,
            'ship_date' => $request->ship_date
        ]);

        // Jika status completed, update stok produk
        if ($request->status === 'completed') {
            foreach ($shipping->items as $item) {
                $product = $item->product;
                $product->update([
                    'stock' => $product->stock + $item->quantity
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pengiriman berhasil diperbarui'
        ]);
    }

    public function accepter(Request $request, Shipping $shipping)
    {
        try {
            DB::beginTransaction();

            // Validate request
            $request->validate([
                'items' => 'required|array',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.qty_received' => 'required|integer|min:0',
                'items.*.note' => 'nullable|string',
            ]);

            // Update shipping status to completed
            $shipping->update([
                'status' => 'completed',
                'ship_date' => now()
            ]);

            // Update items and product stock
            foreach ($request->items as $index => $itemData) {
                $item = $shipping->items()->where('product_id', $itemData['product_id'])->first();

                if ($item) {
                    // Update shipping item
                    $item->update([
                        'qty_received' => $itemData['qty_received'],
                        'note' => $itemData['note'] ?? null
                    ]);

                    // Update product stock
                    $product = Product::where('id', $itemData['product_id'])
                        ->where('store_id', $shipping->store_id)
                        ->first();

                    if ($product) {
                        $product->update([
                            'stock' => $product->stock + $itemData['qty_received']
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengiriman berhasil diterima'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $request->validate([
            'number_shipping' => 'required|string'
        ]);

        $shipping = Shipping::where('number_shipping', $request->number_shipping)
            ->where('store_id', auth()->user()->current_store_id)
            ->first();

        if (!$shipping) {
            return redirect()->route('shippings.index')
                ->with('error', 'Nomor pengiriman tidak ditemukan.');
        }

        return redirect()->route('shippings.edit', $shipping->id);
    }

    public function suratJalan(Shipping $shipping)
    {
        $shipping->load(['user', 'items.product', 'store']);
        $pdf = Pdf::loadView('shippings.surat-jalan', compact('shipping'))
            ->setPaper('a5', 'landscape');
        return $pdf->stream('Surat-Jalan-' . $shipping->number_shipping . '.pdf');
    }

    /**
     * Generate barcode PDF for shipping item
     */
    public function generateBarcodePdf(Shipping $shipping, ShippingItem $item)
    {
        $pdf = PDF::loadView('shippings.barcode-pdf', [
            'shipping' => $shipping,
            'item' => $item,
            'product' => $item->product,
        ]);

        $filename = 'barcode-' . Str::slug($item->product->name) . '-' . $shipping->number_shipping . '.pdf';
        return $pdf->download($filename);
    }
}
