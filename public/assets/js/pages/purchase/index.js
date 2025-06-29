// Initialize shipping modal
const shippingModal = new bootstrap.Modal(document.getElementById('shippingModal'));

// Function to open shipping modal
function openShippingModal(purchaseId) {
    document.getElementById('purchase_id').value = purchaseId;

    // Fetch purchase details
    fetch(`/purchases/${purchaseId}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            // Set store_id if it exists in the form
            const storeSelect = document.querySelector('select[name="store_id"]');
            if (storeSelect) {
                storeSelect.value = data.store_id;
                storeSelect.setAttribute('disabled', true);
                // Tambahkan hidden input untuk memastikan nilai terkirim
                const hiddenStoreInput = document.createElement('input');
                hiddenStoreInput.type = 'hidden';
                hiddenStoreInput.name = 'store_id';
                hiddenStoreInput.value = data.store_id;
                storeSelect.parentNode.appendChild(hiddenStoreInput);
            }

            // Set and lock status
            const statusSelect = document.querySelector('select[name="status"]');
            statusSelect.value = data.status;
            statusSelect.setAttribute('disabled', true);
            // Tambahkan hidden input untuk memastikan nilai terkirim
            const hiddenStatusInput = document.createElement('input');
            hiddenStatusInput.type = 'hidden';
            hiddenStatusInput.name = 'status';
            hiddenStatusInput.value = data.status;
            statusSelect.parentNode.appendChild(hiddenStatusInput);

            // Ensure items exists and is an array
            if (!data.items || !Array.isArray(data.items)) {
                console.error('No items data received');
                return;
            }

            const tbody = document.getElementById('purchaseItems');
            tbody.innerHTML = data.items.map((item, index) => `
                <tr>
                    <td class="text-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input item-checkbox" name="items[${index}][selected]" value="1">
                            <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                            <input type="hidden" name="items[${index}][buy_price]" value="${item.buy_price}">
                        </div>
                    </td>
                    <td>${item.product?.name || 'Unknown Product'}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td>
                        <input type="number" class="form-control shipping-qty" name="items[${index}][quantity]"
                               min="1" max="${item.quantity}" value="${item.quantity}">
                    </td>
                    <td>
                        <input type="number" class="form-control" name="items[${index}][price]"
                               value="${item.price}" readonly>
                    </td>
                </tr>
            `).join('');

            // Add check all functionality
            document.getElementById('checkAll').addEventListener('change', function() {
                document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                calculateTotal();
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data');
        });

    shippingModal.show();
}

// Function to submit shipping
function submitShipping() {
    const form = document.getElementById('shippingForm');
    const formData = new FormData(form);
    const purchaseId = document.getElementById('purchase_id').value;

    // Check if any items are selected
    let hasSelectedItems = false;
    formData.forEach((value, key) => {
        if (key.includes('[selected]') && value === '1') {
            hasSelectedItems = true;
        }
    });

    if (!hasSelectedItems) {
        alert('Pilih minimal 1 produk');
        return;
    }

    // Set status to shipped
    formData.set('status', 'shipped');

    // Submit to shipping resource route
    fetch('/shippings', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update purchase status after shipping created successfully
            updateStatus(purchaseId, 'shipped', formData.get('shipping_date'));
            shippingModal.hide();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan pengiriman');
    });
}

// Function to update status
function updateStatus(purchaseId, status, shipDate) {
    fetch(`/purchases/${purchaseId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status, ship_date: shipDate })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

// Function to calculate total
function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.item-checkbox').forEach((checkbox, index) => {
        if (checkbox.checked) {
            const qty = document.querySelectorAll('.shipping-qty')[index].value;
            const price = document.querySelectorAll('input[name^="items["][name$="[price]"]')[index].value;
            total += qty * price;
        }
    });
    document.getElementById('total_amount').value = total.toFixed(2);
    return total;
}

// Add event listeners when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for quantity changes to update total
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('shipping-qty') || e.target.classList.contains('item-checkbox')) {
            calculateTotal();
        }
    });
});
