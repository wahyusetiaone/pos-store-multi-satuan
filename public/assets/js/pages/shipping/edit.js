$(document).ready(function() {
    // Form submit handler
    document.getElementById('shippingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Get all items data
        const items = [];
        const rows = document.querySelectorAll('#items_table tr');

        rows.forEach((row, index) => {
            const productId = row.querySelector(`input[name="items[${index}][product_id]"]`).value;
            const qtyReceived = row.querySelector(`input[name="items[${index}][qty_received]"]`).value;
            const note = row.querySelector(`input[name="items[${index}][note]"]`).value;

            items.push({
                product_id: productId,
                qty_received: parseInt(qtyReceived),
                note: note
            });
        });

        // Prepare the data
        const formData = new FormData();
        formData.append('items', JSON.stringify(items));

        // Submit form using fetch
        fetch(`/shippings/${window.location.pathname.split('/').slice(-2, -1)[0]}/accepter`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ items: items })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/shippings';
            } else {
                alert(data.message || 'Terjadi kesalahan');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menerima pengiriman');
        });
    });
});
