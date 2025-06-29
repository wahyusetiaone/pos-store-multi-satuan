@extends('layout.layout')
@php
    $title = 'Daftar Penjualan';
    $subTitle = 'Tabel Penjualan';
    $script = '
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const filterSelect = document.getElementById("filterSelect");
                const dateRangeFields = document.getElementById("dateRangeFields");
                const storeSelect = document.getElementById("storeSelect");

                filterSelect.addEventListener("change", function() {
                    if (this.value === "custom") {
                        dateRangeFields.classList.remove("d-none");
                    } else {
                        dateRangeFields.classList.add("d-none");
                        if (this.value) {
                            document.getElementById("filterForm").submit();
                        }
                    }
                });

                if (storeSelect) {
                    storeSelect.addEventListener("change", function() {
                        document.getElementById("filterForm").submit();
                    });
                }
            });
        </script>
    ';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2 align-items-center">
                    <form id="filterForm" action="{{ route('sales.index') }}" method="GET" class="d-flex gap-2 align-items-center">
                        @if(auth()->user()->hasGlobalAccess())
                            <select id="storeSelect" name="store_id" class="form-select" style="width: auto;">
                                <option value="">Semua Toko</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                        @endif

                        <select id="filterSelect" name="filter" class="form-select" style="width: auto;">
                            <option value="">Pilih Filter</option>
                            <option value="today" {{ request('filter') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="year" {{ request('filter') == 'year' ? 'selected' : '' }}>Tahun Ini</option>
                            <option value="custom" {{ request()->filled(['start_date', 'end_date']) ? 'selected' : '' }}>Custom Range</option>
                        </select>

                        <div id="dateRangeFields" class="d-flex gap-2 {{ !request()->filled(['start_date', 'end_date']) ? 'd-none' : '' }}">
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Start Date">
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="End Date">
                            <button type="submit" class="btn btn-primary">
                                <iconify-icon icon="system-uicons:filter"></iconify-icon>
                            </button>
                        </div>
                    </form>

                    <a href="{{ route('sales.export', request()->all()) }}" class="btn btn-success d-inline-flex align-items-center gap-1">
                        Export Excel
                        <iconify-icon icon="mdi:file-excel"></iconify-icon>
                    </a>
                </div>
                <a href="{{ route('sales.create') }}" class="btn btn-primary">Tambah Penjualan</a>
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
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Diskon</th>
                                <th>Dibayar</th>
                                <th>Metode</th>
                                <th>User</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                            <tr>
                                <td>{{ ($sales->currentPage() - 1) * $sales->perPage() + $loop->iteration }}</td>
                                @if(auth()->user()->hasGlobalAccess())
                                    <td>{{ $sale->store->name ?? '-' }}</td>
                                @endif
                                <td>{{ $sale->sale_date }}</td>
                                <td>{{ $sale->customer->name ?? '-' }}</td>
                                <td>Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sale->paid, 0, ',', '.') }}</td>
                                <td>{{ ucfirst($sale->payment_method) }}</td>
                                <td>{{ $sale->user->name ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Lihat">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                    <a href="{{ route('sales.edit', $sale->id) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Ubah">
                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                    </a>
                                    <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" onclick="return confirm('Hapus penjualan ini?')" title="Hapus">
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
                    @if ($sales->hasPages())
                        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $sales->previousPageUrl() ?? 'javascript:void(0)' }}" @if($sales->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                                    <iconify-icon icon="ep:d-arrow-left" class="text-xl"></iconify-icon>
                                </a>
                            </li>
                            @foreach ($sales->getUrlRange(1, $sales->lastPage()) as $page => $url)
                                <li class="page-item">
                                    <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px{{ $page == $sales->currentPage() ? ' bg-primary-600 text-white' : '' }}"
                                       href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $sales->nextPageUrl() ?? 'javascript:void(0)' }}" @if(!$sales->hasMorePages()) tabindex="-1" aria-disabled="true" @endif>
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
