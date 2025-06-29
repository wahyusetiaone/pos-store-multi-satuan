// Initialize variables
let items = [];

$(document).ready(function() {
    const addItemBtn = document.getElementById('add_item');
    const itemsTable = document.getElementById('items_table');
    const totalAmount = document.getElementById('total_amount');
    const productSelect = document.getElementById('product_select');
    const createProductModal = new bootstrap.Modal(document.getElementById('createProductModal'));
    const createProductForm = document.getElementById('createProductForm');
    const storeSelect = document.getElementById('store_id');
    const modalCategorySelect = document.getElementById('modal_category_select');
    const modalStoreId = document.getElementById('modal_store_id');

    // Initialize items array from existing items
    document.querySelectorAll('#items_table tr').forEach(row => {
        const productId = row.querySelector('input[name$="[product_id]"]').value;
        const quantity = row.querySelector('input[name$="[quantity]"]').value;
        const price = row.querySelector('input[name$="[price]"]').value;
        const buyPrice = row.querySelector('input[name$="[buy_price]"]').value;
        const name = row.querySelector('td').textContent.trim();

        items.push({
            product_id: productId,
            name: name,
            quantity: parseInt(quantity),
            price: parseFloat(price),
            buy_price: parseFloat(buyPrice)
        });
    });

    // Add store change handler to load categories
    if (storeSelect) {
        storeSelect.addEventListener('change', function() {
            if (this.value) {
                fetchCategories(this.value);
                updateProductList(this.value);
            }
        });
    }

    // Function to update product list based on store
    function updateProductList(storeId) {
        fetch(`/api/products?store_id=${storeId}`)
            .then(response => response.json())
            .then(data => {
                productSelect.innerHTML = '<option value="">Pilih Produk...</option>';
                data.forEach(product => {
                    const option = new Option(
                        `${product.name} (Stok: ${product.stock})`,
                        product.id
                    );
                    option.dataset.name = product.name;
                    option.dataset.price = product.price;
                    productSelect.appendChild(option);
                });
                productSelect.innerHTML += '<option value="new" style="background-color: #e9ecef; font-weight: bold;">+ Tambah Produk Baru</option>';
            })
            .catch(error => console.error('Error:', error));
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

    // Handle product select change
    productSelect.addEventListener('change', function() {
        if (this.value === 'new') {
            // Set store_id in modal from the selected store
            const currentStoreId = storeSelect ? storeSelect.value : document.querySelector('input[name="store_id"]').value;
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
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new product to select options
                const option = new Option(data.product.name + ' (Stok: ' + data.product.stock + ')', data.product.id);
                option.dataset.name = data.product.name;
                option.dataset.price = data.product.price;

                // Insert the new option right after the first option (Pilih Produk...)
                productSelect.insertBefore(option, productSelect.options[1]);

                // Select the new product
                productSelect.value = data.product.id;

                // Set the price input
                document.getElementById('price_input').value = data.product.price;

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

    // Function to calculate total
    function calculateTotal() {
        const total = items.reduce((sum, item) => sum + (item.quantity * item.buy_price), 0);
        totalAmount.value = total.toFixed(2);
        return total;
    }

    // Function to render items table
    function renderItems() {
        itemsTable.innerHTML = items.map((item, index) => `
            <tr>
                <td>
                    ${item.name}
                    <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                </td>
                <td>
                    <input type="number" name="items[${index}][quantity]" class="form-control"
                           value="${item.quantity}" min="1" onchange="updateQuantity(${index}, this.value)">
                </td>
                <td>
                    <input type="number" name="items[${index}][price]" class="form-control"
                           value="${item.price}" min="0" onchange="updatePrice(${index}, this.value)">
                </td>
                <td>
                    <input type="number" name="items[${index}][buy_price]" class="form-control"
                           value="${item.buy_price}" min="0" onchange="updateBuyPrice(${index}, this.value)">
                </td>
                <td>${(item.quantity * item.buy_price).toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(${index})">
                        <iconify-icon icon="mingcute:delete-2-line"></iconify-icon>
                    </button>
                </td>
            </tr>
        `).join('');

        calculateTotal();
    }

    // Add item to list
    addItemBtn.addEventListener('click', function() {
        const productSelect = document.getElementById('product_select');
        const qtyInput = document.getElementById('qty_input');
        const priceInput = document.getElementById('price_input');
        const buyPriceInput = document.getElementById('buy_price_input');

        if (!productSelect.value || !qtyInput.value || !priceInput.value || !buyPriceInput.value) {
            alert('Harap isi semua field produk');
            return;
        }

        const option = productSelect.options[productSelect.selectedIndex];
        items.push({
            product_id: productSelect.value,
            name: option.dataset.name,
            quantity: parseInt(qtyInput.value),
            price: parseFloat(priceInput.value),
            buy_price: parseFloat(buyPriceInput.value)
        });

        renderItems();

        // Reset inputs
        productSelect.value = '';
        qtyInput.value = '';
        priceInput.value = '';
        buyPriceInput.value = '';
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
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
});

// Global functions for item management
window.updateQuantity = function(index, value) {
    items[index].quantity = parseInt(value);
    renderItems();
};

window.updatePrice = function(index, value) {
    items[index].price = parseFloat(value);
    renderItems();
};

window.updateBuyPrice = function(index, value) {
    items[index].buy_price = parseFloat(value);
    renderItems();
};

window.removeItem = function(index) {
    items.splice(index, 1);
    renderItems();
};
