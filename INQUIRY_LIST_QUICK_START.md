# Inquiry List Integration - Quick Start Guide

## ✅ What's Been Implemented

The inquiry list system is now fully connected across your application. Here's what has been set up:

### 1. **Core Handler File**
- **File**: `assets/js/inquiry-handler.js`
- **Purpose**: Centralized JavaScript that manages all inquiry list functionality
- **Features**:
  - Adds/removes items from localStorage
  - Shows toast notifications
  - Updates cart badge in real-time
  - Handles cross-tab synchronization
  - Exports API for custom usage

### 2. **Updated Product Pages**
The following pages now include the inquiry handler and have "ADD TO INQUIRY LIST" buttons:

**Root Directory:**
- ✅ `brand.php` - Brand listing page
- ✅ `inquirylist.php` - Inquiry form submission page

**Andison Subdirectory:**
- ✅ `Andison/brand.php` - Brand detail page
- ✅ `Andison/arc-welding-machine/arc-welding-machine.php` - Arc welding category

**Main Product Categories:**
- ✅ `arc-welding-machine/arc-welding-machine.php` - Arc welding products
- ✅ `protection/protection.php` - PPE products
- ✅ `welding-accessories/welding-accessories.php` - Welding accessories
- ✅ `welding-consumables/metrode.php` - Metrode products
- ✅ `welding-consumables/welding-consumables.php` - Welding consumables

### 3. **Documentation**
- 📄 `INQUIRY_LIST_DOCUMENTATION.md` - Comprehensive developer documentation
- 📄 This file - Quick start guide

---

## 🚀 How to Test

### Test Scenario 1: Add Single Item
1. Navigate to any product page (e.g., `Andison/brand.php?name=Panasonic`)
2. Click **"ADD TO INQUIRY LIST"** button on a product
3. ✅ Verify:
   - Toast notification appears: "Added to inquiry list!"
   - Button text changes briefly to "Added ✓"
   - Badge appears in header with count "1"

### Test Scenario 2: Add Multiple Items
1. From same page, click **"ADD TO INQUIRY LIST"** on another product
2. ✅ Verify:
   - Badge count increases to "2"
   - Toast notification shows: "Added to inquiry list!"

### Test Scenario 3: Duplicate Item Check
1. Click **"ADD TO INQUIRY LIST"** on the same product again
2. ✅ Verify:
   - Toast shows: "Product already in inquiry list"
   - Button briefly shows green state (.already class)
   - Badge count stays same (no duplicate added)

### Test Scenario 4: Cross-Page Persistence
1. Add items to inquiry list on current page
2. Open new tab/window and go to a different product page
3. Check header badge
4. ✅ Verify:
   - Badge shows correct count from step 1
   - Items persist across pages

### Test Scenario 5: Navigate to Inquiry List
1. Click on **"INQUIRY LIST"** button in header
2. ✅ Verify:
   - All added products appear in table
   - Each item shows: Model, Type, Brand, Quantity
   - Items match what was added

### Test Scenario 6: Modify Quantity
1. On inquiry list page, change quantity in text box
2. ✅ Verify:
   - Quantity updates in table
   - localStorage is updated (check DevTools)

### Test Scenario 7: Remove Item
1. Click **"Remove"** button next to an item
2. ✅ Verify:
   - Item disappears from table
   - Badge count decreases
   - Item is removed from localStorage

### Test Scenario 8: Submit Form
1. Fill out inquiry form fields:
   - Full Name
   - Company
   - Email
   - Address
   - Contact Method
   - Message (optional)
2. Click **"Submit Inquiry"**
3. ✅ Verify:
   - Success message appears
   - Email sent to admin (lizette.macalindol@gmail.com)
   - localStorage is cleared
   - Redirects back to inquiry list (empty)
   - Badge is hidden (count = 0)

### Test Scenario 9: Browser Storage
1. Open Developer Tools (F12)
2. Go to **Application** → **Storage** → **Local Storage**
3. Find your domain
4. Look for key: `inquiryItems`
5. ✅ Verify:
   - JSON array contains your added items
   - Each item has: model, type, brand, qty
   - Format matches expected structure

---

## 🔍 Browser Developer Tools Inspection

### Check localStorage:
```javascript
// In browser console (F12):
localStorage.getItem('inquiryItems')

// Should return something like:
// [
//   {"model":"Arc Welding Machine X500","type":"Welding Equipment","brand":"Panasonic","qty":1},
//   {"model":"Electrode","type":"Consumable","brand":"Metrode","qty":2}
// ]
```

