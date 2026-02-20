# Inquiry List System Documentation

## Overview

The Inquiry List system allows users to add products to a persistent inquiry list that syncs across all pages. Items are stored in browser `localStorage` and can be submitted via the inquiry form on `inquirylist.php`.

## How It Works

### System Flow

1. **User adds product** → Click "ADD TO INQUIRY LIST" button on any product page
2. **Item stored** → Product details saved to `localStorage` with key `'inquiryItems'`
3. **Badge updated** → Inquiry button badge shows count of items
4. **User navigates** → Item count persists across pages and browser sessions
5. **User submits** → Complete inquiry form on `/inquirylist.php` 
6. **Email sent** → Selected items included in email to admin
7. **List cleared** → Items removed from `localStorage` after successful submission

### File Structure

```
ANDISON/
├── assets/
│   └── js/
│       └── inquiry-handler.js          # Main inquiry handler script
├── inquirylist.php                     # Inquiry form and display page
├── brand.php                           # Main brand listing page
├── Andison/
│   └── brand.php                       # Brand detail page
├── welding-consumables/
│   ├── metrode.php                     # Product category page
│   └── welding-consumables.php         # Product category page
└── [other product pages with inquiry buttons]
```

## Implementation

### Step 1: Include the Handler Script

Add this line to the `<head>` section of any page that displays products:

```html
<script src="assets/js/inquiry-handler.js"></script>
```

For pages in subdirectories, adjust the path accordingly:

```html
<!-- For pages in Andison/ or welding-consumables/ -->
<script src="../assets/js/inquiry-handler.js"></script>
```

### Step 2: Add "ADD TO INQUIRY" Buttons

Add buttons with class `add-to-inquiry` and required data attributes:

```html
<button class="add-to-inquiry" 
        type="button"
        data-model="Product Model Name" 
        data-type="Product Type" 
        data-brand="Brand Name">
    ADD TO INQUIRY LIST
</button>
```

**Required Data Attributes:**
- `data-model` - Product model/name (required)
- `data-type` - Product type/category (optional, defaults to "Product")
- `data-brand` - Brand name (optional, defaults to "Industrial")

### Step 3: Add Styles (Optional)

If not already present, add these CSS styles for the toast notification:

```css
.inquiry-toast {
    position: fixed;
    left: 50%;
    bottom: 28px;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.85);
    color: #fff;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 14px;
    z-index: 1200;
    opacity: 0;
    transition: opacity 200ms ease, transform 200ms ease;
    pointer-events: none;
}

.add-to-inquiry {
    background: linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);
    color: white;
    border: none;
    padding: 10px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(43, 17, 219, 0.2);
}

.add-to-inquiry:hover {
    background: linear-gradient(135deg, #1f0aa1 0%, #140570 100%);
    box-shadow: 0 4px 12px rgba(43, 17, 219, 0.35);
    transform: translateY(-2px);
}

.add-to-inquiry.already {
    background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%);
    box-shadow: 0 2px 8px rgba(76, 175, 80, 0.2);
}
```

### Step 4: Display Cart Badge (Optional)

Add a badge that shows the count of items in inquiry list:

```html
<div class="cart-badge hidden" id="cartBadge">0</div>
```

The badge will automatically:
- Show the count of items
- Hide when list is empty
- Update in real-time across all pages
- Display "99+" for 100+ items

## JavaScript API

The `inquiry-handler.js` file exposes a global `InquiryHandler` object with the following methods:

### InquiryHandler.getItems()
Returns an array of all items currently in the inquiry list.

```javascript
const items = InquiryHandler.getItems();
console.log(items);
// Output: [
//   { model: "Model A", type: "Type A", brand: "Brand A", qty: 1 },
//   { model: "Model B", type: "Type B", brand: "Brand B", qty: 1 }
// ]
```

### InquiryHandler.addItem(item)
Adds an item to the inquiry list. Returns `true` if added, `false` if already exists.

