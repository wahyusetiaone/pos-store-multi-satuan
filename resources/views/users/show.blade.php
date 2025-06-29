@extends('layout.layout')
@php
    $title = 'Detail Pengguna';
    $subTitle = 'Informasi Pengguna';
@endphp

@section('content')
<div class="container-fluid">
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Pengguna</h5>
                    <span class="text-muted">Informasi lengkap pengguna</span>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="f7:person"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mage:email"></iconify-icon>
                                </span>
                                <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:account-badge"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $user->roles->pluck('name')->join(', ') }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

