@extends('layout.layout')

@php
    $title = 'Detail Produk';
    $subTitle = 'Informasi Produk';
@endphp

@section('content')
<div class="container-fluid">
    <div class="row gy-4">
        @if(auth()->user()->hasGlobalAccess())
        {{-- Action Buttons --}}
        <div class="col-lg-12">
            <div class="d-flex justify-content-end">
                <a href="{{ route('purchases.create', ['store_id' => $product->store_id, 'product_id' => $product->id]) }}" class="btn btn-primary">
                    Buat Pembelian
                </a>
            </div>
        </div>
        {{-- details store --}}
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Toko</h5>
                    <span class="text-muted">Informasi toko produk ini</span>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Toko</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:store"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $product->store->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:email"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $product->store->email }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:phone"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $product->store->phone }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:check-circle"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $product->store->is_active ? 'Aktif' : 'Tidak Aktif' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" rows="2" readonly>{{ $product->store->address }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- details product --}}
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Produk</h5>
                    <span class="text-muted">Informasi lengkap produk</span>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Produk</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:package-variant"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $product->name }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode SKU</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:barcode"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $product->sku }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:shape-outline"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $product->category->name ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:cash"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="Rp {{ number_format($product->price, 0, ',', '.') }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Stok</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:warehouse"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $product->stock }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="icon-field">
                                <span class="icon">
                                    <iconify-icon icon="mdi:toggle-switch"></iconify-icon>
                                </span>
                                <input type="text" class="form-control" value="{{ $product->status ? 'Aktif' : 'Tidak Aktif' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" rows="2" readonly>{{ $product->description }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Gambar Produk</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($product->images as $image)
                                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="Gambar Produk" style="width:80px;height:80px;object-fit:cover;">
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->hasGlobalAccess())
        {{-- details riwayat penjualan --}}
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Riwayat Penjualan</h5>
                    <span class="text-muted">History transaksi penjualan produk ini</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Diskon</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_qty = 0;
                                    $total_amount = 0;
                                @endphp
                                @forelse($product->saleItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->sale->sale_date->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('sales.show', $item->sale_id) }}" class="text-primary">
                                                {{ $item->sale_id }}
                                            </a>
                                        </td>
                                        <td>{{ $item->sale->customer->name ?? 'Umum' }}</td>
                                        <td class="text-end">{{ number_format($item->quantity) }}</td>
                                        <td class="text-end">{{ number_format($item->price) }}</td>
                                        <td class="text-end">{{ number_format($item->discount) }}</td>
                                        <td class="text-end">{{ number_format($item->subtotal) }}</td>
                                    </tr>
                                    @php
                                        $total_qty += $item->quantity;
                                        $total_amount += $item->subtotal;
                                    @endphp
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada transaksi penjualan</td>
                                    </tr>
                                @endforelse
                                @if($product->saleItems->count() > 0)
                                    <tr class="fw-bold">
                                        <td colspan="4" class="text-end">Total</td>
                                        <td class="text-end">{{ number_format($total_qty) }}</td>
                                        <td colspan="2"></td>
                                        <td class="text-end">{{ number_format($total_amount) }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