```javascript
const item = {
    model: "XYZ-123",
    type: "Welding Machine",
    brand: "Panasonic"
};

if (InquiryHandler.addItem(item)) {
    console.log("Item added successfully");
} else {
    console.log("Item already in list");
}
```

### InquiryHandler.removeItem(model, brand)
Removes a specific item from the inquiry list.

```javascript
InquiryHandler.removeItem("XYZ-123", "Panasonic");
```

### InquiryHandler.clearAllItems()
Clears all items from the inquiry list.

```javascript
InquiryHandler.clearAllItems();
```

### InquiryHandler.showToast(message)
Shows a temporary notification message.

```javascript
InquiryHandler.showToast("Product added to inquiry list!");
```

### InquiryHandler.updateCartBadge()
Manually updates the cart badge count.

```javascript
InquiryHandler.updateCartBadge();
```

## Custom Events

The handler dispatches a custom event when items are updated:

```javascript
window.addEventListener('inquiryItemsUpdated', function(event) {
    console.log("Items updated:", event.detail.items);
});
```

Listen to storage changes from other tabs:

```javascript
window.addEventListener('storage', function(event) {
    if (event.key === 'inquiryItems') {
        console.log("Inquiry items changed in another tab");
        InquiryHandler.updateCartBadge();
    }
});
```

## localStorage Structure

Items are stored in `localStorage` under the key `'inquiryItems'` as a JSON array:

```javascript
localStorage.getItem('inquiryItems');
// Output:
// [
//   {"model":"Arc Welding Machine X500","type":"Welding Equipment","brand":"Panasonic","qty":1},
//   {"model":"Electrode Holder","type":"Accessory","brand":"Weldcraft","qty":2}
// ]
```

## Implementing on New Product Pages

To add inquiry list functionality to a new product page:

1. **Include the script in `<head>`:**
   ```html
   <script src="relative/path/to/assets/js/inquiry-handler.js"></script>
   ```

2. **Add buttons to products:**
   ```html
   <button class="add-to-inquiry" 
           data-model="<?php echo $productModel; ?>" 
           data-type="<?php echo $productType; ?>" 
           data-brand="<?php echo $productBrand; ?>">
       ADD TO INQUIRY LIST
   </button>
   ```

3. **Add badge to header (if using):**
   ```html
   <a href="inquirylist.php" class="inquiry-btn">
       INQUIRY LIST 
       <span class="cart-badge hidden" id="cartBadge">0</span>
   </a>
   ```

## Troubleshooting

### Badge not updating
- Check that the script is loaded: `window.InquiryHandler` should exist
- Clear browser cache and reload
- Check browser console for JavaScript errors

### Items not persisting
- Check browser privacy settings (localStorage might be disabled)
- Try in non-private/incognito mode
- Verify localStorage quota not exceeded

### Script not found
- Check the relative path to `assets/js/inquiry-handler.js`
- For pages in subdirectories, use `../assets/js/inquiry-handler.js`
- Check that the file exists at the correct location

## Browser Compatibility

- Chrome: ✅ Full support
- Firefox: ✅ Full support
- Safari: ✅ Full support
- Edge: ✅ Full support
- IE 11: ⚠️ Partial (no CustomEvent, use polyfill if needed)

## Security Notes

1. **localStorage is NOT secure** - Do not store sensitive information
2. **Items are user-side only** - Send to server for validation
3. **No server-side validation** - Always validate items on backend before processing

## Performance Tips

1. The handler checks and updates the badge every 500ms (configurable)
2. Storage events are automatically throttled by the browser
3. For large product lists (100+), consider pagination
4. localStorage is limited to ~5-10MB per browser/domain

## Support & Maintenance

- Questions? Check the inline comments in `inquiry-handler.js`
- Bugs? Check browser console for error messages
- Improvements? Update `inquiry-handler.js` and test across all pages
