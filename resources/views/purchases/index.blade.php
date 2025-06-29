@extends('layout.layout')
@php
    $title = 'Daftar Pembelian';
    $subTitle = 'Tabel Pembelian';
    $script = '<script src="' . asset('assets/js/pages/purchase/index.js') . '"></script>';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('purchases.create') }}" class="btn btn-primary float-end">Tambah Pembelian</a>
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
                                <th>Supplier</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>User</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $loop->iteration }}</td>
                                    @if(auth()->user()->hasGlobalAccess())
                                        <td>{{ $purchase->store->name ?? '-' }}</td>
                                    @endif
                                    <td>{{ $purchase->purchase_date }}</td>
                                    <td>{{ $purchase->supplier }}</td>
                                    <td>Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                                    <td>
                                        @if($purchase->status == 'drafted')
                                            <span class="badge bg-warning">Draft</span>
                                        @elseif($purchase->status == 'shipped')
                                            <span class="badge bg-info">Dikirim</span>
                                        @else
                                            <span class="badge bg-success">Selesai</span>
                                        @endif
                                    </td>
                                    <td>{{ $purchase->user->name ?? '-' }}</td>
                                    <td class="text-center">
                                        @if($purchase->status == 'drafted')
                                            <a href="{{ route('purchases.edit', $purchase->id) }}" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center me-1" title="Ubah">
                                                <iconify-icon icon="lucide:edit"></iconify-icon>
                                            </a>
                                            <button type="button"
                                                    class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center me-1"
                                                    title="Kirim"
                                                    onclick="openShippingModal({{ $purchase->id }})">
                                                <iconify-icon icon="mdi:truck-delivery"></iconify-icon>
                                            </button>
                                            <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center border-0" onclick="return confirm('Hapus pembelian ini?')" title="Hapus">
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
                    @if ($purchases->hasPages())
                        <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $purchases->previousPageUrl() ?? 'javascript:void(0)' }}" @if($purchases->onFirstPage()) tabindex="-1" aria-disabled="true" @endif>
                                    <iconify-icon icon="ep:d-arrow-left" class="text-xl"></iconify-icon>
                                </a>
                            </li>
                            @foreach ($purchases->getUrlRange(1, $purchases->lastPage()) as $page => $url)
                                <li class="page-item">
                                    <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px{{ $page == $purchases->currentPage() ? ' bg-primary-600 text-white' : '' }}"
                                       href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach
                            <li class="page-item">
                                <a class="page-link bg-primary-50 text-secondary-light fw-medium radius-8 border-0 px-20 py-10 d-flex align-items-center justify-content-center h-48-px w-48-px"
                                   href="{{ $purchases->nextPageUrl() ?? 'javascript:void(0)' }}" @if(!$purchases->hasMorePages()) tabindex="-1" aria-disabled="true" @endif>
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

<!-- Shipping Modal -->
<div class="modal fade" id="shippingModal" tabindex="-1">
    <div class="modal-dialog modal-xl"> <!-- Ubah dari modal-lg ke modal-xl -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buat Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="shippingForm">
                    <input type="hidden" id="purchase_id" name="purchase_id">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nomor Pengiriman</label>
                            <input type="text" name="number_shipping" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Pengiriman</label>
                            <input type="date" name="shipping_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    @if(auth()->user()->hasGlobalAccess())
                    <div class="mb-3">
                        <label class="form-label">Toko</label>
                        <select name="store_id" class="form-select" required>
                            <option value="">Pilih Toko...</option>
                            @foreach(App\Models\Store::where('is_active', true)->get() as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="drafted">Draft</option>
                            <option value="shipped" selected>Dikirim</option>
                            <option value="completed">Selesai</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 50px; min-width: 50px;"> <!-- Tambahkan min-width -->
                                        <div class="form-check"> <!-- Bungkus checkbox dalam form-check -->
                                            <input type="checkbox" class="form-check-input" id="checkAll">
                                        </div>
                                    </th>
                                    <th>Produk</th>
                                    <th style="width: 120px">Qty Pembelian</th>
                                    <th style="width: 120px">Qty Kirim</th>
                                    <th style="width: 150px">Harga</th>
                                </tr>
                            </thead>
                            <tbody id="purchaseItems">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Total:</td>
                                    <td>
                                        <input type="number" name="total" id="total_amount" class="form-control" readonly>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitShipping()">Kirim</button>
            </div>
        </div>
    </div>
</div>
@endsection
