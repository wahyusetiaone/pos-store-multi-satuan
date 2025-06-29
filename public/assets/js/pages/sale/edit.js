$(document).ready(function() {
    let total = parseFloat($('input[name="total"]').val()) || 0;
    let finalTotal = total;

    // Update totals when discount changes
    $('input[name="discount"]').on('input', function() {
        calculateTotals();
    });

    // Calculate change when paid amount changes
    $('input[name="paid"]').on('input', function() {
        calculateChange();
    });

    // Form submission
    $('#saleForm').submit(function(e) {
        e.preventDefault();

        const formData = {
            store_id: $('input[name="store_id"]').val(),
            customer_id: $('select[name="customer_id"]').val(),
            sale_date: $('input[name="sale_date"]').val(),
            total: total,
            discount: parseFloat($('input[name="discount"]').val()) || 0,
            paid: parseFloat($('input[name="paid"]').val()) || 0,
            payment_method: $('select[name="payment_method"]').val(),
            note: $('textarea[name="note"]').val()
        };

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('Transaksi berhasil diperbarui');
                window.location.href = '/sales';
            },
            error: function(xhr) {
                alert('Terjadi kesalahan: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    });

    // Helper functions
    function calculateTotals() {
        const discount = parseFloat($('input[name="discount"]').val()) || 0;
        finalTotal = total - discount;
        calculateChange();
    }

    function calculateChange() {
        const paid = parseFloat($('input[name="paid"]').val()) || 0;
        const change = paid - finalTotal;
    }

    // Initial calculations
    calculateTotals();
});
