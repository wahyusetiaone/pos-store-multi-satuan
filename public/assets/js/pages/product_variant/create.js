// JS for product variant create page
// 1. Disable all fields below product select if product not selected
// 2. On product select, fetch units for that product and enable fields

document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_select');
    const unitSelect = document.querySelector('select[name="product_unit_id"]');
    const nameInput = document.querySelector('input[name="name"]');
    const priceInput = document.querySelector('input[name="price"]');
    const qtyInput = document.querySelector('input[name="qty"]');
    const statusSelect = document.querySelector('select[name="status"]');
    const submitBtn = document.querySelector('button[type="submit"]');

    function setFieldsState(enabled) {
        unitSelect.disabled = !enabled;
        nameInput.disabled = !enabled;
        priceInput.disabled = !enabled;
        qtyInput.disabled = !enabled;
        statusSelect.disabled = !enabled;
        submitBtn.disabled = !enabled;
    }

    // Initial state: disable if no product selected
    setFieldsState(!!productSelect.value);

    // --- Tambah warning jika harga jual < harga produk ---
    let selectedProductPrice = null;
    const priceWarning = document.createElement('div');
    priceWarning.className = 'alert alert-warning mt-2';
    priceWarning.style.display = 'none';
    priceWarning.textContent = 'Harga jual variant lebih rendah dari harga produk utama!';
    priceInput.parentNode.appendChild(priceWarning);

    function checkPriceWarning() {
        if (selectedProductPrice !== null && priceInput.value) {
            if (parseInt(priceInput.value) < selectedProductPrice) {
                priceWarning.style.display = '';
            } else {
                priceWarning.style.display = 'none';
            }
        } else {
            priceWarning.style.display = 'none';
        }
    }

    productSelect.addEventListener('change', function() {
        if (!this.value) {
            unitSelect.innerHTML = '<option value="">Pilih Satuan Produk...</option>';
            setFieldsState(false);
            return;
        }
        // Fetch units for selected product
        fetch(`/product-units?product_id=${this.value}`)
            .then(res => res.json())
            .then(units => {
                unitSelect.innerHTML = '<option value="">Pilih Satuan Produk...</option>';
                units.forEach(unit => {
                    const opt = document.createElement('option');
                    opt.value = unit.id;
                    opt.textContent = unit.unit_name + (unit.conversion_factor ? ` (Konversi: ${unit.conversion_factor_cash})` : '');
                    opt.dataset.conversion = unit.conversion_factor;
                    unitSelect.appendChild(opt);
                });
                setFieldsState(true);
            })
            .catch(() => {
                unitSelect.innerHTML = '<option value="">Pilih Satuan Produk...</option>';
                setFieldsState(false);
            });
        if (this.value) {
            fetch(`/api/products/get?id=${this.value}`)
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        selectedProductPrice = parseInt(res.data.price);
                        checkPriceWarning();
                    } else {
                        selectedProductPrice = null;
                        checkPriceWarning();
                    }
                })
                .catch(() => {
                    selectedProductPrice = null;
                    checkPriceWarning();
                });
        } else {
            selectedProductPrice = null;
            checkPriceWarning();
        }
    });
    priceInput.addEventListener('input', checkPriceWarning);
});
