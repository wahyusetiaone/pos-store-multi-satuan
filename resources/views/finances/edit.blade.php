@extends('layout.layout')
@php
    $title = 'Ubah Transaksi Keuangan';
    $subTitle = 'Edit Data Keuangan';
@endphp

@section('content')
<div class="container-fluid">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ubah Transaksi Keuangan</h5>
                    <span class="text-muted">Edit data transaksi keuangan</span>
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
                    <form method="POST" action="{{ route('finances.update', $finance->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="mdi:calendar"></iconify-icon>
                                    </span>
                                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $finance->date) }}">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="mdi:swap-horizontal"></iconify-icon>
                                    </span>
                                    <select name="type" class="form-control @error('type') is-invalid @enderror">
                                        <option value="">Pilih jenis</option>
                                        <option value="income" {{ old('type', $finance->type) == 'income' ? 'selected' : '' }}>Pemasukan</option>
                                        <option value="expense" {{ old('type', $finance->type) == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kategori</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="mdi:label"></iconify-icon>
                                    </span>
                                    <input type="text" name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category', $finance->category) }}" placeholder="Masukkan kategori">
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jumlah</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="mdi:cash"></iconify-icon>
                                    </span>
                                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $finance->amount) }}" placeholder="Masukkan jumlah">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="2" placeholder="Deskripsi transaksi">{{ old('description', $finance->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

