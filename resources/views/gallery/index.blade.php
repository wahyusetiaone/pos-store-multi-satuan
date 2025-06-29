@extends('layout.layout')
@php
    $title = 'Galeri';
    $subTitle = 'Manajemen Galeri Gambar';
    $script = '<script src="' . asset('assets/js/pages/gallery/index.js') . '"></script>';
@endphp

<style>
.gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: 0.5rem;
    height: 200px;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 1rem;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-item:hover img {
    transform: scale(1.1);
}

.gallery-title {
    color: white;
    font-weight: 500;
    margin: 0;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}

.gallery-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.gallery-actions .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
}

.gallery-actions .btn:hover {
    transform: scale(1.1);
}

.gallery-actions .btn-view {
    color: #0d6efd;
}

.gallery-actions .btn-delete {
    color: #dc3545;
}
</style>

@section('content')
<div class="row gy-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Unggah Gambar</h5>
            </div>
            <div class="card-body">
                <form id="uploadForm" action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Nama Gambar</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">File Gambar</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <small class="text-muted">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">Upload</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">Galeri Gambar</h5>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Cari gambar...">
                        <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <iconify-icon icon="iconamoon:search-light"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3" id="gallery-container">
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img src="" class="img-fluid w-100" alt="">
            </div>
        </div>
    </div>
</div>
@endsection
