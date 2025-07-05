@extends('layout.layout')
@php
    $title = 'Daftar Supplier';
    $subTitle = 'Tabel Supplier';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary float-end">Tambah Supplier</a>
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
                                <th>Nama</th>
                                <th>Telepon</th>
                                <th>Email</th>
                                <th>Alamat</th>
                                <th>Catatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $supplier)
                                <tr>
                                    <td>{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}</td>
                                    @if(auth()->user()->hasGlobalAccess())
                                        <td>{{ $supplier->store->name ?? '-' }}</td>
                                    @endif
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>{{ $supplier->email }}</td>
                                    <td>{{ $supplier->address }}</td>
                                    <td>{{ $supplier->note }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('suppliers.show', $supplier->id) }}" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Lihat">
                                            <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                        </a>
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Ubah">
                                            <iconify-icon icon="lucide:edit"></iconify-icon>
                                        </a>
                                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" onclick="return confirm('Hapus supplier ini?')" title="Hapus">
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
                    @if ($suppliers->hasPages())
                        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $suppliers->previousPageUrl() ?? 'javascript:void(0)' }}" @if($suppliers->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                                    <iconify-icon icon="ep:d-arrow-left" class="text-xl"></iconify-icon>
                                </a>
                            </li>
                            @foreach ($suppliers->getUrlRange(1, $suppliers->lastPage()) as $page => $url)
                                <li class="page-item">
                                    <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px{{ $page == $suppliers->currentPage() ? ' bg-primary-600 text-white' : '' }}"
                                       href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $suppliers->nextPageUrl() ?? 'javascript:void(0)' }}" @if(!$suppliers->hasMorePages()) tabindex="-1" aria-disabled="true" @endif>
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
