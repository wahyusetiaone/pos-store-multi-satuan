// POS Cart Management
const cart = {
    items: [],
    customerId: null,
    customerName: '',
    customerPhone: '',
    paymentMethod: 'cash',
    orderType: 'ots', // mengubah default value sesuai dengan template
    discount: 0,
    tax: 0,
    fixedDiscount: 0,
    paymentAmount: 0,
    voucherCode: null,
    voucherDiscount: 0,

    addItem(product) {
        const existingItem = this.items.find(item => item.id === product.id);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            this.items.push({
                id: product.id,
                name: product.name,
                price: product.price,
                quantity: 1,
                stock: product.stock
            });
        }
        this.updateCart();
    },

    removeItem(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.updateCart();
    },

    updateItemQuantity(productId, quantity) {
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = parseInt(quantity);
            if (item.quantity <= 0) {
                this.removeItem(productId);
            }
        }
        this.updateCart();
    },

    calculateSubtotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    },

    calculateTax() {
        return (this.calculateSubtotal() * this.tax) / 100;
    },

    calculateDiscount() {
        const percentageDiscount = (this.calculateSubtotal() * this.discount) / 100;
        return percentageDiscount + parseFloat(this.fixedDiscount || 0);
    },

    calculateChange() {
        return this.paymentAmount - this.calculateGrandTotal();
    },

    calculateGrandTotal() {
        return this.calculateSubtotal() + this.calculateTax() - this.calculateDiscount() - this.voucherDiscount;
    },

    updateCart() {
        // Update items table
        const itemsContainer = document.getElementById('cartItems');
        itemsContainer.innerHTML = '';

        this.items.forEach((item, index) => {
            const row = document.createElement('tr');
            const newQuantity = Math.min(item.quantity + 1, item.stock);
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>
                    <div class="input-group input-group-sm" style="width: 120px;">
                        <button class="btn btn-outline-secondary btn-decrease" type="button" data-product-id="${item.id}" data-quantity="${item.quantity - 1}">-</button>
                        <input type="number" class="form-control text-center quantity-input"
                            value="${item.quantity}"
                            data-product-id="${item.id}"
                            min="1"
                            max="${item.stock}">
                        <button class="btn btn-outline-secondary btn-increase" type="button" data-product-id="${item.id}" data-quantity="${newQuantity}">+</button>
                    </div>
                </td>
                <td>Rp ${item.price.toLocaleString()},-</td>
                <td>
                    <button class="btn btn-sm btn-danger btn-remove" data-product-id="${item.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;

            // Add event listeners for this row
            const decreaseBtn = row.querySelector('.btn-decrease');
            const increaseBtn = row.querySelector('.btn-increase');
            const quantityInput = row.querySelector('.quantity-input');
            const removeBtn = row.querySelector('.btn-remove');

            decreaseBtn.addEventListener('click', () => {
                const newQty = parseInt(decreaseBtn.dataset.quantity);
                if (newQty >= 1) {
                    this.updateItemQuantity(item.id, newQty);
                }
            });

            increaseBtn.addEventListener('click', () => {
                const newQty = parseInt(increaseBtn.dataset.quantity);
                if (newQty <= item.stock) {
                    this.updateItemQuantity(item.id, newQty);
                }
            });

            quantityInput.addEventListener('change', (e) => {
                const newQty = parseInt(e.target.value);
                if (newQty >= 1 && newQty <= item.stock) {
                    this.updateItemQuantity(item.id, newQty);
                }
            });

            removeBtn.addEventListener('click', () => {
                this.removeItem(item.id);
            });

            itemsContainer.appendChild(row);
        });

        // Update totals
        document.getElementById('totalAmount').textContent = `Rp ${this.calculateSubtotal().toLocaleString()},-`;
        document.getElementById('grandTotal').textContent = `Rp ${this.calculateGrandTotal().toLocaleString()},-`;
    },

    clear() {
        // Reset cart object values
        this.items = [];
        this.customerId = null;
        this.customerName = '';
        this.customerPhone = '';
        this.paymentMethod = 'cash';
        this.orderType = 'ots'; // mengubah default value sesuai dengan template
        this.discount = 0;
        this.tax = 0;
        this.fixedDiscount = 0;
        this.paymentAmount = 0;
        this.voucherCode = null;
        this.voucherDiscount = 0;

        // Reset HTML input fields
        document.getElementById('customerSearch').value = '';
        document.getElementById('customerName').value = '';
        document.getElementById('customerId').value = '';
        document.querySelector('input[name="payment_amount"]').value = '0';
        document.querySelector('input[name="voucher_code"]').value = '';
        document.querySelector('input[name="discount_percentage"]').value = '0';
        document.querySelector('input[name="tax_percentage"]').value = '0';
        document.querySelector('input[name="fixed_discount"]').value = '0';
        document.querySelector('select[name="payment_method"]').value = 'cash';
        document.querySelector('select[name="order_type"]').value = 'ots';

        // Hide customer search results if visible
        document.getElementById('customerSearchResults').classList.add('d-none');

        // Remove any validation styling
        document.querySelector('input[name="payment_amount"]').classList.remove('is-invalid');
        const warning = document.querySelector('input[name="payment_amount"]').nextElementSibling;
        if (warning?.classList.contains('invalid-feedback')) {
            warning.remove();
        }

        this.updateCart();
    }
};

