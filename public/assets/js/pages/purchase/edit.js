// Copy of create.js logic, fully adapted for edit
let items = [];

// Function to calculate total
function calculateTotal() {
    const total = items.reduce((sum, item) => sum + (item.quantity * item.buy_price), 0);
    document.getElementById('total_amount').value = total;
    return total;
}

// Helper: fetch product units
function fetchProductUnits(productId) {
    return fetch(`/product-units?product_id=${productId}`)
        .then(res => res.json());
}

// Helper: get selected unit's conversion factor
function getSelectedUnitConversion() {
    const unitSelect = document.getElementById('unit_select');
    const selected = unitSelect.options[unitSelect.selectedIndex];
    return parseFloat(selected?.dataset.conversion || 1);
}

// Enable/disable fields
function setFieldsState(state) {
    document.getElementById('unit_select').disabled = !state;
    document.getElementById('qty_input').disabled = !state;
    document.getElementById('ppn_input').disabled = !state;
    document.getElementById('buy_price_input').disabled = !state;
    document.getElementById('price_input').disabled = true;
    document.getElementById('add_item').disabled = !state;
}

// Function to render items table
function renderItems() {
    const itemsTable = document.getElementById('items_table');
    itemsTable.innerHTML = items.map((item, index) => `
        <tr>
            <td>
                ${item.name}
                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
            </td>
            <td>
                ${item.unit_name}
                <input type="hidden" name="items[${index}][unit_id]" value="${item.unit_id}">
            </td>
            <td>
                ${item.quantity}
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
            </td>
            <td>
                ${item.ppn}
                <input type="hidden" name="items[${index}][ppn]" value="${item.ppn}">
            </td>
            <td>
                ${item.buy_price}
                <input type="hidden" name="items[${index}][buy_price]" value="${item.buy_price}">
            </td>
            <td>
                ${item.price}
                <input type="hidden" name="items[${index}][price]" value="${item.price}">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})">
                    <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                </button>
            </td>
        </tr>
    `).join('');

    calculateTotal();
}

