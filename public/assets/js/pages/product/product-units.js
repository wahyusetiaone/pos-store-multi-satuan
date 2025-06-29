document.addEventListener('DOMContentLoaded', function() {
    let unitIndex = 1;
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-unit-row')) {
            e.preventDefault();
            const row = e.target.closest('.product-unit-row');
            const newRow = row.cloneNode(true);
            // Reset values
            newRow.querySelectorAll('select, input').forEach(el => {
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
                if (el.tagName === 'INPUT') el.value = '';
            });
            // Update name attributes
            newRow.querySelector('select').name = `product_units[${unitIndex}][unit_id]`;
            newRow.querySelector('input').name = `product_units[${unitIndex}][conversion_factor]`;
            // Change add to remove button
            const btn = newRow.querySelector('button');
            btn.classList.remove('btn-success', 'add-unit-row');
            btn.classList.add('btn-danger', 'remove-unit-row');
            btn.textContent = '-';
            row.parentNode.appendChild(newRow);
            unitIndex++;
        }
        if (e.target.classList.contains('remove-unit-row')) {
            e.preventDefault();
            const row = e.target.closest('.product-unit-row');
            row.remove();
        }
    });
});

