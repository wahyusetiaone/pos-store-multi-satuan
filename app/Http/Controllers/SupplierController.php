<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Store;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();
        if (!auth()->user()->hasGlobalAccess()) {
            $query->where('store_id', auth()->user()->current_store_id);
        }
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $suppliers = $query->orderByDesc('id')->paginate(15);
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $stores = auth()->user()->hasGlobalAccess() ? Store::where('is_active', true)->get() : [];
        return view('suppliers.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'store_id' => auth()->user()->hasGlobalAccess() ? 'required|exists:stores,id' : 'prohibited',
        ]);
        $data = $validated;
        if (!auth()->user()->hasGlobalAccess()) {
            $data['store_id'] = auth()->user()->current_store_id;
        }
        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        $stores = auth()->user()->hasGlobalAccess() ? Store::where('is_active', true)->get() : [];
        return view('suppliers.edit', compact('supplier', 'stores'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'store_id' => auth()->user()->hasGlobalAccess() ? 'required|exists:stores,id' : 'prohibited',
        ]);
        $data = $validated;
        if (!auth()->user()->hasGlobalAccess()) {
            $data['store_id'] = auth()->user()->current_store_id;
        }
        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diubah.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }

    // API: Get suppliers by store_id
    public function apiByStore(Request $request)
    {
        $storeId = $request->get('store_id');
        if (!$storeId) {
            return response()->json([]);
        }
        $suppliers = Supplier::where('store_id', $storeId)->orderBy('name')->get(['id', 'name']);
        return response()->json($suppliers);
    }
}
