# Product Modal Refactoring Summary

## Objective ✓
Unify product modal implementation across **brand.php** and **62 category pages** to use a single reusable template from `includes/product_modal.php`.

## Current Status

### ✅ COMPLETED
1. **Unified Modal Template**: `includes/product_modal.php` contains:
   - Complete modal UI with image gallery, specs table, and datasheet download
   - `openProductModal(card)` function that reads data from card elements
   - Event listeners for modal interactions (close, navigation, inquiries)
   - Support for multi-column spec tables and image galleries

2. **Brand Pages Integration**: 
   - `brand.php` ✓ Already uses unified `includes/product_modal.php`
   - Product cards have all required data attributes
   - Modal triggers on card click

3. **Category Pages Infrastructure**:
   - All 62 category pages already include `product_modal.php`
   - Product cards already have proper data attributes:
     - `data-model`, `data-type`, `data-brand`
     - `data-image`, `data-images`, `data-specs`
     - `data-description`

4. **Sidebar Navigation**:
   - `includes/sidebar.php` properly links to all category pages
   - Active state highlighting works correctly

5. **Data Compatibility**:
   - Category pages fetch product data from `andison_get_products_for_subcategory()`
   - Data structure matches unified modal expectations

### 🔄 IN PROGRESS - Category Page Custom Modal Removal

**Pattern Identified**: Each category page has its own custom modal implementation:
- Duplicate CSS for `.modal-overlay`, `.modal-container`, etc.
- Duplicate `openProductModal()` function with custom logic
- Duplicate event listeners in `DOMContentLoaded`

**Example Refactored**: `arc-welding-machine/arc-welding-machine.php`
- ✓ Removed duplicate `openProductModal()` function
- ✓ Removed `closeModal()` function
- ✓ Removed modal setup code in `DOMContentLoaded`
- ✓ Kept `product_modal.php` include at bottom
- ✓ Kept simple DOMContentLoaded listener that calls unified `openProductModal()`

**Remaining Category Pages**: 61 pages follow the identical pattern as arc-welding-machine.php

---

## How It Works

### 1. Product Card Click Flow
```
User clicks product card
  ↓
DOMContentLoaded listener detects click
  ↓
Calls openProductModal(card) from product_modal.php
  ↓
Function reads data attributes from card:
  - data-model, data-type, data-brand
  - data-images (JSON array of URLs)
  - data-specs (JSON object or array)  ↓
Modal populates with:
  - Product name/brand/type
  - Image gallery with navigation
  - Specs table (handles 2-column or multi-column formats)
  - Datasheet button (finds PDF from assets/brands items/)
  - "Add to Inquiry List" button
```

### 2. Data Source
- **Brand pages**: `brands_info.json` via JSON fetch
- **Category pages**: `andison_get_products_for_subcategory()` function
- **Both**: Product card data attributes as fallback/override

---

## Implementation Pattern for Remaining Pages

### For Each Category Page File:

#### 1. Find and Remove (Lines with PRODUCT MODAL)
```javascript
// REMOVE these sections:
- // ============================================
  // PRODUCT MODAL FUNCTIONALITY
  // ============================================
  var modal = document.getElementById('productModal'); // and related vars
  
- function openProductModal(cardElement) { ... }
- function closeModal() { ... }

- document.addEventListener('DOMContentLoaded', function() {
    modalClose.addEventListener('click', closeModal);
    // ... all modal control code
    // ... inquiry button handler
    // ... product card button listeners
  });
```

#### 2. Keep These
```php
<?php require_once __DIR__ . '/../includes/product_modal.php'; ?>

<script>
// -- Product Detail Modal – open on product card click --
document.addEventListener('DOMContentLoaded', function(){
    var grid = document.querySelectorAll('.product-card');
    grid.forEach(function(card){
        card.addEventListener('click', function(e){
            if (e.target.closest('.add-to-inquiry')) return;
            if (typeof openProductModal === 'function') {
                openProductModal(card);
            }
        });
    });
});
</script>
```

---

## Files to Update

### High Priority (Primary Categories - 9 files)
- [ ] `arc-welding-robot.php` 
- [ ] `batteries/batteries.php`
- [ ] `drilling-and-lifting/drilling-and-lifting.php`
- [ ] `gas-detectors/gas-detectors.php`
- [ ] `portable-ventilators/portable-ventilators.php`
- [ ] `power-tools/power-tools.php`
- [ ] `protection/protection.php`
- [ ] `welding-accessories/welding-accessories.php`
- [ ] `welding-consumables/welding-consumables.php`

### Medium Priority (Subcategories - 53 files)
All files in subdirectories like:
- `arc-welding-machine/mig-welding-machine.php`
- `batteries/low-maintenance.php`
- `drilling-and-lifting/magnetic-drill.php`
- `gas-detectors/portable-gas-detectors.php`
- etc.

---

## Technical Details

### Unified Modal HTML Elements (from product_modal.php)
```
#prodDetailOverlay        - Main overlay/backdrop
#prodDetailModal          - Modal container
#prodDetailClose          - Close button
#prodDetailBrand          - Brand name display
#prodDetailName           - Product name display
#prodDetailType           - Product type display
#prodMainImg              - Main image display
#prodThumbnails           - Thumbnail gallery
#prodDetailSpecsTable     - Specs table
#prodDatasheetBtn         - Datasheet download button
#prodDetailInquiry        - Inquiry list button
```

### Data Attributes on Product Cards
```html
<div class="product-card"
  data-model="YD-350KR2"
  data-type="Arc Welding Machine"
  data-brand="Panasonic Connect"
  data-image="path/to/main-image.jpg"
  data-images='["img1.jpg", "img2.jpg"]'
  data-specs='{"Voltage":"AC 200V", "Capacity":"350A"}'
  data-description="Optional product description">
  ...
</div>
```

---

## Testing Checklist

- [ ] Click product card on category page
- [ ] Modal opens with correct product information
- [ ] Image gallery shows all images and thumbnails  work
- [ ] Specs table displays correctly (2-column or multi-column)
- [ ] Datasheet button appears and links to correct PDF
- [ ] "Add to Inquiry List" button works
- [ ] Modal closes on Escape key
- [ ] Modal closes on overlay click
- [ ] Active product highlighting works in sidebar
- [ ] Related products section loads (if JSON available)

---

## Benefits of Unified Modal

✅ **Single Source of Truth**: One implementation to maintain  
✅ **Consistent UX**: Same modal experience across all product pages  
✅ **Reduced Code**: Eliminates 62 duplicate implementations  
✅ **Easier Maintenance**: Updates to modal apply everywhere  
✅ **Better Performance**: Shared CSS/JS not duplicated in every page  
✅ **Flexible Data**: Works with both JSON and database-driven products  
✅ **Progressive Enhancement**: Fallback to card data if JSON unavailable  

---

## Frontend Refactoring Only

⚠️ **Database Logic Unchanged**:
- No PHP backend changes
- No database queries modified
- `andison_get_products_for_subcategory()` unchanged
- `brands_info.json` structure unchanged
- Product data fetching remains the same

---

## Deployment Notes

1. Test arc-welding-machine.php thoroughly before rolling out to other pages
2. Update 3-5 key category pages next
3. Remaining pages can follow the same pattern
4. No database migration needed
5. No API changes
6. CSS/JS optimizations possible after full rollout (combining into single file)

---

**Last Updated**: March 2, 2026  
**Status**: In Progress - Arc Welding Machine complete, rolling out to other categories  
**Owner**: Frontend Team
