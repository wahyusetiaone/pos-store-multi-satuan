@extends('layout.pos')

@section('content')
<div class="container-fluid">
    <!-- Navigation Icons -->
    <div class="row mb-3">
        <!-- Left Side - Product List -->
        <div class="col-lg-8 p-3">
            <div class="card">
                <div class="d-flex gap-4 p-2">
                    <a href="{{ route('index') }}" class="text-decoration-none">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="ri-home-4-fill text-white fs-4"></i>
                        </div>
                    </a>
                    <a href="{{ route('sales.index') }}" class="text-decoration-none">
                        <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="ri-shopping-cart-2-fill text-white fs-4"></i>
                        </div>
                    </a>
                    <a href="{{ route('customers.index') }}" class="text-decoration-none">
                        <div class="rounded-circle bg-info d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="ri-user-3-fill text-white fs-4"></i>
                        </div>
                    </a>
                </div>
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Data Produk</h5>
                </div>
                <div class="card-body">
                    <!-- Filter and Search -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <select class="form-select" id="categoryFilter">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari Menu" id="searchProduct">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="row" id="productsGrid">
                        @foreach($variants as $variant)
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 product-card cursor-pointer"
                                data-variant-id="{{ $variant->id }}"
                                data-product-id="{{ $variant->product->id ?? '' }}"
                                data-product-sku="{{ $variant->product->sku ?? '' }}"
                                data-category-id="{{ $variant->product->category_id ?? '' }}">
                                <img src="{{ $variant->product->images->first() ? asset('storage/' . $variant->product->images->first()->image_path) : asset('assets/images/no-image.png') }}"
                                     class="card-img-top" alt="{{ $variant->product->name ?? '-' }}" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h6 class="card-title m-0 p-0">{{ $variant->product->name ?? '-' }} <span class="badge bg-secondary">{{ $variant->name }}</span></h6>
                                    <p class="card-text text-primary m-0 p-0">Rp {{ number_format($variant->price, 0, ',', '.') }},-</p>
                                    <p class="card-text small m-0 p-0">STOK: {{ $variant->stock }}x</p>
                                    @if($variant->productUnit && $variant->productUnit->unit)
                                        <p class="card-text small m-0 p-0">Satuan: {{ $variant->productUnit->unit->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Cart -->
        <div class="col-lg-4 p-3">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0 d-flex justify-content-between">
                        <span>Keranjang</span>
                        <span>NO BON: {{ str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Customer Information -->
                    <div class="mb-3">
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" placeholder="CUSTOMER" id="customerSearch">
                            <input type="hidden" id="customerId">
                            <button class="btn btn-outline-secondary" type="button" id="searchCustomerBtn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <input type="text" class="form-control mb-2" id="customerName" placeholder="ATAS NAMA" readonly>
                        <div id="customerSearchResults" class="position-absolute bg-white border rounded shadow-sm p-2 d-none" style="z-index: 1000; width: 95%;">
                            <!-- Search results will appear here -->
                        </div>
                        <small class="text-muted">Kosongkan Atas Nama jika transaksi umum.</small>
                    </div>

                    <!-- Cart Items -->
                    <div class="table-responsive mb-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody id="cartItems">
                                <!-- Cart items will be added here dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Payment Details -->
                    <div class="payment-details">
                        <div class="mb-2 row">
                            <div class="col-6">
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="cash">Lunas</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <select name="order_type" class="form-select form-select-sm">
                                    <option value="ots">Ditempat</option>
                                    <option value="cod">Delivery</option>
                                </select>
                            </div>
                        </div>

                        <div class="payment-summary">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Bayar:</span>
                                <span class="text-end" id="totalAmount">Rp 0,-</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Voucher:</span>
                                <div class="input-group input-group-sm w-50">
                                    <input type="text" name="voucher_code" class="form-control form-control-sm" placeholder="Kode voucher">
                                    <button class="btn btn-outline-secondary" type="button" id="applyVoucher">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="justify-content-between mb-2" id="voucherAmountRow" style="display:none">
                                <span>Potongan Voucher:</span>
                                <span class="fw-bold text-end text-success" id="voucherAmount">Rp 0,-</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Diskon (%):</span>
                                <div class="input-group input-group-sm w-50">
                                    <input type="number" name="discount_percentage" class="form-control form-control-sm" value="0" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Pajak (%):</span>
                                <div class="input-group input-group-sm w-50">
                                    <input type="number" name="tax_percentage" class="form-control form-control-sm" value="0" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Diskon (Rp):</span>
                                <input type="number" name="fixed_discount" class="form-control form-control-sm w-50" value="0" min="0">
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold">Grand Total:</span>
                                <span class="fw-bold text-end" id="grandTotal">Rp 0,-</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span>Pembayaran:</span>
                                <div class="input-group input-group-sm w-50">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="payment_amount" class="form-control form-control-sm" value="0" min="0" id="paymentAmount">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Kembalian:</span>
                                <span class="fw-bold text-end" id="changeAmount">Rp 0,-</span>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary w-100" id="saveTransaction">Simpan Transaksi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .product-card {
        cursor: pointer;
        transition: transform 0.2s;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .payment-summary {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
    }
    .hover-bg-light:hover {
        background-color: #f8f9fa;
    }

    .customer-item {
        transition: background-color 0.2s;
    }

    .customer-item:hover {
        background-color: #e9ecef;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/pages/pos/pos.js') }}"></script>
@endpush
