@extends('layout.layout')
@php
    $title = 'Edit Variant Produk';
    $subTitle = 'Edit Data Variant Produk';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Variant Produk</h5>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('product-variants.update', $productVariant->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="product_id" value="{{ $productVariant->product_id }}">
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <select class="form-select" disabled>
                            <option value="">Pilih Produk...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id', $productVariant->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan Produk</label>
                        <select name="product_unit_id" class="form-select @error('product_unit_id') is-invalid @enderror" required>
                            <option value="">Pilih Satuan Produk...</option>
                            @foreach($productUnits as $unit)
                                <option value="{{ $unit->id }}" {{ old('product_unit_id', $productVariant->product_unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->product->name ?? '-' }} [{{ $unit->product->defaultUnit->name ?? '-' }}] - {{ $unit->unit->name ?? '-' }} (Konversi: {{ $unit->conversion_factor_cash }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Variant</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $productVariant->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $productVariant->price) }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Qty</label>
                        <input type="number" name="qty" class="form-control @error('qty') is-invalid @enderror" value="{{ old('qty', $productVariant->qty) }}" required>
                        @error('qty')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', $productVariant->status) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('status', $productVariant->status) == 0 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="text-end">
                        <a href="{{ route('product-variants.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

