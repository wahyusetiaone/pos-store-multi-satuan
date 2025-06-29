<div class="mb-3">
    <strong>Name:</strong> {{ $product->name }}<br>
    <strong>SKU:</strong> {{ $product->sku ?? '-' }}<br>
    <strong>Category:</strong> {{ $product->category->name ?? '-' }}<br>
    <strong>Price:</strong> {{ number_format($product->price, 2) }}<br>
    <strong>Stock:</strong> <span class="fw-bold {{ $product->stock <= 5 ? 'text-danger' : 'text-success' }}">{{ $product->stock }}</span><br>
    <strong>Status:</strong> {{ ucfirst($product->status ?? 'active') }}<br>
    <strong>Description:</strong> <span>{{ $product->description ?? '-' }}</span>
</div>
@if($product->images && $product->images->count())
    <hr>
    <h5>Images</h5>
    <div class="d-flex flex-wrap gap-2">
        @foreach($product->images as $image)
            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Product Image" style="width:80px;height:80px;object-fit:cover;">
        @endforeach
    </div>
@endif
