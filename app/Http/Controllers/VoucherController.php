<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Store;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::with('store')->orderByDesc('id')->paginate(15);
        return view('vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        return view('vouchers.create', compact('stores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'nullable|exists:stores,id',
            'code' => 'required|string|unique:vouchers,code',
            'discount_amount' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);
        $validated['times_used'] = 0;
        Voucher::create($validated);
        return redirect()->route('vouchers.index')->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function edit(Voucher $voucher)
    {
        $stores = Store::where('is_active', true)->get();
        return view('vouchers.edit', compact('voucher', 'stores'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'store_id' => 'nullable|exists:stores,id',
            'code' => 'required|string|unique:vouchers,code,' . $voucher->id,
            'discount_amount' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);
        $voucher->update($validated);
        return redirect()->route('vouchers.index')->with('success', 'Voucher berhasil diubah.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('vouchers.index')->with('success', 'Voucher berhasil dihapus.');
    }

    public function check(Request $request)
    {
        $code = $request->query('code');

        $voucher = Voucher::where('code', $code)
            ->where('store_id', auth()->user()->current_store_id)
            ->first();

        if (!$voucher) {
            return response()->json([
                'valid' => false,
                'message' => 'Voucher tidak ditemukan'
            ]);
        }

        if (!$voucher->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Voucher sudah tidak berlaku'
            ]);
        }

        return response()->json([
            'valid' => true,
            'discount_amount' => $voucher->discount_amount,
            'message' => 'Voucher valid'
        ]);
    }
}

