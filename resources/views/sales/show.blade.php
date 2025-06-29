@extends('layout.layout')
@php
    $title = 'Detail Penjualan';
    $subTitle = 'Informasi Penjualan';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Detail Penjualan</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Toko</label>
                            <input type="text" class="form-control" value="{{ $sale->store->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="text" class="form-control" value="{{ $sale->sale_date->format('d/m/Y') }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Pelanggan</label>
                            <input type="text" class="form-control" value="{{ $sale->customer->name ?? '-' }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" value="{{ $sale->customer->phone ?? '-' }}" readonly>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive mb-4">
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
                                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->discount, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total:</td>
                                <td>Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Diskon:</td>
                                <td>Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total Akhir:</td>
                                <td>Rp {{ number_format($sale->total - $sale->discount, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Dibayar:</td>
                                <td>Rp {{ number_format($sale->paid, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Kembalian:</td>
                                <td>Rp {{ number_format($sale->paid - ($sale->total - $sale->discount), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <input type="text" class="form-control" value="{{ ucfirst($sale->payment_method) }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Kasir</label>
                            <input type="text" class="form-control" value="{{ $sale->user->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" rows="2" readonly>{{ $sale->note }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">Kembali</a>
                    <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-primary">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
