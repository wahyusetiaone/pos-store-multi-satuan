@extends('layout.layout')
@php
    $title = 'Detail Transaksi Keuangan';
    $subTitle = 'Informasi Keuangan';
@endphp

@section('content')
<div class="container-fluid">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Transaksi Keuangan</h5>
                    <span class="text-muted">Informasi lengkap transaksi keuangan</span>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:calendar"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $finance->date }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:swap-horizontal"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ ucfirst($finance->type) }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:label"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $finance->category }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jumlah</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:cash"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="Rp {{ number_format($finance->amount, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" rows="2" readonly>{{ $finance->description }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">User</label>
                            <input type="text" class="form-control" value="{{ $finance->user->name ?? '-' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

