@extends('layout.layout')
@php
    $title = 'Detail Pembelian';
    $subTitle = 'Detail Data Pembelian';
@endphp

@section('content')
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Pembelian</h5>
                </div>
                <div class="card-body">
                    <form>
                        @if(auth()->user()->hasGlobalAccess())
                            <div class="mb-3">
                                <label class="form-label">Toko</label>
                                <input type="text" class="form-control" value="{{ $purchase->store->name }}" readonly>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Pembelian</label>
                                    <input type="date" class="form-control" value="{{ $purchase->purchase_date->format('Y-m-d') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Supplier</label>
                                    <input type="text" class="form-control" value="{{ $purchase->supplier->name }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <input type="text" class="form-control" value="{{ ucfirst($purchase->status) }}" readonly>
                                </div>
                            </div>
                        </div>
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
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($purchase->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->productUnit->unit->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->ppn }}</td>
                                        <td>{{ number_format($item->buy_price, 0, ',', '.') }}</td>
                                        <td>{{ number_format($item->price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Total:</td>
                                    <td>{{ number_format($purchase->total, 0, ',', '.') }}</td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" rows="2" readonly>{{ $purchase->note }}</textarea>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

