@extends('layout.layout')
@php
    $title = 'Tambah Penjualan';
    $subTitle = 'Form Transaksi Penjualan';
    $script = '<script src="' . asset('assets/js/pages/sale/create.js') . '"></script>';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Transaksi Penjualan</h5>
            </div>
            <div class="card-body">
                <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
                    @csrf
                    @if(auth()->user()->hasGlobalAccess())
                        <div class="mb-3">
                            <label class="form-label">Pilih Toko</label>
                            <select name="store_id" id="store_id" class="form-select @error('store_id') is-invalid @enderror" required>
                                <option value="">Pilih Toko...</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pelanggan</label>
                                <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                                    <option value="">Pilih Pelanggan...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-phone="{{ $customer->phone }}">
                                            {{ $customer->name }} - {{ $customer->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Atau Pelanggan Baru</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" name="customer_name" class="form-control" placeholder="Nama Pelanggan">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" name="customer_phone" class="form-control" placeholder="No. Telepon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Selection -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Tambah Produk</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <select id="product_select" class="form-select">
                                        <option value="">Pilih Produk...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                    data-name="{{ $product->name }}"
                                                    data-price="{{ $product->price }}"
                                                    data-stock="{{ $product->stock }}">
                                                {{ $product->name }} (Stok: {{ $product->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" id="qty_input" class="form-control" placeholder="Jumlah" min="1">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" id="price_input" class="form-control" placeholder="Harga" min="0">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" id="discount_input" class="form-control" placeholder="Diskon" min="0" value="0">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary w-100" id="add_item">Tambah</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th width="100">Jumlah</th>
                                    <th width="150">Harga</th>
                                    <th width="150">Diskon</th>
                                    <th width="150">Subtotal</th>
                                    <th width="50">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="items_table">
                                <!-- Items will be added here dynamically -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td colspan="2">
                                        <input type="number" name="total" id="total" class="form-control" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">Diskon:</td>
                                    <td colspan="2">
                                        <input type="number" name="discount" id="discount" class="form-control" value="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total Akhir:</td>
                                    <td colspan="2">
                                        <input type="number" name="final_total" id="final_total" class="form-control" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">Dibayar:</td>
                                    <td colspan="2">
                                        <input type="number" name="paid" id="paid" class="form-control" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">Kembalian:</td>
                                    <td colspan="2">
                                        <input type="number" name="change" id="change" class="form-control" readonly>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="cash">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="card">Kartu Debit/Kredit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea name="note" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
