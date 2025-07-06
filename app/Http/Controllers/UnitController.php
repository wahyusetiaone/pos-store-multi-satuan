<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::orderByDesc('id')->paginate(15);
        return view('units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:units,code',
            'description' => 'nullable|string',
        ]);
        Unit::create($validated);
        return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        return view('units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:units,code,' . $unit->id,
            'description' => 'nullable|string',
        ]);
        $unit->update($validated);
        return redirect()->route('units.index')->with('success', 'Satuan berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus.');
    }

    /**
     * API: Get product units by product_id
     */
    public function productUnits(Request $request)
    {
        $productId = $request->query('product_id');
        if (!$productId) {
            return response()->json([]);
        }
        $units = \App\Models\ProductUnit::with('unit')
            ->where('product_id', $productId)
            ->get()
            ->map(function($pu) {
                return [
                    'id' => $pu->id,
                    'unit_id' => $pu->unit_id,
                    'unit_name' => $pu->unit ? $pu->unit->name : '',
                    'conversion_factor' => $pu->conversion_factor,
                    'conversion_factor_cash' => $pu->conversion_factor_cash
                ];
            });
        return response()->json($units);
    }
}
