@extends('layout.layout')
@php
    $title = 'Edit Pembelian';
    $subTitle = 'Form Edit Pembelian';
    $script = '<script src="' . asset('assets/js/pages/purchase/edit.js') . '"></script>';
@endphp

@section('content')
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Pembelian</h5>
                </div>
                <div class="card-body">
                    <form id="purchaseForm" action="{{ route('purchases.update', $purchase->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @if(auth()->user()->hasGlobalAccess())
                            <div class="mb-3">
                                <label class="form-label">Pilih Toko</label>
                                <select name="store_id" id="store_id" class="form-select @error('store_id') is-invalid @enderror" required>
                                    <option value="">Pilih Toko...</option>
                                    @foreach(App\Models\Store::where('is_active', true)->get() as $store)
                                        <option value="{{ $store->id }}" {{ $purchase->store_id == $store->id ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('store_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Pembelian</label>
                                    <input type="date" name="purchase_date" class="form-control" value="{{ $purchase->purchase_date->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Supplier</label>
                                    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                        <option value="">Pilih Supplier...</option>
                                        @foreach(App\Models\Supplier::where('store_id', $purchase->store_id)->get() as $supplier)
                                            <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="drafted" {{ $purchase->status == 'drafted' ? 'selected' : '' }}>Draft</option>
                                        <option value="shipped" {{ $purchase->status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                        <option value="completed" {{ $purchase->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Product Selection -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="fas fa-plus"></i> Tambah Produk
                            </button>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Satuan</th>
                                    <th width="80">Jumlah</th>
                                    <th width="80">PPN (%)</th>
                                    <th width="120">Harga Beli</th>
                                    <th width="120">Harga Jual</th>
                                    <th width="50">Aksi</th>
                                </tr>
                                </thead>
                                <tbody id="items_table">
                                @foreach($purchase->items as $item)
                                    <tr>
                                        <td>
                                            {{ $item->product->name }}
                                            <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $item->product_id }}">
                                        </td>
                                        <td>
                                            {{ $item->productUnit->unit->name }}
                                            <input type="hidden" name="items[{{ $loop->index }}][product_unit_id]" value="{{ $item->product_unit_id }}">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control" value="{{ $item->quantity }}" min="1">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $loop->index }}][ppn]" class="form-control" value="{{ $item->ppn }}" min="0">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $loop->index }}][buy_price]" class="form-control" value="{{ $item->buy_price }}" min="0">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $loop->index }}][price]" class="form-control" value="{{ $item->price }}" min="0">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                                                <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Total:</td>
                                    <td colspan="2">
                                        <input type="number" name="total" id="total_amount" class="form-control" value="{{ $purchase->total }}" readonly>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                            <small class="form-text text-muted">* Harga jual adalah harga jual per satuan dasar, dan tidak mempengaruhi harga penjualan serta dapat diubah pada halaman management produk.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="note" class="form-control" rows="2">{{ $purchase->note }}</textarea>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Produk -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Tambah Produk ke Daftar Pembelian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <select id="product_select" class="form-select">
                            <option value="">Pilih Produk...</option>
                            @foreach(App\Models\Product::where('store_id', $purchase->store_id)->get() as $product)
                                <option value="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}">
                                    {{ $product->name }} (Stok: {{ $product->stock }} {{ $product->defaultUnit->name }})
                                </option>
                            @endforeach
                            <option value="new" style="background-color: #e9ecef; font-weight: bold;">+ Tambah Produk Baru</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan Pembelian</label>
                        <select id="unit_select" class="form-select" disabled>
                            <option value="">Pilih Satuan...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Pembelian</label>
                        <input type="number" id="qty_input" class="form-control" placeholder="Jumlah" min="1" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PPN (%)</label>
                        <input type="number" id="ppn_input" class="form-control" placeholder="PPN %" min="0" max="100" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Beli</label>
                        <small class="form-text text-muted"> sudah termasuk PPN</small>
                        <input type="number" id="buy_price_input" class="form-control" placeholder="Harga Beli" min="0" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Jual</label>
                        <small class="form-text text-muted"> dalam satuan dasar</small>
                        <input type="number" id="price_input" class="form-control" placeholder="Harga Jual" min="0" disabled readonly>
                    </div>

                    <div class="mb-4">
                        <button type="button" class="btn btn-primary w-100" id="add_item" disabled data-bs-dismiss="modal">Tambah</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create Product -->
    <div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProductModalLabel">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createProductForm">
                        @csrf
                        <input type="hidden" name="store_id" id="modal_store_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">SKU</label>
                                    <input type="text" name="sku" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kategori</label>
                                    <select name="category_id" id="modal_category_select" class="form-select" required>
                                        <option value="">Pilih Kategori...</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="1">Aktif</option>
                                        <option value="0">Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Harga</label>
                                    <input type="number" name="price" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Stok</label>
                                    <input type="number" name="stock" class="form-control" value="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gambar Produk</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveProductBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection
