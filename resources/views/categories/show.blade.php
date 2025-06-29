@extends('layout.layout')
@php
    $title = 'Detail Kategori';
    $subTitle = 'Informasi Kategori';
@endphp

@section('content')
<div class="container-fluid">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Kategori</h5>
                    <span class="text-muted">Informasi lengkap kategori</span>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Kategori</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:shape-outline"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $category->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" rows="2" readonly>{{ $category->description }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Jumlah Produk</label>
                            <input type="text" class="form-control" value="{{ $category->products->count() }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

