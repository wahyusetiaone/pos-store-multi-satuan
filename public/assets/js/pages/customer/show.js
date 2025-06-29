document.addEventListener('DOMContentLoaded', function() {
    // Payment form handling
    const savePaymentBtn = document.getElementById('savePayment');
    const paymentForm = document.getElementById('paymentForm');
    const paymentModal = document.getElementById('paymentModal');

    savePaymentBtn.addEventListener('click', async function() {
        try {
            const formData = new FormData(paymentForm);
            const response = await fetch('/payment-ar-histories', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            const data = await response.json();

            if (response.ok) {
                alert('Pembayaran berhasil disimpan!');
                window.location.reload();
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        }
    });

    // Payment History handling
    const paymentHistoryModal = document.getElementById('paymentHistoryModal');
    const paymentHistoryTable = document.getElementById('paymentHistoryTable').querySelector('tbody');

    // Event listener untuk semua tombol status yang bisa di-klik
    document.querySelectorAll('.show-payment-history').forEach(button => {
        button.addEventListener('click', async function() {
            const arId = this.dataset.arId;

            try {
                const response = await fetch(`/payment-ar-histories/${arId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const histories = await response.json();

                // Clear existing rows
                paymentHistoryTable.innerHTML = '';

                if (histories.length > 0) {
                    histories.forEach(history => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${history.date}</td>
                            <td>Rp ${history.amount},-</td>
                            <td>${history.payment_method}</td>
                            <td>${history.notes}</td>
                            <td>${history.user}</td>
                        `;
                        paymentHistoryTable.appendChild(row);
                    });
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = '<td colspan="5" class="text-center">Tidak ada riwayat pembayaran</td>';
                    paymentHistoryTable.appendChild(row);
                }
            } catch (error) {
                console.error('Error fetching payment history:', error);
                paymentHistoryTable.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Gagal memuat riwayat pembayaran</td></tr>';
            }
        });
    });
});