// Category filter functionality
function filterProducts() {
    const categoryId = document.getElementById('categoryFilter').value;
    const searchText = document.getElementById('searchProduct').value.toLowerCase();

    document.querySelectorAll('.product-card').forEach(card => {
        const productName = card.querySelector('.card-title').textContent.toLowerCase();
        const productCategoryId = card.getAttribute('data-category-id');

        const matchesCategory = !categoryId || productCategoryId === categoryId;
        const matchesSearch = !searchText || productName.includes(searchText);

        card.closest('.col-md-3').style.display = (matchesCategory && matchesSearch) ? '' : 'none';
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Payment method change handler
    document.querySelector('select[name="payment_method"]').addEventListener('change', function() {
        cart.paymentMethod = this.value;
    });

    // Order type change handler
    document.querySelector('select[name="order_type"]').addEventListener('change', function() {
        cart.orderType = this.value;
    });

    // Add tax percentage handler
    document.querySelector('input[name="tax_percentage"]').addEventListener('input', function() {
        cart.tax = parseFloat(this.value) || 0;
        cart.updateCart();
    });

    // Add discount percentage handler
    document.querySelector('input[name="discount_percentage"]').addEventListener('input', function() {
        cart.discount = parseFloat(this.value) || 0;
        cart.updateCart();
    });

    // Fixed discount input handler
    document.querySelector('input[name="fixed_discount"]').addEventListener('input', function() {
        cart.fixedDiscount = parseFloat(this.value) || 0;
        cart.updateCart();
    });

    // Payment amount handler
    document.querySelector('input[name="payment_amount"]').addEventListener('input', function() {
        cart.paymentAmount = parseFloat(this.value) || 0;
        cart.updateCart();
    });

    // Voucher apply button handler
    document.getElementById('applyVoucher').addEventListener('click', async function() {
        const voucherCode = document.querySelector('input[name="voucher_code"]').value.trim();
        if (!voucherCode) {
            alert('Masukkan kode voucher terlebih dahulu!');
            return;
        }

        try {
            const response = await fetch(`/api/vouchers/check?code=${encodeURIComponent(voucherCode)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok && data.valid) {
                cart.voucherCode = voucherCode;
                cart.voucherDiscount = data.discount_amount || 0;
                cart.updateCart();
                alert('Voucher berhasil diterapkan!');
            } else {
                cart.voucherCode = null;
                cart.voucherDiscount = 0;
                cart.updateCart();
                alert(data.message || 'Voucher tidak valid!');
            }
        } catch (error) {
            console.error('Error checking voucher:', error);
            alert('Gagal memeriksa voucher. Silakan coba lagi.');
        }
    });

    // Save transaction button handler
    document.getElementById('saveTransaction').addEventListener('click', async function() {
        if (cart.items.length === 0) {
            alert('Keranjang kosong!');
            return;
        }

        const grandTotal = cart.calculateGrandTotal();
        const paymentAmount = parseFloat(document.querySelector('input[name="payment_amount"]').value) || 0;

        // Check if payment is less than grand total (piutang) but no customer selected
        if (paymentAmount < grandTotal && !cart.customerId) {
            alert('Transaksi dengan pembayaran kurang dari total (piutang) wajib memilih customer!');
            return;
        }

        try {
            const response = await fetch('/sales', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    items: cart.items,
                    customer_id: cart.customerId,
                    customer_name: cart.customerName,
                    customer_phone: cart.customerPhone,
                    payment_method: cart.paymentMethod,
                    order_type: cart.orderType,
                    total: cart.calculateSubtotal(), // Perbaikan di sini
                    grand_total: grandTotal,
                    paid: paymentAmount,
                    discount: cart.discount,
                    tax: cart.tax,
                    fixed_discount: cart.fixedDiscount,
                    voucher_code: cart.voucherCode,
                    voucher_discount: cart.voucherDiscount
                })
            });

            if (response.ok) {
                const result = await response.json();
                alert('Transaksi berhasil disimpan!');
                cart.clear();
                // window.location.href = `/sales/${result.data.id}`;
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Gagal menyimpan transaksi');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });

    // Add payment amount handler with validation
    document.querySelector('input[name="payment_amount"]').addEventListener('input', function() {
        const paymentAmount = parseFloat(this.value) || 0;
        const grandTotal = cart.calculateGrandTotal();

        // If payment is less than grand total, show warning about customer requirement
        if (paymentAmount < grandTotal) {
            if (!cart.customerId) {
                this.classList.add('is-invalid');
                if (!this.nextElementSibling?.classList.contains('invalid-feedback')) {
                    const warning = document.createElement('div');
                    warning.className = 'invalid-feedback';
                    warning.textContent = 'Pembayaran kurang dari Grand Total memerlukan data Customer';
                    this.parentNode.appendChild(warning);
                }
            }
        } else {
            this.classList.remove('is-invalid');
            const warning = this.nextElementSibling;
            if (warning?.classList.contains('invalid-feedback')) {
                warning.remove();
            }
        }

        cart.paymentAmount = paymentAmount;
        cart.updateCart();
    });

    // Add category filter event listener
    document.getElementById('categoryFilter').addEventListener('change', filterProducts);

    // Add product search event listener
    document.getElementById('searchProduct').addEventListener('input', filterProducts);

    // Product card click handler - Update to include category ID attribute
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            const product = {
                id: this.dataset.productId,
                name: this.querySelector('.card-title').textContent,
                price: parseFloat(this.querySelector('.text-primary').textContent.replace(/[^0-9]/g, '')),
                stock: parseInt(this.querySelector('.card-text.small').textContent.match(/\d+/)[0])
            };
            cart.addItem(product);
        });
    });

    // Initialize tooltips and other Bootstrap components if needed
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// Customer Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const customerSearch = document.getElementById('customerSearch');
    const customerSearchResults = document.getElementById('customerSearchResults');
    const customerNameInput = document.getElementById('customerName');
    const customerIdInput = document.getElementById('customerId');
    let searchTimeout;

    customerSearch.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const searchTerm = e.target.value.trim();

        if (searchTerm.length < 2) {
            customerSearchResults.classList.add('d-none');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/search/customer-search?term=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    customerSearchResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(customer => {
                            const div = document.createElement('div');
                            div.className = 'customer-item p-2 cursor-pointer';
                            div.innerHTML = `
                                <div class="fw-bold">${customer.name}</div>
                                <div class="small text-muted">${customer.phone || 'No phone'}</div>
                            `;
                            div.addEventListener('click', () => {
                                customerIdInput.value = customer.id;
                                customerNameInput.value = customer.name;
                                customerSearch.value = customer.name;
                                customerSearchResults.classList.add('d-none');
                                cart.customerId = customer.id;
                                cart.customerName = customer.name;
                            });
                            customerSearchResults.appendChild(div);
                        });
                        customerSearchResults.classList.remove('d-none');
                    } else {
                        customerSearchResults.innerHTML = `
                            <div class="p-2 text-muted">No customers found</div>
                            <div class="p-2 border-top">
                                <a href="#" class="text-primary fw-bold text-decoration-underline" id="addNewCustomer">
                                    Tambahkan `+ searchTerm +` sebagai customer baru
                                </a>
                            </div>
                        `;
                        const addNewCustomerLink = customerSearchResults.querySelector('#addNewCustomer');
                        addNewCustomerLink.addEventListener('click', async (e) => {
                            e.preventDefault();
                            try {
                                const response = await fetch('/customers', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    },
                                    body: JSON.stringify({
                                        name: searchTerm
                                    })
                                });

                                if (response.ok) {
                                    const customer = await response.json();
                                    customerIdInput.value = customer.id;
                                    customerNameInput.value = customer.name;
                                    customerSearch.value = customer.name;
                                    customerSearchResults.classList.add('d-none');
                                    cart.customerId = customer.id;
                                    cart.customerName = customer.name;
                                } else {
                                    const error = await response.json();
                                    throw new Error(error.message || 'Failed to create customer');
                                }
                            } catch (error) {
                                alert('Error creating customer: ' + error.message);
                            }
                        });
                        customerSearchResults.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error searching customers:', error);
                    customerSearchResults.innerHTML = '<div class="p-2 text-danger">Error searching customers</div>';
                    customerSearchResults.classList.remove('d-none');
                });
        }, 300); // Debounce time: 300ms
    });

    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!customerSearch.contains(e.target) && !customerSearchResults.contains(e.target)) {
            customerSearchResults.classList.add('d-none');
        }
    });

    // Search button click handler
    document.getElementById('searchCustomerBtn').addEventListener('click', function() {
        if (customerSearch.value.trim().length >= 2) {
            const event = new Event('input');
            customerSearch.dispatchEvent(event);
        }
    });
});
