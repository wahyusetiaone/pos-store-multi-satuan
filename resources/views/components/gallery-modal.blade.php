@props([
    'id' => 'galleryModal',
    'title' => 'Galeri Gambar',
    'selectMode' => false,
])
<style>
    .gallery-item {
        cursor: pointer;
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

<div class="modal fade" id="{{ $id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Upload Form -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form id="modalUploadForm" action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                            @csrf
                            <div class="col-md-4">
                                <label class="form-label">Nama Gambar</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">File Gambar</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <small class="text-muted">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB</small>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Search -->
                <div class="input-group mb-3">
                    <input type="text" id="modalSearchInput" class="form-control" placeholder="Cari gambar...">
                    <button class="btn btn-outline-secondary" type="button" id="modalSearchBtn">
                        <iconify-icon icon="iconamoon:search-light"></iconify-icon>
                    </button>
                </div>

                <!-- Gallery Grid -->
                <div class="row g-3 gallery-container" id="modalGalleryContainer" >
                    <!-- Images will be loaded here -->
                </div>
            </div>
            @if($selectMode)
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
            @endif
        </div>
    </div>
</div>

