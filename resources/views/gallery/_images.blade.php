@foreach($images as $image)
    <div class="col-xl-3 col-lg-4 col-md-6" id="image-{{ $image->id }}">
        <div class="gallery-item">
            <img src="{{ Storage::url($image->path) }}" alt="{{ $image->name }}">
            <div class="gallery-overlay">
                <h6 class="gallery-title">{{ $image->name }}</h6>
                <div class="gallery-actions">
                    <button type="button"
                            class="btn btn-view"
                            onclick="viewImage('{{ Storage::url($image->path) }}', '{{ $image->name }}')">
                        <iconify-icon icon="iconamoon:eye-light"></iconify-icon>
                    </button>
                    <button type="button"
                            class="btn btn-delete"
                            onclick="deleteImage({{ $image->id }})">
                        <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

