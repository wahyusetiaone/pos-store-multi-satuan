@extends('layout.layout')
@php
    $title = 'Daftar Hutang Pelanggan';
    $subTitle = 'Tabel Hutang Pelanggan';
@endphp

@section('content')
<div class="row gy-4">
     <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Hutang Pelanggan</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Nama</th>
                                <th scope="col">Email</th>
                                <th scope="col">Telepon</th>
                                <th scope="col">Alamat</th>
                                <th scope="col">Total Hutang</th>
                                <th scope="col" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->address }}</td>
                                    <td>
                                        Rp {{ number_format($customer->accountReceivables->sum('pending_payment'), 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('customers.show', $customer->id) }}" class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Lihat">
                                            <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    @if ($customers->hasPages())
                        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                            {{-- Previous Page Link --}}
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $customers->previousPageUrl() ?? 'javascript:void(0)' }}" @if($customers->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                                    <iconify-icon icon="ep:d-arrow-left" class="text-xl"></iconify-icon>
                                </a>
                            </li>
                            {{-- Pagination Elements --}}
                            @foreach ($customers->getUrlRange(1, $customers->lastPage()) as $page => $url)
                                <li class="page-item">
                                    <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px{{ $page == $customers->currentPage() ? ' bg-primary-600 text-white' : '' }}"
                                       href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            {{-- Next Page Link --}}
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $customers->nextPageUrl() ?? 'javascript:void(0)' }}" @if(!$customers->hasMorePages()) tabindex="-1" aria-disabled="true" @endif>
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