$(document).ready(function() {
    const addItemBtn = document.getElementById('add_item');
    const productSelect = document.getElementById('product_select');
    const createProductModal = new bootstrap.Modal(document.getElementById('createProductModal'));
    const createProductForm = document.getElementById('createProductForm');
    const storeSelect = document.getElementById('store_id');
    const modalCategorySelect = document.getElementById('modal_category_select');
    const modalStoreId = document.getElementById('modal_store_id');

    // Add store change handler to load categories
    if (storeSelect) {
        storeSelect.addEventListener('change', function() {
            if (this.value) {
                fetchCategories(this.value);
            }
        });
    }

    // Function to fetch categories by store
    function fetchCategories(storeId) {
        fetch(`/api/categories?store_id=${storeId}`)
            .then(response => response.json())
            .then(data => {
                modalCategorySelect.innerHTML = '<option value="">Pilih Kategori...</option>';
                data.forEach(category => {
                    modalCategorySelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
                });
            })
            .catch(error => console.error('Error:', error));
    }

    // Product select change
    productSelect.addEventListener('change', function() {
        const productId = this.value;
        // Reset all fields
        document.getElementById('unit_select').innerHTML = '<option value="">Pilih Satuan...</option>';
        document.getElementById('qty_input').value = '';
        document.getElementById('ppn_input').value = '';
        document.getElementById('buy_price_input').value = '';
        document.getElementById('price_input').value = '';
        setFieldsState(false);
        if (!productId || productId === 'new') {
            return;
        }
        // Fetch product units
        fetchProductUnits(productId).then(units => {
            const unitSelect = document.getElementById('unit_select');
            unitSelect.innerHTML = '<option value="">Pilih Satuan...</option>';
            units.forEach(unit => {
                const opt = document.createElement('option');
                opt.value = unit.id;
                opt.textContent = unit.unit_name;
                opt.dataset.conversion = unit.conversion_factor;
                unitSelect.appendChild(opt);
            });
            unitSelect.disabled = false;
        });
    });

    // Unit select change
    document.getElementById('unit_select').addEventListener('change', function() {
        const enabled = !!this.value;
        document.getElementById('qty_input').disabled = !enabled;
        document.getElementById('ppn_input').disabled = !enabled;
        document.getElementById('buy_price_input').disabled = !enabled;
        document.getElementById('price_input').disabled = true;
        document.getElementById('add_item').disabled = !enabled;
        document.getElementById('qty_input').value = '';
        document.getElementById('ppn_input').value = '';
        document.getElementById('buy_price_input').value = '';
        document.getElementById('price_input').value = '';
    });

    // Auto-calc price_input
    function autoCalcPrice() {
        const buyPrice = parseFloat(document.getElementById('buy_price_input').value) || 0;
        const conversion = getSelectedUnitConversion();
        document.getElementById('price_input').value = conversion > 0 ? (buyPrice / conversion).toFixed(2) : '';
    }
    document.getElementById('buy_price_input').addEventListener('input', autoCalcPrice);
    document.getElementById('unit_select').addEventListener('change', autoCalcPrice);

    // Handle product select change
    productSelect.addEventListener('change', function() {
        if (this.value === 'new') {
            // Set store_id in modal from the selected store
            const currentStoreId = storeSelect ? storeSelect.value : currentStoreId;
            if (!currentStoreId) {
                alert('Silakan pilih toko terlebih dahulu');
                this.value = '';
                return;
            }
            modalStoreId.value = currentStoreId;
            // Fetch categories before showing modal
            fetchCategories(currentStoreId);
            createProductModal.show();
            this.value = ''; // Reset select
        } else if (this.value) {
            const option = this.options[this.selectedIndex];
            document.getElementById('price_input').value = option.dataset.price;
        }
    });

    // Handle save product
    document.getElementById('saveProductBtn').addEventListener('click', function() {
        const formData = new FormData(createProductForm);

        // Send request to create product
        fetch('/products', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'  // This indicates AJAX request
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new product to select options
                const option = new Option(data.data.name + ' (Stok: ' + data.data.stock + ')', data.data.id);
                option.dataset.name = data.data.name;
                option.dataset.price = data.data.price;

                // Insert the new option right after the first option (Pilih Produk...)
                productSelect.insertBefore(option, productSelect.options[1]);

                // Select the new product
                productSelect.value = data.data.id;

                // Set the price input
                document.getElementById('price_input').value = data.data.price;

                // Close modal and reset form
                createProductModal.hide();
                createProductForm.reset();

                // Show success message
                alert('Produk berhasil ditambahkan');
            } else {
                alert(data.message || 'Gagal menambahkan produk');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menambahkan produk');
        });
    });

    // Add item to list
    addItemBtn.addEventListener('click', function() {
        const productId = productSelect.value;
        const productName = productSelect.options[productSelect.selectedIndex].dataset.name;
        const unitSelect = document.getElementById('unit_select');
        const unitId = unitSelect.value;
        const unitName = unitSelect.options[unitSelect.selectedIndex]?.textContent || '';
        const qty = parseInt(document.getElementById('qty_input').value);
        const ppn = parseFloat(document.getElementById('ppn_input').value) || 0;
        const buyPrice = parseFloat(document.getElementById('buy_price_input').value);
        const price = parseFloat(document.getElementById('price_input').value);
        const conversion = getSelectedUnitConversion();
        if (!productId || !unitId || !qty || !buyPrice || !price) {
            alert('Harap isi semua field produk');
            return;
        }
        // Subtotal: (qty * buy_price) + PPN
        const subtotal = (qty * buyPrice) + ((qty * buyPrice) * (ppn / 100));
        items.push({
            product_id: productId,
            name: productName,
            unit_id: unitId,
            unit_name: unitName,
            quantity: qty,
            ppn: ppn,
            buy_price: buyPrice,
            price: price,
            conversion: conversion,
            subtotal: subtotal
        });
        renderItems();
        // Reset fields
        setFieldsState(false);
        productSelect.value = '';
        document.getElementById('unit_select').innerHTML = '<option value="">Pilih Satuan...</option>';
        document.getElementById('qty_input').value = '';
        document.getElementById('ppn_input').value = '';
        document.getElementById('buy_price_input').value = '';
        document.getElementById('price_input').value = '';
    });

    // Form submit handler
    document.getElementById('purchaseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        if (items.length === 0) {
            alert('Harap tambahkan minimal 1 produk');
            return;
        }

        // Get form data
        const formData = new FormData(this);

        // Submit form using fetch
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/purchases';
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan pembelian');
        });
    });

    // Handle pre-selected store and product
    const urlParams = new URLSearchParams(window.location.search);
    const preSelectedStoreId = urlParams.get('store_id');
    const preSelectedProductId = urlParams.get('product_id');

    if (preSelectedStoreId) {
        const storeSelect = document.getElementById('store_id');
        if (storeSelect) {
            storeSelect.value = preSelectedStoreId;
            // Trigger change event to load categories if needed
            storeSelect.dispatchEvent(new Event('change'));
        }
    }

    if (preSelectedProductId) {
        const productSelect = document.getElementById('product_select');
        if (productSelect) {
            productSelect.value = preSelectedProductId;
            // Add the product to the table automatically
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const productName = selectedOption.getAttribute('data-name');
            const productPrice = selectedOption.getAttribute('data-price');

            // Set default values
            document.getElementById('qty_input').value = '1';
            document.getElementById('price_input').value = productPrice;
            document.getElementById('buy_price_input').value = productPrice;

            // Click the add button automatically
            document.getElementById('add_item').click();
        }
    }
});

// Global function for remove item (needs to be global for onclick handler)
window.removeItem = function(index) {
    items.splice(index, 1);
    renderItems();
};
