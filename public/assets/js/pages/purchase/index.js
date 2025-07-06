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

            document.getElementById('supplier_id').value = data.supplier_id;

            const tbody = document.getElementById('purchaseItems');
            tbody.innerHTML = data.items.map((item, index) => `
                <tr>
                    <td class="text-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input item-checkbox" name="items[${index}][selected]" value="1">
                            <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                            <input type="hidden" name="items[${index}][product_unit_id]" value="${item.product_unit_id}">
                            <input type="hidden" name="items[${index}][buy_price]" value="${item.buy_price}">
                            <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                            <input type="hidden" name="items[${index}][price]" value="${item.price}">
                        </div>
                    </td>
                    <td>${item.product?.name || 'Unknown Product'}</td>
                    <td class="text-center">${item.quantity}</td>
                    <td>
                        <input type="number" class="form-control shipping-qty" name="items[${index}][quantity]"
                               min="1" max="${item.quantity}" value="${item.quantity}" data-index="${index}">
                    </td>
                    <td class="item-price-display text-end" id="item-price-display-${index}">Rp 0</td>
                </tr>
            `).join('');

            // Update price display for all items
            updateAllPriceDisplays();

            // Add check all functionality
            document.getElementById('checkAll').addEventListener('change', function() {
                document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                calculateTotal();
                updateAllPriceDisplays();
            });

            // Add event listeners for qty change to update price display
            tbody.querySelectorAll('.shipping-qty').forEach(input => {
                input.addEventListener('input', function() {
                    updatePriceDisplay(this.dataset.index);
                    calculateTotal();
                });
            });
            // Add event listeners for checkbox change to update price display
            tbody.querySelectorAll('.item-checkbox').forEach((checkbox, idx) => {
                checkbox.addEventListener('change', function() {
                    updatePriceDisplay(idx);
                    calculateTotal();
                });
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
    // Update all buy_price dan price input sesuai qty terbaru sebelum submit
    document.querySelectorAll('.shipping-qty').forEach((input, idx) => {
        const qty = parseFloat(input.value) || 0;
        const qtyPembelian = parseFloat(document.querySelectorAll('input[name^="items["][name$="[quantity]"]')[idx].value) || 1;
        const buyPriceInput = document.querySelectorAll('input[name^="items["][name$="[buy_price]"]')[idx];
        const originalBuyPrice = parseFloat(buyPriceInput.value) || 0;
        // Hitung buy_price baru sesuai qty kirim
        const newBuyPrice = qty > 0 ? (originalBuyPrice / qtyPembelian) * qty : 0;
        buyPriceInput.value = newBuyPrice;
        // Update input price juga (jika ingin price sama dengan buy_price per qty)
        const priceInput = document.querySelectorAll('input[name^="items["][name$="[price]"]')[idx];
        if (priceInput) {
            priceInput.value = newBuyPrice;
        }
    });
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
            console.log('Checkbox checked at index:', index);
            const qty = document.querySelectorAll('.shipping-qty')[index].value;
            const qty_u = document.querySelectorAll('input[name^="items["][name$="[quantity]"]')[index].value;
            const price = document.querySelectorAll('input[name^="items["][name$="[buy_price]"]')[index].value;
            total += (price/qty_u) * qty;
        }
    });
    document.getElementById('total_amount').value = total;
    return total;
}

// --- Tambahkan fungsi berikut di bawah ---
function updatePriceDisplay(index) {
    const qtyInput = document.querySelectorAll('.shipping-qty')[index];
    const qty = parseFloat(qtyInput.value) || 0;
    const priceInput = document.querySelectorAll('input[name^="items["][name$="[buy_price]"]')[index];
    const qtyPembelian = parseFloat(document.querySelectorAll('input[name^="items["][name$="[quantity]"]')[index].value) || 1;
    const price = parseFloat(priceInput.value) || 0;
    // Hitung harga satuan * qty kirim
    const priceDisplay = qty > 0 ? (price / qtyPembelian) * qty : 0;
    document.getElementById(`item-price-display-${index}`).textContent = 'Rp ' + priceDisplay.toLocaleString('id-ID');
}

function updateAllPriceDisplays() {
    document.querySelectorAll('.shipping-qty').forEach((input, idx) => {
        updatePriceDisplay(idx);
    });
}

// Add event listeners when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for quantity changes to update total
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('shipping-qty') || e.target.classList.contains('item-checkbox')) {
            calculateTotal();
        }
    });
    updateAllPriceDisplays();
});
