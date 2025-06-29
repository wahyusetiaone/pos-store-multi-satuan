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
                <img src="https://via.placeholder.com/600"
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
        <div class="mb-4">
            <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }}">
                {{ $product->stock > 0 ? 'Tersedia' : 'Stok Habis' }}
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
            Rp {{ number_format($product->price, 0, ',', '.') }}
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
                    <td>{{ $product->stock }}</td>
                </tr>
                <tr>
                    <td>Toko</td>
                    <td>{{ $product->store->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-lg btn-primary {{ $product->stock <= 0 ? 'disabled' : '' }}">
                <iconify-icon icon="heroicons:shopping-cart" class="me-2"></iconify-icon>
                Beli Sekarang
            </button>
            <button class="btn btn-lg btn-outline-primary">
                <iconify-icon icon="heroicons:chat-bubble-left-right"></iconify-icon>
                Tanya Produk
            </button>
        </div>
    </div>
</div>

@if($relatedProducts->isNotEmpty())
<div class="row mt-5">
    <div class="col-12">
        <h4 class="mb-4">Produk Terkait</h4>
        <div class="row g-4">
            @foreach($relatedProducts as $related)
            <div class="col-md-3">
                <div class="card h-100 product-card">
                    <div class="position-relative">
                        @if($related->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $related->images->first()->image_path) }}"
                                 class="card-img-top" alt="{{ $related->name }}"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <img src="https://via.placeholder.com/300"
                                 class="card-img-top" alt="{{ $related->name }}"
                                 style="height: 200px; object-fit: cover;">
                        @endif
                    </div>
                    <div class="card-body">
                        <small class="text-muted mb-2 d-block">{{ $related->category->name ?? 'Uncategorized' }}</small>
                        <h6 class="card-title mb-2">{{ $related->name }}</h6>
                        <p class="card-text text-primary fw-bold">
                            Rp {{ number_format($related->price, 0, ',', '.') }}
                        </p>
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
