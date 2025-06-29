@extends('layout.layout')
@php
    $title = 'Edit Penjualan';
    $subTitle = 'Form Edit Penjualan';
    $script = '<script src="' . asset('assets/js/pages/sale/edit.js') . '"></script>';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit Penjualan</h5>
            </div>
            <div class="card-body">
                <form id="saleForm" action="{{ route('sales.update', $sale->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @if(auth()->user()->hasGlobalAccess())
                        <div class="mb-3">
                            <label class="form-label">Toko</label>
                            <select name="store_id_display" id="store_id" class="form-select" disabled>
                                <option value="">Pilih Toko...</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ $sale->store_id == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="store_id" value="{{ $sale->store_id }}">
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" name="sale_date" class="form-control" value="{{ $sale->sale_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Pelanggan</label>
                                <select name="customer_id" class="form-select" required>
                                    <option value="">Pilih Pelanggan...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ $sale->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->phone }}
                                        </option>
                                    @endforeach
                                </select>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->discount, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td>
                                        <input type="number" name="total" class="form-control" value="{{ $sale->total }}" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">Diskon:</td>
                                    <td>
                                        <input type="number" name="discount" class="form-control" value="{{ $sale->discount }}">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end">Dibayar:</td>
                                    <td>
                                        <input type="number" name="paid" class="form-control" value="{{ $sale->paid }}" required>
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
                                    <option value="cash" {{ $sale->payment_method == 'cash' ? 'selected' : '' }}>Tunai</option>
                                    <option value="transfer" {{ $sale->payment_method == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                    <option value="card" {{ $sale->payment_method == 'card' ? 'selected' : '' }}>Kartu Debit/Kredit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Catatan</label>
                                <textarea name="note" class="form-control" rows="2">{{ $sale->note }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
