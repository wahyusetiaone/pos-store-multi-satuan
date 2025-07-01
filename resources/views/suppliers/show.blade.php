@extends('layout.layout')
@php
    $title = 'Detail Supplier';
    $subTitle = 'Detail Data Supplier';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Detail Supplier</h5>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    @if(auth()->user()->hasGlobalAccess())
                        <dt class="col-sm-4">Toko</dt>
                        <dd class="col-sm-8">{{ $supplier->store->name ?? '-' }}</dd>
                    @endif
                    <dt class="col-sm-4">Nama</dt>
                    <dd class="col-sm-8">{{ $supplier->name }}</dd>
                    <dt class="col-sm-4">Telepon</dt>
                    <dd class="col-sm-8">{{ $supplier->phone }}</dd>
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $supplier->email }}</dd>
                    <dt class="col-sm-4">Alamat</dt>
                    <dd class="col-sm-8">{{ $supplier->address }}</dd>
                    <dt class="col-sm-4">Catatan</dt>
                    <dd class="col-sm-8">{{ $supplier->note }}</dd>
                </dl>
                <div class="text-end mt-4">
                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-success">Edit</a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

