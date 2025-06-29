$(document).ready(function() {
    let searchTimer;

    // Search functionality with debounce
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimer);
        const searchText = $(this).val();

        searchTimer = setTimeout(function() {
            $.ajax({
                url: window.location.href,
                data: { search: searchText },
                success: function(response) {
                    if (response.success) {
                        $('#gallery-container').html(response.html);
                    }
                }
            });
        }, 300); // Delay 300ms after last keypress
    });

    $('#searchBtn').on('click', function() {
        $('#searchInput').trigger('keyup');
    });

    // Handle form submission
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        var submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Create new image card
                    var imageCard = `
                        <div class="col-xl-3 col-lg-4 col-md-6" id="image-${response.data.id}">
                            <div class="gallery-item">
                                <img src="${response.data.full_path}" alt="${response.data.name}">
                                <div class="gallery-overlay">
                                    <h6 class="gallery-title">${response.data.name}</h6>
                                    <div class="gallery-actions">
                                        <button type="button"
                                                class="btn btn-view"
                                                onclick="viewImage('${response.data.full_path}', '${response.data.name}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-delete"
                                                onclick="deleteImage(${response.data.id})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    // Add new card to the gallery
                    $('#gallery-container').prepend(imageCard);

                    // Reset form
                    $('#uploadForm')[0].reset();

                    // Show success message
                    alert('Gambar berhasil diunggah');
                } else {
                    alert('Gagal mengunggah gambar');
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
            },
            complete: function() {
                submitButton.prop('disabled', false);
            }
        });
    });
});

// Function to delete image
function deleteImage(id) {
    if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
        $.ajax({
            url: `/gallery/${id}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Remove image card from gallery with fade effect
                    $(`#image-${id}`).fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert('Gagal menghapus gambar');
                }
            },
            error: function(xhr) {
                alert('Error: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'));
            }
        });
    }
}

// Function to view image in modal
function viewImage(src, title) {
    var modal = $('#imagePreviewModal');
    modal.find('.modal-title').text(title);
    modal.find('.modal-body img').attr('src', src);
    modal.modal('show');
}
