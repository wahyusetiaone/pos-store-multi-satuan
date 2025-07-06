@extends('layout.layout')
@php
    $title = 'Tambah Pembelian';
    $subTitle = 'Form Transaksi Pembelian';
    $script = '
        <script src="' . asset('assets/js/pages/gallery-modal.js') . '"></script>
        <script src="' . asset('assets/js/pages/purchase/create-product.js') . '"></script>
        <script src="' . asset('assets/js/pages/purchase/create.js') . '"></script>
    ';
@endphp

<style>
.selected-images {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1rem;
}

.selected-image-item {
    position: relative;
    width: 150px;
}

.selected-image-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 0.5rem;
}

.remove-image {
    position: absolute;
    top: -10px;
    right: -10px;
    background: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    color: #dc3545;
    cursor: pointer;
}

.remove-image:hover {
    background: #dc3545;
    color: white;
}
</style>

@section('content')
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transaksi Pembelian</h5>
                </div>
                <div class="card-body">
                    <form id="purchaseForm" action="{{ route('purchases.store') }}" method="POST">
                        @csrf
                        @if(auth()->user()->hasGlobalAccess())
                            <div class="mb-3">
                                <label class="form-label">Pilih Toko</label>
                                <select name="store_id" id="store_id" class="form-select @error('store_id') is-invalid @enderror" required>
                                    <option value="">Pilih Toko...</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('store_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Pembelian</label>
                                    <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Supplier</label>
                                    <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                        <option value="">Pilih Supplier...</option>
                                        @foreach(App\Models\Supplier::where('store_id', auth()->user()->hasGlobalAccess() ? old('store_id', $preSelectedStore?->id ?? null) : auth()->user()->current_store_id)->get() as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="drafted">Draft</option>
                                        <option value="shipped">Dikirim</option>
                                        <option value="completed">Selesai</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Product Selection -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <i class="fas fa-plus"></i> Tambah Produk
                            </button>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Satuan</th>
                                    <th width="80">Jumlah</th>
                                    <th width="80">PPN (%)</th>
                                    <th width="120">Harga Beli</th>
                                    <th width="120">Harga Jual</th>
                                    <th width="50">Aksi</th>
                                </tr>
                                </thead>
                                <tbody id="items_table">
                                <!-- Items will be added here dynamically -->
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end fw-bold">Total:</td>
                                    <td colspan="2">
                                        <input type="number" name="total" id="total_amount" class="form-control" readonly>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                            <small class="form-text text-muted">* Harga jual adalah harga jual per satuan terpilih, dan tidak mempengaruhi harga penjualan serta dapat diubah pada halaman management produk.</small>

                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Pembelian</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Tambah Produk -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Tambah Produk ke Daftar Pembelian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <select id="product_select" class="form-select">
                            <option value="">Pilih Produk...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}">
                                    {{ $product->name }} (Stok: {{ $product->stock }} {{ $product->defaultUnit->name }})
                                </option>
                            @endforeach
                            <option value="new" style="background-color: #e9ecef; font-weight: bold;">+ Tambah Produk Baru</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Satuan Pembelian</label>
                        <select id="product_unit_id" name="product_unit_id" class="form-select" disabled>
                            <option value="">Pilih Satuan...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah Pembelian</label>
                        <input type="number" id="qty_input" class="form-control" placeholder="Jumlah" min="1" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Beli</label>
                        <input type="number" id="buy_price_input" class="form-control" placeholder="Harga Beli" min="0" disabled>
                    </div>
                    <div class="mb-3" id="buy_price_input_ppn_group" style="display:none;">
                        <label class="form-label">Harga Beli</label>
                        <small class="form-text text-muted"> (Termasuk PPN)</small>
                        <input type="number" id="buy_price_input_ppn" class="form-control" placeholder="Harga Beli Termasuk PPN" min="0" disabled readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Jual</label>
                        <small class="form-text text-muted"> dalam satuan dipilih</small>
                        <input type="number" id="price_input" class="form-control" placeholder="Harga Jual" min="0" disabled readonly>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="ppn_checkbox">
                            <label class="form-check-label" for="ppn_checkbox">
                                Gunakan PPN 11%
                            </label>
                        </div>
                        <input type="hidden" id="ppn_input" value="0">
                    </div>

                    <div class="mb-3">
                        <div id="product_variant_unit_table_wrapper">
                            <label class="form-label">Varian & Satuan Produk</label>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0" id="product_variant_unit_table">
                                    <thead>
                                        <tr>
                                            <th>Nama Varian</th>
                                            <th>Satuan</th>
                                            <th>Konversi</th>
                                            <th>Qty</th>
                                            <th>Harga Jual</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td colspan="6" class="text-center text-muted">Pilih produk untuk melihat varian & satuan</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <button type="button" class="btn btn-primary w-100" id="add_item" disabled data-bs-dismiss="modal">Tambah</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create Product -->
    <div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createProductModalLabel">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createProductForm">
                        @csrf
                        <input type="hidden" name="store_id" id="modal_store_id">

                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <input type="text" name="sku" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="category_id" id="modal_category_select" class="form-select" required>
                                <option value="">Pilih Kategori...</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <input type="number" name="price" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stock" class="form-control" value="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar Produk</label>
                            <input type="hidden" name="selected_images" id="selectedImages">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-primary" onclick="openGalleryModal('modalGalleryModal')">
                                    <i class="fas fa-images"></i> Pilih Gambar dari Galeri
                                </button>
                            </div>
                            <div class="selected-images" id="selectedImagesPreview"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveProductBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <x-gallery-modal
        id="modalGalleryModal"
        title="Pilih Gambar Produk"
        :selectMode="true"
    />
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ppnCheckbox = document.getElementById('ppn_checkbox');
        const ppnInput = document.getElementById('ppn_input');
        if (ppnCheckbox && ppnInput) {
            ppnCheckbox.addEventListener('change', function() {
                ppnInput.value = this.checked ? 11 : 0;
            });
        }
    });
</script>
@endpush
