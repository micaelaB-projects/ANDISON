
// Product search functionality
(function(){
    var searchInput = document.getElementById('productSearch');
    var productRows = document.querySelectorAll('.product-row');
    
    if (searchInput && productRows.length > 0) {
        searchInput.addEventListener('input', function(){
            var searchTerm = this.value.toLowerCase().trim();
            var visibleCount = 0;
            
            productRows.forEach(function(row){
                var model = row.getAttribute('data-model') || '';
                var type = row.getAttribute('data-type') || '';
                var badge = row.getAttribute('data-badge') || '';
                
                var matches = model.includes(searchTerm) || 
                             type.includes(searchTerm) || 
                             badge.includes(searchTerm);
                
                if (matches || searchTerm === '') {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show message if no results
            var noResultsRow = document.getElementById('noSearchResults');
            if (visibleCount === 0 && searchTerm !== '') {
                if (!noResultsRow) {
                    var tbody = document.querySelector('#productsTable tbody');
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'noSearchResults';
                    noResultsRow.innerHTML = '<td colspan="7" style="background:#fff;border-radius:12px;padding:14px;border:1px dashed rgba(43,17,219,0.25);color:#374151;text-align:center;"><i class="bi bi-search"></i> No products found matching "' + searchTerm + '"</td>';
                    tbody.appendChild(noResultsRow);
                } else {
                    noResultsRow.querySelector('td').innerHTML = '<i class="bi bi-search"></i> No products found matching "' + searchTerm + '"';
                    noResultsRow.style.display = '';
                }
            } else if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        });
    }
})();

// Bulk selection functionality
(function(){
    var selectAllCheckbox = document.getElementById('selectAllCheckbox');
    var productCheckboxes = document.querySelectorAll('.product-checkbox');
    var bulkActionsBar = document.getElementById('bulkActionsBar');
    var selectedCountText = document.getElementById('selectedCountText');
    var selectedProductsContainer = document.getElementById('selectedProductsContainer');
    
    if (!selectAllCheckbox || productCheckboxes.length === 0) return;
    
    function updateBulkActionsBar() {
        var checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
        
        if (checkedCount > 0) {
            bulkActionsBar.style.display = 'flex';
            selectedCountText.textContent = checkedCount + ' product' + (checkedCount !== 1 ? 's' : '') + ' selected';
            
            // Update hidden inputs with selected product indices
            selectedProductsContainer.innerHTML = '';
            document.querySelectorAll('.product-checkbox:checked').forEach(function(checkbox) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected_products[]';
                input.value = checkbox.value;
                selectedProductsContainer.appendChild(input);
            });
        } else {
            bulkActionsBar.style.display = 'none';
            selectedProductsContainer.innerHTML = '';
        }
        
        // Update select all checkbox state
        var totalVisible = document.querySelectorAll('.product-row:not([style*="display: none"]) .product-checkbox').length;
        var checkedVisible = document.querySelectorAll('.product-row:not([style*="display: none"]) .product-checkbox:checked').length;
        selectAllCheckbox.checked = totalVisible > 0 && checkedVisible === totalVisible;
        selectAllCheckbox.indeterminate = checkedVisible > 0 && checkedVisible < totalVisible;
    }
    
    // Handle individual checkboxes
    productCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', updateBulkActionsBar);
    });
    
    // Handle select all checkbox
    selectAllCheckbox.addEventListener('change', function() {
        var isChecked = this.checked;
        document.querySelectorAll('.product-row:not([style*="display: none"]) .product-checkbox').forEach(function(checkbox) {
            checkbox.checked = isChecked;
        });
        updateBulkActionsBar();
    });
})();
