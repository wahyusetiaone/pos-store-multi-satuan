$(document).ready(function() {
    // Initialize variables
    let items = [];
    let total = 0;
    let finalTotal = 0;

    // Handle store change for global access users
    $('#store_id').change(function() {
        const storeId = $(this).val();
        if (storeId) {
            // Update customer dropdown
            $.get('/api/customers', { store_id: storeId }, function(customers) {
                let options = '<option value="">Pilih Pelanggan...</option>';
                customers.forEach(customer => {
                    options += `<option value="${customer.id}" data-name="${customer.name}" data-phone="${customer.phone}">
                        ${customer.name} - ${customer.phone}
                    </option>`;
                });
                $('#customer_id').html(options);
            });

            // Update product dropdown
            $.get('/api/products', { store_id: storeId }, function(products) {
                let options = '<option value="">Pilih Produk...</option>';
                products.forEach(product => {
                    options += `<option value="${product.id}"
                        data-name="${product.name}"
                        data-price="${product.price}"
                        data-stock="${product.stock}">
                        ${product.name} (Stok: ${product.stock})
                    </option>`;
                });
                $('#product_select').html(options);
            });
        }
    });

    // Handle product selection
    $('#product_select').change(function() {
        const selectedOption = $(this).find(':selected');
        const price = selectedOption.data('price') || '';
        $('#price_input').val(price);
    });

    // Add item to table
    $('#add_item').click(function() {
        const productSelect = $('#product_select');
        const selectedOption = productSelect.find(':selected');
        const productId = productSelect.val();
        const productName = selectedOption.data('name');
        const stock = selectedOption.data('stock');
        const quantity = parseInt($('#qty_input').val());
        const price = parseFloat($('#price_input').val());
        const discount = parseFloat($('#discount_input').val()) || 0;

        if (!productId || !quantity || !price) {
            alert('Mohon lengkapi data produk');
            return;
        }

        if (quantity > stock) {
            alert('Jumlah melebihi stok yang tersedia');
            return;
        }

        // Check if product already exists in items
        const existingItem = items.find(item => item.id === productId);
        if (existingItem) {
            alert('Produk sudah ada di daftar');
            return;
        }

        const subtotal = (quantity * price) - discount;
        const item = {
            id: productId,
            name: productName,
            quantity: quantity,
            price: price,
            discount: discount,
            subtotal: subtotal
        };

        items.push(item);
        updateItemsTable();
        resetInputs();
        calculateTotals();
    });

    // Remove item from table
    $(document).on('click', '.remove-item', function() {
        const index = $(this).data('index');
        items.splice(index, 1);
        updateItemsTable();
        calculateTotals();
    });

    // Update totals when discount changes
    $('#discount').on('input', function() {
        calculateTotals();
    });

    // Calculate change when paid amount changes
    $('#paid').on('input', function() {
        calculateChange();
    });

    // Form submission
    $('#saleForm').submit(function(e) {
        e.preventDefault();

        if (items.length === 0) {
            alert('Mohon tambahkan minimal 1 produk');
            return;
        }

        const formData = {
            store_id: $('#store_id').val() || null,
            customer_id: $('#customer_id').val() || null,
            customer_name: $('input[name="customer_name"]').val(),
            customer_phone: $('input[name="customer_phone"]').val(),
            items: items,
            total: total,
            discount: parseFloat($('#discount').val()) || 0,
            paid: parseFloat($('#paid').val()) || 0,
            payment_method: $('select[name="payment_method"]').val(),
            note: $('textarea[name="note"]').val()
        };

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Transaksi berhasil disimpan');
                    window.location.href = '/sales';
                } else {
                    alert(response.message || 'Terjadi kesalahan');
                }
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Helper functions
    function updateItemsTable() {
        let html = '';
        items.forEach((item, index) => {
            html += `
                <tr>
                    <td>${item.name}<input type="hidden" name="items[${index}][id]" value="${item.id}"></td>
                    <td>${item.quantity}<input type="hidden" name="items[${index}][quantity]" value="${item.quantity}"></td>
                    <td>${formatRupiah(item.price)}<input type="hidden" name="items[${index}][price]" value="${item.price}"></td>
                    <td>${formatRupiah(item.discount)}<input type="hidden" name="items[${index}][discount]" value="${item.discount}"></td>
                    <td>${formatRupiah(item.subtotal)}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-item" data-index="${index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        $('#items_table').html(html);
    }

    function calculateTotals() {
        total = items.reduce((sum, item) => sum + item.subtotal, 0);
        const discount = parseFloat($('#discount').val()) || 0;
        finalTotal = total - discount;

        $('#total').val(total);
        $('#final_total').val(finalTotal);

        calculateChange();
    }

    function calculateChange() {
        const paid = parseFloat($('#paid').val()) || 0;
        const change = paid - finalTotal;
        $('#change').val(Math.max(0, change));
    }

    function resetInputs() {
        $('#product_select').val('');
        $('#qty_input').val('');
        $('#price_input').val('');
        $('#discount_input').val('0');
    }

    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }
});
