@extends('layout.layout')
@php
    $title = 'Daftar Cash On Delivery';
    $subTitle = 'Tabel Pesanan COD';
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
                    <form id="filterForm" action="{{ route('cod.index') }}" method="GET" class="d-flex gap-2 align-items-center">
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
                </div>
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
                                <th>No. Telepon</th>
                                <th>Total</th>
                                <th>Diskon</th>
                                <th>Dibayar</th>
                                <th>Status</th>
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
                                <td>{{ $sale->customer->phone ?? '-' }}</td>
                                <td>Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sale->discount, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($sale->paid, 0, ',', '.') }}</td>
                                <td>{{ ucfirst($sale->payment_method) }}</td>
                                <td>{{ $sale->user->name ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center" title="Lihat">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $sales->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
