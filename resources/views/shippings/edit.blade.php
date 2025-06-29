@extends('layout.layout')
@php
    $title = 'Pengiriman';
    $subTitle = 'Form Pengiriman';
    $script = '<script src="' . asset('assets/js/pages/shipping/edit.js') . '"></script>';
@endphp

@section('content')
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pengiriman</h5>
                </div>
                <div class="card-body">
                    <form id="shippingForm" action="{{ route('shippings.update', $shipping->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @if(auth()->user()->hasGlobalAccess())
                            <div class="mb-3">
                                <label class="form-label">Toko</label>
                                <select name="store_id_display" id="store_id" class="form-select" disabled>
                                    <option value="">Pilih Toko...</option>
                                    @foreach(App\Models\Store::where('is_active', true)->get() as $store)
                                        <option value="{{ $store->id }}" {{ $shipping->store_id == $store->id ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="store_id" value="{{ $shipping->store_id }}">
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Pengiriman</label>
                                    <input type="text" name="number_shipping" class="form-control" value="{{ $shipping->number_shipping }}" required readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Pengiriman</label>
                                    <input type="date" name="shipping_date" class="form-control" value="{{ $shipping->shipping_date->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Supplier</label>
                                    <input type="text" name="supplier" class="form-control" value="{{ $shipping->supplier }}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status_display" class="form-select" disabled>
                                        <option value="drafted" {{ $shipping->status == 'drafted' ? 'selected' : '' }}>Draft</option>
                                        <option value="shipped" {{ $shipping->status == 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                        <option value="completed" {{ $shipping->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                    <input type="hidden" name="status" value="{{ $shipping->status }}">
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>SKU</th>
                                    <th>Barcode</th>
                                    <th width="100">Qty Kirim</th>
                                    <th width="100">Qty Terima</th>
                                    <th width="200">Note</th>
                                    <th width="50">Aksi</th>
                                </tr>
                                </thead>
                                <tbody id="items_table">
                                @foreach($shipping->items as $item)
                                    <tr>
                                        <td>
                                            <input type="text" name="display_items[{{ $loop->index }}][product_id]" class="form-control" value="{{ $item->product->name }}" disabled>
                                            <input type="hidden" name="items[{{ $loop->index }}][product_id]" class="form-control" value="{{ $item->product_id }}">
                                        </td>
                                        <td>{{ $item->product->sku }}</td>
                                        <td>
                                            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($item->product->sku, 'C128', 1.2, 40) }}" alt="Barcode" style="max-width:120px; max-height:40px;">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $loop->index }}][qty_sent]" class="form-control" value="{{ $item->qty_sent ?? $item->quantity }}" min="0" disabled>
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{ $loop->index }}][qty_received]" class="form-control" value="{{ $item->qty_received ?? 0 }}" min="0">
                                        </td>
                                        <td>
                                            <input type="text" name="items[{{ $loop->index }}][note]" class="form-control" value="{{ $item->note ?? '' }}" placeholder="Catatan">
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('shippings.barcode', ['shipping' => $shipping->id, 'item' => $item->id]) }}"
                                               class="w-32-px h-32-px bg-info-focus text-info-main rounded-circle d-inline-flex align-items-center justify-content-center"
                                               title="Print Barcode"
                                               target="_blank">
                                                <iconify-icon icon="mdi:barcode"></iconify-icon>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="note" class="form-control" rows="2">{{ $shipping->note }}</textarea>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('shippings.index') }}" class="btn btn-secondary">Batal</a>
                            @if($shipping->status !== 'completed' )
                                <button type="submit" class="btn btn-primary">Terima Barang</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
