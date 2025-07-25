// Modern toast notification using Bootstrap
function showToast(message, type = 'info') {
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.style.position = 'fixed';
        toastContainer.style.top = '20px';
        toastContainer.style.right = '20px';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0 show mb-2`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    toastContainer.appendChild(toast);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, 5000); // Auto-dismiss after 5 seconds
    toast.querySelector('.btn-close').onclick = () => toast.remove();
}

// Fungsi untuk membersihkan nilai dari format rupiah (menjadi angka murni)
function cleanRupiah(value) {
    return parseInt(String(value).replace(/[^0-9]/g, ''), 10) || 0;
}

// Fungsi untuk memformat angka menjadi format rupiah (misal: 1000000 -> 1.000.000)
function formatRupiah(number) {
    const cleanedNumber = cleanRupiah(number);
    if (cleanedNumber === 0) return '0'; // Handle case when value is 0 or empty

    const formatter = new Intl.NumberFormat('id-ID'); // Menggunakan locale Indonesia
    return formatter.format(cleanedNumber);
}

// Dapatkan semua input dengan class 'rupiah-format-input'
const rupiahInputs = document.querySelectorAll('.rupiah-format-input');

rupiahInputs.forEach(input => {
    // Event listener saat input diketik
    input.addEventListener('input', function(e) {
        let value = e.target.value;
        const cleaned = cleanRupiah(value);
        e.target.value = formatRupiah(cleaned);

        // hidden input kelas 'rupiah-raw-value'
        // const rawInput = e.target.closest('.input-group')?.querySelector('.rupiah-raw-value');
        // if (rawInput) {
        //     rawInput.value = cleaned;
        // }
    });

    // Event listener saat input kehilangan fokus (opsional, untuk memastikan format saat paste)
    input.addEventListener('blur', function(e) {
        let value = e.target.value;
        const cleaned = cleanRupiah(value);
        e.target.value = formatRupiah(cleaned);
    });

    // Inisialisasi format saat halaman dimuat jika ada nilai awal
    // Ini memastikan nilai yang sudah ada dari server diformat dengan benar saat pertama kali load
    if (input.value) {
        input.value = formatRupiah(input.value);
    }
});
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

    addItem(variant) {
        const existingItem = this.items.find(item => item.id === variant.id);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            this.items.push({
                id: variant.id,
                name: `${variant.name}`,
                price: variant.price,
                quantity: 1,
                stock: variant.qty
            });
        }
        this.updateCart();
    },

    removeItem(variantId) {
        this.items = this.items.filter(item => item.id !== variantId);
        this.updateCart();
    },

    updateItemQuantity(variantId, quantity) {
        const item = this.items.find(item => item.id === variantId);
        if (item) {
            item.quantity = parseInt(quantity);
            if (item.quantity <= 0) {
                this.removeItem(variantId);
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

    setPaymentFieldsDisabled(isDisabled) {
        document.querySelector('input[name="voucher_code"]').disabled = isDisabled;
        document.getElementById('applyVoucher').disabled = isDisabled;
        document.querySelector('input[name="discount_percentage"]').disabled = isDisabled;
        document.querySelector('input[name="tax_percentage"]').disabled = isDisabled;
        document.querySelector('input[name="fixed_discount"]').disabled = isDisabled;
        document.querySelector('input[name="payment_amount"]').disabled = isDisabled;
        document.querySelector('select[name="payment_method"]').disabled = isDisabled;
        document.querySelector('select[name="order_type"]').disabled = isDisabled;
        document.querySelector('select[name="order_type"]').disabled = isDisabled;
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
                        <button class="btn btn-outline-secondary btn-decrease" type="button" data-variant-id="${item.id}" data-quantity="${item.quantity - 1}">-</button>
                        <input type="number" class="form-control text-center quantity-input"
                            value="${item.quantity}"
                            data-variant-id="${item.id}"
                            min="1"
                            max="${item.stock}">
                        <button class="btn btn-outline-secondary btn-increase" type="button" data-variant-id="${item.id}" data-quantity="${newQuantity}">+</button>
                    </div>
                </td>
                <td>Rp ${item.price.toLocaleString()},-</td>
                <td>
                    <button class="btn btn-sm btn-danger btn-remove" data-variant-id="${item.id}">
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

        // Disable/enable payment fields if cart is empty
        const isCartEmpty = this.items.length === 0;
        this.setPaymentFieldsDisabled(isCartEmpty);
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
        // Ambil SKU dari data attribute jika ada
        const productSku = card.getAttribute('data-product-sku') ? card.getAttribute('data-product-sku').toLowerCase() : '';

        const matchesCategory = !categoryId || productCategoryId === categoryId;
        const matchesSearch = !searchText || productName.includes(searchText) || productSku.includes(searchText);

        card.closest('.col-md-3').style.display = (matchesCategory && matchesSearch) ? '' : 'none';
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Set payment fields disabled on page load
    cart.setPaymentFieldsDisabled(true);
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
        cart.fixedDiscount = parseFloat(cleanRupiah(this.value)) || 0;
        cart.updateCart();
    });

    // Payment amount handler
    document.querySelector('input[name="payment_amount"]').addEventListener('input', function() {
        cart.paymentAmount = parseFloat(cleanRupiah(this.value)) || 0;
        cart.updateCart();
    });

    // Voucher apply button handler
    document.getElementById('applyVoucher').addEventListener('click', async function() {
        const voucherCode = document.querySelector('input[name="voucher_code"]').value.trim();
        if (!voucherCode) {
            showToast('Masukkan kode voucher terlebih dahulu!', 'info');
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
                showVoucherAmount(cart.voucherDiscount);
                showToast('Voucher berhasil diterapkan!', 'success');
            } else {
                cart.voucherCode = null;
                cart.voucherDiscount = 0;
                cart.updateCart();
                showVoucherAmount(0);
                showToast(data.message || 'Voucher tidak valid!', 'danger');
            }
        } catch (error) {
            console.error('Error checking voucher:', error);
            showToast('Gagal memeriksa voucher. Silakan coba lagi.', 'danger');
        }
    });

    // Save transaction button handler
    document.getElementById('saveTransaction').addEventListener('click', async function() {
        const isHasStoreID = document.querySelector('select[name="store_id"]');

        if (cart.items.length === 0) {
            showToast('Keranjang kosong!', 'danger');
            return;
        }

        const grandTotal = cart.calculateGrandTotal();
        const paymentAmount = parseFloat(cleanRupiah(document.querySelector('input[name="payment_amount"]').value)) || 0;

        // Check if payment is less than grand total (piutang) but no customer selected
        if (paymentAmount < grandTotal && !cart.customerId) {
            showToast('Transaksi dengan pembayaran kurang dari total (piutang) wajib memilih customer!', 'info');
            return;
        }

        // Cek jika ada select store_id tapi belum dipilih
        if (isHasStoreID && (isHasStoreID.value === '' || isHasStoreID.value === null)) {
            showToast('Pilih toko terlebih dahulu!', 'danger');
            return;
        }

        try {
            const bodyData = {
                items: cart.items,
                customer_id: cart.customerId,
                customer_name: cart.customerName,
                customer_phone: cart.customerPhone,
                payment_method: cart.paymentMethod,
                order_type: cart.orderType,
                total: cart.calculateSubtotal(),
                grand_total: grandTotal,
                paid: paymentAmount,
                discount: cart.discount,
                tax: cart.tax,
                fixed_discount: cart.fixedDiscount,
                voucher_code: cart.voucherCode,
                voucher_discount: cart.voucherDiscount
            };
            if (isHasStoreID && isHasStoreID.value) {
                bodyData.store_id = isHasStoreID.value;
            }
            const response = await fetch('/sales', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(bodyData)
            });

            if (response.ok) {
                const result = await response.json();
                showToast('Transaksi berhasil disimpan!', 'success');
                cart.clear();
                // window.location.href = `/sales/${result.data.id}`;
            } else {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Gagal menyimpan transaksi');
            }
        } catch (error) {
            showToast('Error: ' + error.message, 'danger');
        }
    });

    // Add payment amount handler with validation
    document.querySelector('input[name="payment_amount"]').addEventListener('input', function() {
        const paymentAmount = parseFloat(cleanRupiah(this.value) || 0);
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
            const variant = {
                id: this.dataset.variantId,
                name: this.querySelector('.card-title').textContent,
                price: parseFloat(this.querySelector('.text-primary').textContent.replace(/[^0-9]/g, '')),
                stock: parseInt(this.querySelector('.card-text.small').textContent.match(/\d+/)[0])
            };
            cart.addItem(variant);
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
                                showToast('Error creating customer: ' + error.message, 'error');
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


function updateChangeAndLabel() {
    let grandTotal = 0;
    let payment = 0;
    const grandTotalText = document.getElementById('grandTotal').innerText.replace(/[^\d]/g, '');
    if (grandTotalText) grandTotal = parseInt(grandTotalText);
    const paymentInput = document.getElementById('paymentAmount');
    if (paymentInput && paymentInput.value) payment = parseInt(cleanRupiah(paymentInput.value));
    let change = payment - grandTotal;
    const changeLabel = document.querySelector('#changeAmount').parentElement.querySelector('span');
    if (isNaN(change)) change = 0;
    if (change < 0) {
        changeLabel.textContent = 'Kekurangan:';
        document.getElementById('changeAmount').innerText = formatRupiah(Math.abs(change));
    } else {
        changeLabel.textContent = 'Kembalian:';
        document.getElementById('changeAmount').innerText = formatRupiah(change);
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const paymentInput = document.getElementById('paymentAmount');
    if (paymentInput) {
        paymentInput.addEventListener('input', updateChangeAndLabel);
    }
    const observer = new MutationObserver(updateChangeAndLabel);
    const grandTotalElem = document.getElementById('grandTotal');
    if (grandTotalElem) {
        observer.observe(grandTotalElem, { childList: true, characterData: true, subtree: true });
    }
});
function showVoucherAmount(amount) {
    const voucherRow = document.getElementById('voucherAmountRow');
    const voucherAmount = document.getElementById('voucherAmount');
    if (amount && amount > 0) {
        voucherAmount.textContent = formatRupiah(amount);
        voucherRow.classList.add('d-flex');
        voucherRow.style.display = '';
    } else {
        voucherRow.style.display = 'none';
        voucherAmount.textContent = 'Rp 0,-';
        voucherRow.classList.remove('d-flex');
    }
}
