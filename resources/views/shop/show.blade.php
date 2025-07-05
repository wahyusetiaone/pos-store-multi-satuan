@extends('layouts.shop')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="position-sticky" style="top: 20px;">
            @if($product->images->isNotEmpty())
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                     class="img-fluid rounded-4" alt="{{ $product->name }}"
                     style="width: 100%; height: 400px; object-fit: cover;">
            @else
                <img src="https://placehold.co/400"
                     class="img-fluid rounded-4" alt="{{ $product->name }}"
                     style="width: 100%; height: 400px; object-fit: cover;">
            @endif
        </div>
    </div>
    <div class="col-md-7">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none">Home</a></li>
                @if($product->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('shop.index', ['category' => $product->category->id]) }}" class="text-decoration-none">
                            {{ $product->category->name }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        <h2 class="mb-2">{{ $product->name }}</h2>
        <div class="mb-2">
            <span class="badge bg-light text-dark border">{{ $variant->name }}</span>
        </div>
        <div class="mb-4">
            <span class="badge bg-{{ $variant->stock > 0 ? 'success' : 'danger' }}">
                {{ $variant->stock > 0 ? 'Tersedia' : 'Stok Habis' }}
            </span>
            <span class="text-muted ms-2">SKU: {{ $product->sku }}</span>
        </div>

        <!-- Store Information -->
        <div class="d-flex align-items-start gap-3 mb-4">
            <iconify-icon icon="heroicons:building-storefront" style="font-size: 24px;"></iconify-icon>
            <div>
                <h6 class="mb-1">{{ $product->store->name }}</h6>
                <small class="text-muted">{{ $product->store->address }}</small>
            </div>
        </div>

        <h3 class="text-primary mb-4">
            Rp {{ number_format($variant->price, 0, ',', '.') }}
        </h3>

        <div class="mb-4">
            <h5>Deskripsi Produk</h5>
            <p class="text-muted">{{ $product->description ?: 'Tidak ada deskripsi' }}</p>
        </div>

        <div class="mb-4">
            <h5>Detail Produk</h5>
            <table class="table table-bordered">
                <tr>
                    <td style="width: 200px;">Kategori</td>
                    <td>{{ $product->category->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Stok</td>
                    <td>{{ $variant->stock }}</td>
                </tr>
                <tr>
                    <td>Satuan</td>
                    <td>{{ $variant->productUnit->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Toko</td>
                    <td>{{ $product->store->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="d-flex gap-2">
            @php
                $waNumber = $product->store->phone ? preg_replace('/[^0-9]/', '', $product->store->phone) : '';
                if (strpos($waNumber, '0') === 0) {
                    $waNumber = '62' . substr($waNumber, 1);
                }
                $waText = urlencode("Halo, saya ingin membeli produk: $product->name ($variant->name) dengan harga Rp " . number_format($variant->price, 0, ',', '.') . ". Apakah masih tersedia?");
                $waUrl = $waNumber ? "https://wa.me/$waNumber?text=$waText" : '#';
                $waAskText = urlencode("Halo, saya ingin bertanya tentang produk: $product->name ($variant->name) dengan harga Rp " . number_format($variant->price, 0, ',', '.') . ".");
                $waAskUrl = $waNumber ? "https://wa.me/$waNumber?text=$waAskText" : '#';
            @endphp
            <a href="{{ $waUrl }}" target="_blank" class="btn btn-lg btn-primary {{ $variant->stock <= 0 || !$waNumber ? 'disabled' : '' }}">
                <iconify-icon icon="heroicons:shopping-cart" class="me-2"></iconify-icon>
                Beli Sekarang
            </a>
            <a href="{{ $waAskUrl }}" target="_blank" class="btn btn-lg btn-outline-primary {{ !$waNumber ? 'disabled' : '' }}">
                <iconify-icon icon="heroicons:chat-bubble-left-right"></iconify-icon>
                Tanya Produk
            </a>
        </div>
    </div>
</div>

@if($relatedVariants->isNotEmpty())
<div class="row mt-5">
    <div class="col-12">
        <h4 class="mb-4">Produk Terkait</h4>
        <div class="row g-4">
            @foreach($relatedVariants as $related)
                @php $relatedProduct = $related->product; @endphp
                <div class="col-md-3">
                    <div class="card h-100 product-card">
                        <div class="position-relative">
                            @if($relatedProduct->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $relatedProduct->images->first()->image_path) }}"
                                     class="card-img-top" alt="{{ $relatedProduct->name }}"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <img src="https://placehold.co/400"
                                     class="card-img-top" alt="{{ $relatedProduct->name }}"
                                     style="height: 200px; object-fit: cover;">
                            @endif
                        </div>
                        <div class="card-body">
                            <small class="text-muted mb-2 d-block">{{ $relatedProduct->category->name ?? 'Uncategorized' }}</small>
                            <h6 class="card-title mb-2">{{ $relatedProduct->name }} <span class="badge bg-light text-dark border ms-1">{{ $related->name }}</span></h6>
                            <p class="card-text text-primary fw-bold">
                                Rp {{ number_format($related->price, 0, ',', '.') }}
                            </p>
                            <span class="text-muted small d-block mb-2">Stok: {{ $related->stock }}</span>
                            <a href="{{ route('shop.show', $related) }}" class="btn btn-sm btn-primary">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@push('scripts')
<style>
    .product-card {
        transition: transform 0.2s;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
</style>
@endpush
@endsection
