@extends('layout.layout')
@php
    $title = 'Detail Variant Produk';
    $subTitle = 'Informasi Variant Produk';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Detail Variant Produk</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Produk</label>
                        <input type="text" class="form-control" value="{{ $productVariant->product->name ?? '-' }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Satuan Produk</label>
                        <input type="text" class="form-control" value="{{ $productVariant->productUnit->unit->name ?? '-' }} (Konversi: {{ $productVariant->productUnit->conversion_factor_cash ?? '-' }})" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Variant</label>
                        <input type="text" class="form-control" value="{{ $productVariant->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Harga Jual</label>
                        <input type="text" class="form-control" value="Rp {{ number_format($productVariant->price, 0, ',', '.') }}" readonly>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Qty</label>
                        <input type="text" class="form-control" value="{{ $productVariant->qty }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <input type="text" class="form-control" value="{{ $productVariant->status ? 'Aktif' : 'Nonaktif' }}" readonly>
                    </div>
                </div>
                <div class="text-end">
                    <a href="{{ route('product-variants.edit', $productVariant->id) }}" class="btn btn-success">Edit</a>
                    <a href="{{ route('product-variants.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

