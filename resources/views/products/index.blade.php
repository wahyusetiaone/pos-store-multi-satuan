@extends('layout.layout')

@php
    $title = 'Daftar Produk';
    $subTitle = 'Tabel Produk';
    $script = '
        <script src="' . asset('assets/js/pages/product/index.js') . '"></script>
    ';
@endphp

@section('styles')
<style>
    .sort-column {
        position: relative;
        padding-right: 18px !important;
    }
    .sort-icon {
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
    }
    .sort-icon.asc {
        color: #4318FF;
    }
    .sort-icon.desc {
        color: #4318FF;
    }
</style>
@endsection

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-center" id="filterForm">
                    <div class="col-auto">
                        <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
                    </div>
                    <div class="col-auto">
                        <select name="category_id" class="form-control">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="sort" value="{{ request('sort', 'stock_asc') }}" id="sortInput">
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                    <div class="col-auto ms-auto">
                        @if(auth()->user()->hasGlobalAccess())
                            <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                Import Produk
                            </button>
                        @endif
                        <a href="{{ route('products.create') }}" class="btn btn-success">Tambah Produk</a>
                    </div>
                </form>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span>Tampilkan:</span>
                        <select name="per_page" class="form-select" style="width: auto;" onchange="window.location.href='{{ route('products.index') }}?per_page=' + this.value">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 items</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 items</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 items</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua</option>
                        </select>
                        <button type="button" id="downloadSelected" class="btn btn-info ms-3 d-none">
                            <iconify-icon icon="mdi:barcode-scan"></iconify-icon>
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </div>
                                </th>
                                <th>No</th>
                                @if(auth()->user()->hasGlobalAccess())
                                    <th>Nama Toko</th>
                                @endif
                                <th>SKU</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Barcode</th>
                                <th>Harga</th>
                                <th class="sort-column" style="cursor: pointer;" onclick="toggleSort()">
                                    Stok
                                    <span class="sort-icon {{ request('sort') == 'stock_asc' ? 'asc' : (request('sort') == 'stock_desc' ? 'desc' : '') }}">
                                        @if(request('sort') == 'stock_asc')
                                            <iconify-icon icon="heroicons:arrow-up"></iconify-icon>
                                        @elseif(request('sort') == 'stock_desc')
                                            <iconify-icon icon="heroicons:arrow-down"></iconify-icon>
                                        @else
                                            <iconify-icon icon="heroicons:arrows-up-down"></iconify-icon>
                                        @endif
                                    </span>
                                </th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input product-checkbox" type="checkbox"
                                               value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                    </div>
                                </td>
                                <td>{{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}</td>
                                @if(auth()->user()->hasGlobalAccess())
                                    <td>{{ $product->store->name ?? '-' }}</td>
                                @endif
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '-' }}</td>
                                <td>
                                    <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($product->sku, 'C128', 1.2, 40) }}" alt="Barcode" style="max-width:120px; max-height:40px;">
                                </td>
                                <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                <td>{{ $product->stock }}</td>
                                <td class="text-center">
                                    <a href="{{ route('products.show', $product->id) }}" class="w-32-px h-32-px bg-primary-light text-primary-600 rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Lihat">
                                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                                    </a>
                                    <a href="{{ route('products.edit', $product->id) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Ubah">
                                        <iconify-icon icon="lucide:edit"></iconify-icon>
                                    </a>
                                    <a href="{{ route('products.barcode', $product->id) }}" class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Download Barcode" target="_blank">
                                        <iconify-icon icon="mdi:barcode"></iconify-icon>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" onclick="return confirm('Hapus produk ini?')" title="Hapus">
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
                    @if ($products->hasPages())
                        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $products->previousPageUrl() ?? 'javascript:void(0)' }}" @if($products->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                                    <iconify-icon icon="ep:d-arrow-left" class="text-xl"></iconify-icon>
                                </a>
                            </li>
                            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                <li class="page-item">
                                    <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px{{ $page == $products->currentPage() ? ' bg-primary-600 text-white' : '' }}"
                                       href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $products->nextPageUrl() ?? 'javascript:void(0)' }}" @if(!$products->hasMorePages()) tabindex="-1" aria-disabled="true" @endif>
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

@if(auth()->user()->hasGlobalAccess())
<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="store_id" class="form-label">Pilih Toko</label>
                        <select name="store_id" id="store_id" class="form-control" required>
                            <option value="">Pilih Toko</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">File Excel</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('products.template') }}" class="text-decoration-none" target="_blank">
                            <i class="fas fa-download"></i> Download Template
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

