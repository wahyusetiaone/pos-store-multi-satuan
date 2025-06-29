@extends('layout.layout')

@php
    $title = 'Ubah Produk';
    $subTitle = 'Edit Data Produk';
    $script = '
        <script src="' . asset('assets/js/pages/product/product-units.js') . '"></script>
    ';
@endphp

@section('content')
<div class="container-fluid">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ubah Produk</h5>
                    <span class="text-muted">Edit data produk</span>
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
                    <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Produk</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="mdi:package-variant"></iconify-icon>
                                    </span>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" placeholder="Masukkan nama produk">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode SKU</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="mdi:barcode"></iconify-icon>
                                    </span>
                                    <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" placeholder="Masukkan kode SKU">
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="mdi:shape-outline"></iconify-icon>
                                    </span>
                                    <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                        <option value="">Pilih kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Harga</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="mdi:cash"></iconify-icon>
                                    </span>
                                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $product->price) }}" placeholder="Masukkan harga">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Stok</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="mdi:warehouse"></iconify-icon>
                                    </span>
                                    <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', $product->stock) }}" placeholder="Masukkan jumlah stok">
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="mdi:toggle-switch"></iconify-icon>
                                    </span>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="1" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Satuan Default</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="mdi:ruler"></iconify-icon>
                                    </span>
                                    <select name="default_unit_id" class="form-control @error('default_unit_id') is-invalid @enderror">
                                        <option value="">Pilih satuan</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('default_unit_id', $product->default_unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('default_unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2" placeholder="Deskripsi produk">{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Gambar Produk</label>
                                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Satuan Produk & Konversi</label>
                            <div id="product-units-list">
                                @php $unitIndex = 0; @endphp
                                @forelse(old('product_units', $product->units->map(function($unit) { return [
                                    'unit_id' => $unit->id,
                                    'conversion_factor' => $unit->pivot->conversion_factor
                                ]; })->toArray()) as $unitRow)
                                <div class="row align-items-end mb-2 product-unit-row">
                                    <div class="col-md-5">
                                        <label class="form-label">Satuan</label>
                                        <select name="product_units[{{ $unitIndex }}][unit_id]" class="form-select" required>
                                            <option value="">Pilih Satuan...</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ (isset($unitRow['unit_id']) && $unitRow['unit_id'] == $unit->id) ? 'selected' : '' }}>{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Faktor Konversi</label>
                                        <input type="number" step="0.0001" min="0.0001" name="product_units[{{ $unitIndex }}][conversion_factor]" class="form-control" value="{{ isset($unitRow['conversion_factor']) ? (fmod($unitRow['conversion_factor'], 1) == 0 ? number_format($unitRow['conversion_factor'], 0, '', '') : rtrim(rtrim(number_format($unitRow['conversion_factor'], 4, '.', ''), '0'), '.')) : '' }}" placeholder="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn {{ $unitIndex == 0 ? 'btn-success add-unit-row' : 'btn-danger remove-unit-row' }} w-100">{{ $unitIndex == 0 ? '+' : '-' }}</button>
                                    </div>
                                </div>
                                @php $unitIndex++; @endphp
                                @empty
                                <div class="row align-items-end mb-2 product-unit-row">
                                    <div class="col-md-5">
                                        <label class="form-label">Satuan</label>
                                        <select name="product_units[0][unit_id]" class="form-select" required>
                                            <option value="">Pilih Satuan...</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Faktor Konversi</label>
                                        <input type="number" step="0.0001" min="0.0001" name="product_units[0][conversion_factor]" class="form-control" placeholder="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-success add-unit-row w-100">+</button>
                                    </div>
                                </div>
                                @endforelse
                            </div>
                            <small class="text-muted">Tambahkan satuan lain beserta faktor konversinya (misal: 1 Dus = 12 Pcs, dst).</small>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
