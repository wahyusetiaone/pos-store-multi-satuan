@extends('layout.layout')
@php
    $title = 'Tambah Pengiriman';
    $subTitle = 'Form Transaksi Pengiriman';
@endphp

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Transaksi Pengiriman</h5>
            </div>
            <div class="card-body">
                <form id="shippingForm" action="{{ route('shippings.store') }}" method="POST">
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
                                <label class="form-label">Tanggal Pengiriman</label>
                                <input type="date" name="shipping_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Supplier</label>
                                <input type="text" name="supplier" class="form-control" required>
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
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Tambah Produk</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <select id="product_select" class="form-select">
                                        <option value="">Pilih Produk...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ $product->price }}">
                                                {{ $product->name }} (Stok: {{ $product->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" id="qty_input" class="form-control" placeholder="Jumlah" min="1">
                                </div>
                                <div class="col-md-2">
                                    <input type="number" id="price_input" class="form-control" placeholder="Harga" min="0">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary w-100" id="add_item">Tambah</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th width="100">Jumlah</th>
                                    <th width="150">Harga</th>
                                    <th width="150">Subtotal</th>
                                    <th width="50">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="items_table">
                                <!-- Items will be added here dynamically -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td colspan="2">
                                        <input type="number" name="total" id="total_amount" class="form-control" readonly>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('shippings.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan Pengiriman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let items = [];
    const addItemBtn = document.getElementById('add_item');
    const itemsTable = document.getElementById('items_table');
    const totalAmount = document.getElementById('total_amount');

    // Function to calculate total
    function calculateTotal() {
        const total = items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
        totalAmount.value = total;
        return total;
    }

    // Function to render items table
    function renderItems() {
        itemsTable.innerHTML = items.map((item, index) => `
            <tr>
                <td>
                    ${item.name}
                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                    <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                    <input type="hidden" name="items[${index}][price]" value="${item.price}">
                </td>
                <td>${item.quantity}</td>
                <td>${item.price}</td>
                <td>${item.quantity * item.price}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        calculateTotal();
    }

    // Add item to list
    addItemBtn.addEventListener('click', function() {
        const productSelect = document.getElementById('product_select');
        const qtyInput = document.getElementById('qty_input');
        const priceInput = document.getElementById('price_input');

        if (!productSelect.value || !qtyInput.value || !priceInput.value) {
            alert('Harap isi semua field produk');
            return;
        }

        const option = productSelect.options[productSelect.selectedIndex];
        items.push({
            product_id: productSelect.value,
            name: option.dataset.name,
            quantity: parseInt(qtyInput.value),
            price: parseFloat(priceInput.value)
        });

        renderItems();

        // Reset inputs
        productSelect.value = '';
        qtyInput.value = '';
        priceInput.value = '';
    });

    // Remove item function
    window.removeItem = function(index) {
        items.splice(index, 1);
        renderItems();
    };

    // Form submit handler
    document.getElementById('shippingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (items.length === 0) {
            alert('Harap tambahkan minimal 1 produk');
            return;
        }

        // Submit form
        this.submit();
    });

    // Product select handler
    document.getElementById('product_select').addEventListener('change', function() {
        if (this.value) {
            const option = this.options[this.selectedIndex];
            document.getElementById('price_input').value = option.dataset.price;
        }
    });
});
</script>
@endpush
@endsection
