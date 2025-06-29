@foreach($products as $product)
<div class="col-md-3 mb-3">
    <div class="card h-100 product-card" data-product-id="{{ $product->id }}">
        <img src="{{ $product->images->first() ? asset($product->images->first()->image_path) : asset('images/no-image.jpg') }}"
             class="card-img-top" alt="{{ $product->name }}" style="height: 150px; object-fit: cover;">
        <div class="card-body">
            <h6 class="card-title">{{ $product->name }}</h6>
            <p class="card-text text-primary">Rp {{ number_format($product->price, 0, ',', '.') }},-</p>
            <p class="card-text small">STOK: {{ $product->stock }}x</p>
        </div>
    </div>
</div>
@endforeach

