@extends('layouts.shop')

@section('content')
<!-- Featured Products Section -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-4">Produk Unggulan</h4>
        <div class="row g-4">
            @foreach($products as $product)
            <div class="col-md-3">
                <div class="card h-100 product-card">
                    <div class="position-relative">
                        @if($product->images->isNotEmpty())
                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                 class="card-img-top" alt="{{ $product->name }}"
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <img src="https://placehold.co/400"
                                 class="card-img-top" alt="{{ $product->name }}"
                                 style="height: 200px; object-fit: cover;">
                        @endif
                        @if($product->stock <= 0)
                            <div class="position-absolute top-0 start-0 bg-danger text-white px-2 py-1 m-2 rounded-pill">
                                Stok Habis
                            </div>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <small class="text-muted mb-2">{{ $product->category->name ?? 'Uncategorized' }}</small>
                        <h6 class="card-title mb-1 flex-grow-1">{{ $product->name }}</h6>
                        <p class="card-text text-primary fw-bold mb-2">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                <iconify-icon icon="heroicons:cube"></iconify-icon>
                                Stok: {{ $product->stock }}
                            </span>
                            <a href="{{ route('shop.show', $product) }}"
                               class="btn btn-sm {{ $product->stock > 0 ? 'btn-primary' : 'btn-secondary disabled' }}">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $products->links() }}
</div>

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
