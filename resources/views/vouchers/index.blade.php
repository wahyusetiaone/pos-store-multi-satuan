@extends('layout.layout')
@php
    $title = 'Voucher';
    $subTitle = 'Daftar Voucher';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <form method="GET" action="{{ route('vouchers.index') }}" class="row g-2 align-items-center" id="filterForm">
                    <div class="col-auto">
                        <input type="text" name="search" class="form-control" placeholder="Cari voucher..." value="{{ request('search') }}">
                    </div>
                    <div class="col-auto ms-auto">
                        <a href="{{ route('vouchers.create') }}" class="btn btn-success">Tambah Voucher</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Toko</th>
                                <th>Nominal Diskon</th>
                                <th>Berlaku Dari</th>
                                <th>Berlaku Sampai</th>
                                <th>Batas Pakai</th>
                                <th>Terpakai</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($vouchers as $voucher)
                            <tr>
                                <td>{{ ($vouchers->currentPage() - 1) * $vouchers->perPage() + $loop->iteration }}</td>
                                <td>{{ $voucher->code }}</td>
                                <td>{{ $voucher->store->name ?? '-' }}</td>
                                <td>{{ number_format($voucher->discount_amount, 0, ',', '.') }}</td>
                                <td>{{ $voucher->valid_from }}</td>
                                <td>{{ $voucher->valid_until ?? '-' }}</td>
                                <td>{{ $voucher->usage_limit ?? '-' }}</td>
                                <td>{{ $voucher->times_used }}</td>
                                <td>
                                    @if($voucher->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('vouchers.edit', $voucher->id) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Ubah">
                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                    </a>
                                    <form action="{{ route('vouchers.destroy', $voucher->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" onclick="return confirm('Hapus voucher ini?')" title="Hapus">
                                            <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    @if ($vouchers->hasPages())
                        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $vouchers->previousPageUrl() ?? 'javascript:void(0)' }}" @if($vouchers->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                                    <iconify-icon icon="ep:d-arrow-left" class="text-xl"></iconify-icon>
                                </a>
                            </li>
                            @foreach ($vouchers->getUrlRange(1, $vouchers->lastPage()) as $page => $url)
                                <li class="page-item">
                                    <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px{{ $page == $vouchers->currentPage() ? ' bg-primary-600 text-white' : '' }}"
                                       href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $vouchers->nextPageUrl() ?? 'javascript:void(0)' }}" @if(!$vouchers->hasMorePages()) tabindex="-1" aria-disabled="true" @endif>
                                    <iconify-icon icon="ep:d-arrow-right" class="text-xl"></iconify-icon>
                                </a>
                            </li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
