@extends('layout.layout')
@php
    $title = 'Detail Toko';
    $subTitle = 'Informasi Toko';
@endphp

@section('content')
<div class="container-fluid">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Toko</h5>
                    <span class="text-muted">Informasi lengkap toko</span>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        @if($store->logo)
                            <div class="col-md-12 text-center mb-4">
                                <img src="{{ asset('storage/' . $store->logo) }}" alt="Logo toko" class="img-thumbnail" style="max-height: 200px">
                            </div>
                        @endif
                        <div class="col-md-6">
                            <label class="form-label">Nama Toko</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:store"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $store->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:email"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $store->email }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:phone"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $store->phone }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="{{ $store->is_active ? 'Aktif' : 'Nonaktif' }}" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" rows="2" readonly>{{ $store->address }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" rows="2" readonly>{{ $store->description }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jumlah Produk</label>
                            <input type="text" class="form-control" value="{{ $store->products->count() }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Penjualan</label>
                            <input type="text" class="form-control" value="{{ $store->sales->count() }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Pengguna</label>
                            <input type="text" class="form-control" value="{{ $store->users->count() }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
