@extends('layout.layout')
@php
    $title = 'Daftar Toko';
    $subTitle = 'Tabel Toko';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('stores.create') }}" class="btn btn-primary float-end">Tambah Toko</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Alamat</th>
                                <th>Telepon</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stores as $store)
                                <tr>
                                    <td>{{ $store->name }}</td>
                                    <td>{{ $store->address }}</td>
                                    <td>{{ $store->phone }}</td>
                                    <td>{{ $store->email }}</td>
                                    <td>
                                        @if($store->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('stores.show', $store->id) }}" class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Lihat">
                                            <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                        </a>
                                        @if(auth()->user()->hasGlobalAccess())
                                            <a href="{{ route('stores.users', $store->id) }}" class="w-32-px h-32-px bg-info-light text-info-600 rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Kelola User">
                                                <iconify-icon icon="mdi:account-multiple"></iconify-icon>
                                            </a>
                                            <a href="{{ route('stores.edit', $store->id) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Ubah">
                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                            </a>
                                            <form action="{{ route('stores.destroy', $store->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" onclick="return confirm('Hapus toko ini?')" title="Hapus">
                                                    <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    @if ($stores->hasPages())
                        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $stores->previousPageUrl() ?? 'javascript:void(0)' }}" @if($stores->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                                    <iconify-icon icon="ep:d-arrow-left" class="text-xl"></iconify-icon>
                                </a>
                            </li>
                            @foreach ($stores->getUrlRange(1, $stores->lastPage()) as $page => $url)
                                <li class="page-item">
                                    <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px{{ $page == $stores->currentPage() ? ' bg-primary-600 text-white' : '' }}"
                                       href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $stores->nextPageUrl() ?? 'javascript:void(0)' }}" @if(!$stores->hasMorePages()) tabindex="-1" aria-disabled="true" @endif>
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
