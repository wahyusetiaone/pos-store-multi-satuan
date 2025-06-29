<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\User;
use App\Models\SaleItem;
use App\Models\Store;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Exports\SaleExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'user', 'store']);

        // Filter by store
        if (auth()->user()->hasGlobalAccess()) {
            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }
            // Get stores for filter dropdown
            $stores = Store::where('is_active', true)->get();
        } else {
            $query->where('store_id', auth()->user()->current_store_id);
            $stores = collect(); // empty collection for non-global users
        }

        // Apply date filters
        switch ($request->filter) {
            case 'today':
                $query->whereDate('sale_date', now());
                break;
            case 'month':
                $query->whereYear('sale_date', now()->year)
                      ->whereMonth('sale_date', now()->month);
                break;
            case 'year':
                $query->whereYear('sale_date', now()->year);
                break;
            default:
                if ($request->filled(['start_date', 'end_date'])) {
                    $query->whereBetween('sale_date', [
                        $request->start_date . ' 00:00:00',
                        $request->end_date . ' 23:59:59'
                    ]);
                }
                break;
        }

        $sales = $query->orderByDesc('id')->paginate(15)->withQueryString();
        return view('sales.index', compact('sales', 'stores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = [];
        $stores = [];
        if (auth()->user()->hasGlobalAccess()) {
            $stores = Store::where('is_active', true)->get();
        }

        $customers = Customer::query();
        if (!auth()->user()->hasGlobalAccess()) {
            $customers->where('store_id', auth()->user()->current_store_id);
        }
        $customers = $customers->get();

        return view('sales.create', compact('stores', 'customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Create or get customer
            $customer = null;
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
            } elseif ($request->customer_name) {
                // Create new customer if doesn't exist
                $customer = Customer::create([
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'store_id' => auth()->user()->hasGlobalAccess() ? $request->store_id : auth()->user()->current_store_id
                ]);
            }

            // Set store_id based on user access
            if (auth()->user()->hasGlobalAccess()) {
                $storeId = $request->store_id;
                if (!$storeId) {
                    throw new \Exception('Store ID is required for global access users');
                }
            } else {
                $storeId = auth()->user()->current_store_id;
            }

            // Create sale
            $sale = Sale::create([
                'store_id' => $storeId,
                'customer_id' => $customer ? $customer->id : null,
                'user_id' => auth()->id(),
                'sale_date' => now(), // Using Carbon now() instance
                'total' => $request->total,
                'grand_total' => $request->grand_total,
                'discount' => $request->discount ?? 0,
                'order_type' => $request->order_type,
                'paid' => $request->paid,
                'payment_method' => $request->payment_method,
                'note' => $request->note,
            ]);

            // Create sale items
            foreach ($request->items as $item) {
                // Ambil variant
                $variant = \App\Models\ProductVariant::with('productUnit')->find($item['id']);
                if (!$variant) {
                    throw new \Exception('Variant tidak ditemukan');
                }
                $conversionFactor = $variant->productUnit ? $variant->productUnit->conversion_factor : 1;
                $quantityConversion = $variant->qty * $conversionFactor;
                $grandQuantityConversion = $item['quantity'] * $conversionFactor;
                // Simpan sale item
                $saleItem = \App\Models\SaleItem::create([
                    'sale_id' => $sale->id,
                    'variant_id' => $variant->id,
                    'product_id' => $variant->product_id,
                    'quantity' => $item['quantity'],
                    'quantity_conversion' => $grandQuantityConversion,
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => ($item['quantity'] * $item['price']) - ($item['discount'] ?? 0),
                ]);
                // Update stock produk utama
                $product = \App\Models\Product::find($variant->product_id);
                if ($product) {
                    $product->decrement('stock', $grandQuantityConversion);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan',
                'data' => $sale->load('items', 'customer')
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'items']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $customers = Customer::all();
        $users = User::all();
        $stores = [];
        if (auth()->user()->hasGlobalAccess()) {
            $stores = Store::where('is_active', true)->get();
        }

        return view('sales.edit', compact('stores','sale', 'customers', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'user_id' => 'required|exists:users,id',
            'sale_date' => 'required|date',
            'total' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'order_type' => 'required|string',
            'paid' => 'required|numeric',
            'payment_method' => 'required|string',
            'note' => 'nullable|string',
        ]);
        $sale->update($validated);
        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('sales.index')->with('success', 'Penjualan berhasil dihapus.');
    }

    /**
     * Export sales to Excel
     */
    public function export(Request $request)
    {
        $query = Sale::with(['customer', 'user', 'store', 'items.product']);

        // Filter by store
        if (auth()->user()->hasGlobalAccess()) {
            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
                $storeName = Store::find($request->store_id)->name;
                $storePrefix = Str::slug($storeName) . '_';
            } else {
                $storePrefix = 'semua_toko_';
            }
        } else {
            $query->where('store_id', auth()->user()->current_store_id);
            $storePrefix = '';
        }

        // Apply date filters
        switch ($request->filter) {
            case 'today':
                $query->whereDate('sale_date', now());
                $filename = $storePrefix . 'penjualan_hari_ini_' . now()->format('Y-m-d');
                break;
            case 'month':
                $query->whereYear('sale_date', now()->year)
                      ->whereMonth('sale_date', now()->month);
                $filename = $storePrefix . 'penjualan_bulan_' . now()->format('Y-m');
                break;
            case 'year':
                $query->whereYear('sale_date', now()->year);
                $filename = $storePrefix . 'penjualan_tahun_' . now()->format('Y');
                break;
            default:
                if ($request->filled(['start_date', 'end_date'])) {
                    $query->whereBetween('sale_date', [
                        $request->start_date . ' 00:00:00',
                        $request->end_date . ' 23:59:59'
                    ]);
                    $filename = $storePrefix . 'penjualan_' . $request->start_date . '_' . $request->end_date;
                } else {
                    $filename = $storePrefix . 'semua_penjualan';
                }
                break;
        }

        $sales = $query->orderByDesc('sale_date')->get();

        return Excel::download(
            new SaleExport($sales),
            $filename . '.xlsx'
        );
    }
}