### Check API:
```javascript
// In browser console:
InquiryHandler.getItems()          // Get all items
InquiryHandler.addItem({model: "Test", type: "Type", brand: "Brand"})
InquiryHandler.removeItem("Test", "Brand")
InquiryHandler.clearAllItems()
InquiryHandler.showToast("Custom message")
```

### Listen to updates:
```javascript
// In browser console:
window.addEventListener('inquiryItemsUpdated', (e) => {
    console.log('Items updated:', e.detail.items);
});
```

---

## 📋 Files Modified

| File | Changes |
|------|---------|
| `assets/js/inquiry-handler.js` | Created - New handler script |
| `inquirylist.php` | Added script reference + updated event listener |
| `brand.php` | Added script reference |
| `Andison/brand.php` | Added script reference |
| `Andison/arc-welding-machine/arc-welding-machine.php` | Added script reference |
| `arc-welding-machine/arc-welding-machine.php` | Added script reference |
| `protection/protection.php` | Added script reference |
| `welding-accessories/welding-accessories.php` | Added script reference |
| `welding-consumables/metrode.php` | Added script reference |
| `welding-consumables/welding-consumables.php` | Added script reference |

---

## 🎯 Key Features

✅ **Persistent Storage** - Items saved in browser localStorage
✅ **Real-time Updates** - Badge updates instantly across all pages
✅ **Cross-Tab Sync** - Multiple browser tabs stay synchronized
✅ **Duplicate Prevention** - Same item can't be added twice
✅ **User Feedback** - Toast notifications for all actions
✅ **Form Integration** - Items included in email submission
✅ **Responsive Design** - Works on desktop and mobile
✅ **No Server Required** - Client-side only for adding items
✅ **Email Storage** - Submitted data stored in database

---

## 🔧 For Developers

### Adding inquiry functionality to a new page:

1. **Include the script in `<head>`:**
```html
<script src="path/to/assets/js/inquiry-handler.js"></script>
```

2. **Add button to products:**
```html
<button class="add-to-inquiry" 
        data-model="Product Name" 
        data-type="Product Type" 
        data-brand="Brand Name">
    ADD TO INQUIRY LIST
</button>
```

3. **Optional - Add badge to header:**
```html
<span class="cart-badge hidden" id="cartBadge">0</span>
```

4. **Test it works:**
   - Click button - should add item
   - Check DevTools console - no errors
   - Badge updates - count increases

See `INQUIRY_LIST_DOCUMENTATION.md` for complete developer documentation.

---

## ⚠️ Known Limitations

1. **Browser Dependent** - localStorage limited to 5-10MB and disabled in private mode
2. **Not Secure** - Data visible in DevTools, not encrypted
3. **Not Server-Synced** - Items only persist locally until form submitted
4. **No Offline Queue** - If form submission fails, items not automatically retried
5. **Session-Based** - Only persists during browser session (can be extended)

---

## 💡 Tips & Tricks

### Force Clear Inquiry List:
```javascript
// In browser console:
localStorage.removeItem('inquiryItems');
location.reload();
```

### Programmatically Add Items:
```javascript
// In browser console or custom script:
InquiryHandler.addItem({
    model: 'XYZ-123',
    type: 'Welding Machine',
    brand: 'Panasonic',
    qty: 2
});
```

### Monitor All Changes:
```javascript
// In browser console:
setInterval(() => {
    console.log('Current items:', InquiryHandler.getItems());
}, 1000);
```

---

## 🐛 Troubleshooting

### Issue: Badge not showing count
**Solution:**
- Check that `id="cartBadge"` exists in header
- Open DevTools Console (F12) and run: `InquiryHandler.updateCartBadge()`
- Clear cache: Ctrl+Shift+Del

### Issue: Items not persisting across pages
**Solution:**
- Check that script is loaded: Type `InquiryHandler` in console
- Check browser localStorage is enabled
- Try incognito mode (if using private browsing)

### Issue: Add button not working
**Solution:**
- Verify `inquiry-handler.js` is being loaded (Network tab in DevTools)
- Check that button has correct class: `add-to-inquiry`
- Check console for JavaScript errors

### Issue: Email not sending
**Solution:**
- Check that all required fields are filled
- Verify email address format
- Check server mail() function is configured
- Look in `/uploads/` directory for attachment handling

---

## 📞 Support

For issues or questions:
1. Check the documentation: `INQUIRY_LIST_DOCUMENTATION.md`
2. Inspect localStorage in DevTools
3. Check browser console for errors
4. Review the `inquiry-handler.js` source code
5. Test in different browsers (Chrome, Firefox, Safari, Edge)

---

**Last Updated:** February 20, 2026
**System Status:** ✅ Fully Integrated and Ready for Testing
