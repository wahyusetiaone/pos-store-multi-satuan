document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const downloadButton = document.getElementById('downloadSelected');

    // Handle "Check All" functionality
    checkAll.addEventListener('change', function() {
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDownloadButton();
    });

    // Handle individual checkbox changes
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDownloadButton();

            // Update "Check All" state
            const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
            checkAll.checked = allChecked;
        });
    });

    // Update download button visibility and URL
    function updateDownloadButton() {
        const selectedProducts = Array.from(productCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selectedProducts.length > 0) {
            downloadButton.classList.remove('d-none');
            downloadButton.onclick = function() {
                window.location.href = `${window.location.origin}/download/barcode/multiple?product_ids=${selectedProducts.join(',')}`;
            }
        } else {
            downloadButton.classList.add('d-none');
        }
    }

    // Initial check for any pre-checked boxes
    updateDownloadButton();
});

