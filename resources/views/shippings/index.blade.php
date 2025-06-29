@extends('layout.layout')
@php
    $title = 'Daftar Pengiriman';
    $subTitle = 'Tabel Pengiriman';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        @if(!auth()->user()->hasGlobalAccess())
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-2">Cari Pengiriman</h4>
                    <p class="mb-3 text-muted">Masukkan nomor pengiriman untuk mencari data pengiriman.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('shippings.search') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="number_shipping" class="form-control form-control-lg" placeholder="Masukkan Nomor Pengiriman..." required>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-lg w-100">Cari Pengiriman</button>
                            </div>
                        </div>
                    </form>
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                </div>
            </div>
        @endif

        @if(auth()->user()->hasGlobalAccess())
        <div class="card">
            <div class="card-header">
                <h4 class="mb-2">Pengiriman</h4>
                <p class="mb-3 text-muted">Lihat dan kelola data pengiriman barang di sini.</p>
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
                                @if(auth()->user()->hasGlobalAccess())
                                    <th>Nama Toko</th>
                                @endif
                                <th>Kode Pengiriman</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>User</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shippings as $shipping)
                            <tr>
                                <td>{{ ($shippings->currentPage() - 1) * $shippings->perPage() + $loop->iteration }}</td>
                                @if(auth()->user()->hasGlobalAccess())
                                    <td>{{ $shipping->store->name ?? '-' }}</td>
                                @endif
                                <td>{{ $shipping->number_shipping }}</td>
                                <td>{{ $shipping->shipping_date }}</td>
                                <td>Rp {{ number_format($shipping->total, 0, ',', '.') }}</td>
                                <td>
                                    @if($shipping->status == 'drafted')
                                        <span class="badge bg-warning">Draft</span>
                                    @elseif($shipping->status == 'shipped')
                                        <span class="badge bg-info">Dikirim</span>
                                    @else
                                        <span class="badge bg-success">Selesai</span>
                                    @endif
                                </td>
                                <td>{{ $shipping->user->name }}</td>
                                <td class="text-center">
                                    <a href="{{ route('shippings.edit', $shipping->id) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Ubah">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                    <a href="{{ route('shippings.surat-jalan', $shipping->id) }}" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Surat Jalan" target="_blank">
                                        <iconify-icon icon="mdi:file-pdf-box"></iconify-icon>
                                    </a>
                                    <form action="{{ route('shippings.destroy', $shipping->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" onclick="return confirm('Hapus pengiriman ini?')" title="Hapus">
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
                    @if ($shippings->hasPages())
                        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $shippings->previousPageUrl() ?? 'javascript:void(0)' }}" @if($shippings->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                                    <iconify-icon icon="ep:d-arrow-left" class="text-xl"></iconify-icon>
                                </a>
                            </li>
                            @foreach ($shippings->getUrlRange(1, $shippings->lastPage()) as $page => $url)
                                <li class="page-item">
                                    <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px{{ $page == $shippings->currentPage() ? ' bg-primary-600 text-white' : '' }}"
                                       href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $shippings->nextPageUrl() ?? 'javascript:void(0)' }}" @if(!$shippings->hasMorePages()) tabindex="-1" aria-disabled="true" @endif>
                                    <iconify-icon icon="ep:d-arrow-right" class="text-xl"></iconify-icon>
                                </a>
                            </li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
