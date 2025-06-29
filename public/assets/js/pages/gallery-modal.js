// Function to initialize gallery modal
function initGalleryModal(modalId) {
    // Load gallery items via Ajax when modal is opened
    $(`#${modalId}`).on('show.bs.modal', function () {
        loadGalleryImages(modalId);
    });

    // Handle form submission for image upload
    $('#modalUploadForm').on('submit', function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        $.ajax({
            url: form.action,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Reset form
                    form.reset();
                    // Reload gallery
                    loadGalleryImages(modalId);
                }
            },
            error: function(xhr) {
                alert('Error uploading image. Please try again.');
            }
        });
    });

    // Handle search input
    $('#modalSearchInput').on('keyup', function(e) {
        if (e.key === 'Enter') {
            handleSearch(modalId);
        }
    });

    // Handle search button click
    $('#modalSearchBtn').on('click', function() {
        handleSearch(modalId);
    });
}

// Function to handle search
function handleSearch(modalId) {
    const searchTerm = $('#modalSearchInput').val().trim();
    loadGalleryImages(modalId, searchTerm);
}

// Function to load gallery images
function loadGalleryImages(modalId, search = '') {
    $.ajax({
        url: '/gallery/images',
        method: 'GET',
        data: {
            search: search
        },
        success: function(response) {
            if (response.success) {
                const galleryContainer = $(`#${modalId} .gallery-container`);
                galleryContainer.empty();
                galleryContainer.html(response.html);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading gallery:', error);
            alert('Failed to load gallery images. Please try again.');
        }
    });
}

// Function to open gallery modal
function openGalleryModal(modalId) {
    $(`#${modalId}`).modal('show');
}

function closeGalleryModal(modalId) {
    $(`#${modalId}`).modal('hide');
}

// Function to handle image selection from gallery
function handleImageSelect(imageId) {
    const selectedItem = $(`.gallery-item[data-id="${imageId}"]`);
    const imagePath = selectedItem.data('path');
    const imageName = selectedItem.data('name');

    // Add to selected images array if not already selected
    if (!window.selectedImages.includes(imageId)) {
        window.selectedImages.push(imageId);

        // Add preview
        const preview = `
            <div class="selected-image-item" id="preview-${imageId}">
                <button type="button" class="remove-image" onclick="removeSelectedImage(${imageId})">
                    <iconify-icon icon="mingcute:close-line"></iconify-icon>
                </button>
                <img src="${imagePath}" alt="${imageName}">
            </div>
        `;
        $('#selectedImagesPreview').append(preview);
    }

    // Update hidden input
    $('#selectedImages').val(JSON.stringify(window.selectedImages));
}

// Function to remove selected image
function removeSelectedImage(imageId) {
    // Remove from array
    window.selectedImages = window.selectedImages.filter(id => id !== imageId);

    // Remove preview
    $(`#preview-${imageId}`).remove();

    // Update hidden input
    $('#selectedImages').val(JSON.stringify(window.selectedImages));
}
