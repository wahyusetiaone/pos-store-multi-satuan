function updateFilters() {
    const params = new URLSearchParams();

    // Get store filter value (if exists)
    const storeFilter = document.getElementById('storeFilter');
    if (storeFilter && storeFilter.value) {
        params.append('store_id', storeFilter.value);
    }

    // Get category filter value
    const categoryValue = document.getElementById('categoryFilter').value;
    if (categoryValue && categoryValue !== 'all') {
        params.append('category', categoryValue);
    }

    // Get date range values
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    // Only append dates if both start and end dates are selected
    if (startDate && endDate) {
        // Validate date range
        if (startDate > endDate) {
            alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir');
            return;
        }
        params.append('start_date', startDate);
        params.append('end_date', endDate);
    } else if (startDate || endDate) {
        // If only one date is filled
        alert('Silakan isi kedua tanggal untuk filter periode');
        return;
    }

    window.location.href = `${window.location.pathname}?${params.toString()}`;
}
