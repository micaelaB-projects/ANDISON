/**
 * Inquiry List Handler
 * Manages adding/removing items from local inquiry list that syncs with inquirylist.php
 * 
 * Usage:
 * 1. Include this script on product pages
 * 2. Add buttons with class "add-to-inquiry" and data attributes:
 *    <button class="add-to-inquiry" 
 *            data-model="Product Model" 
 *            data-type="Product Type" 
 *            data-brand="Brand Name">
 *      ADD TO INQUIRY LIST
 *    </button>
 */

(function() {
    'use strict';
    
    /**
     * Storage key for inquiry items
     */
    const STORAGE_KEY = 'inquiryItems';
    
    /**
     * Get all inquiry items from localStorage
     */
    function getItems() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        } catch (e) {
            console.error('Error parsing inquiry items:', e);
            return [];
        }
    }
    
    /**
     * Save inquiry items to localStorage
     */
    function setItems(items) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
            // Dispatch custom event to update UI across all pages/tabs
            window.dispatchEvent(new CustomEvent('inquiryItemsUpdated', { detail: { items: items } }));
        } catch (e) {
            console.error('Error saving inquiry items:', e);
        }
    }
    
    /**
     * Add item to inquiry list
     */
    function addItem(item) {
        const items = getItems();
        
        // Check if item already exists
        const exists = items.some(i => 
            i.model === item.model && i.brand === item.brand
        );
        
        if (exists) {
            return false; // Item already in list
        }
        
        // Add quantity if not present
        if (!item.qty) {
            item.qty = 1;
        }
        
        items.push(item);
        setItems(items);
        return true;
    }
    
    /**
     * Remove item from inquiry list
     */
    function removeItem(model, brand) {
        const items = getItems();
        const filtered = items.filter(i => !(i.model === model && i.brand === brand));
        setItems(filtered);
    }
    
    /**
     * Clear all inquiry items
     */
    function clearAllItems() {
        localStorage.removeItem(STORAGE_KEY);
        window.dispatchEvent(new CustomEvent('inquiryItemsUpdated', { detail: { items: [] } }));
    }
    
    /**
     * Show toast notification
     */
    function showToast(message) {
        let toast = document.querySelector('.inquiry-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'inquiry-toast';
            document.body.appendChild(toast);
        }
        
        toast.textContent = message;
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(-50%) translateY(-6px)';
        
        clearTimeout(toast._hideTimeout);
        toast._hideTimeout = setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(0)';
        }, 1800);
    }
    
    /**
     * Update cart badge count
     */
    function updateCartBadge() {
        const badge = document.getElementById('cartBadge');
        if (!badge) return;
        
        const items = getItems();
        const count = items.length;
        
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
    
    /**
     * Initialize inquiry handler on page load
     */
    function init() {
        // Update badge on page load
        updateCartBadge();
        
        // Listen for storage changes from other pages/tabs
        window.addEventListener('storage', updateCartBadge);
        
        // Listen for custom event when items added on same page
        window.addEventListener('inquiryItemsUpdated', updateCartBadge);
        
        // Handle all "add-to-inquiry" buttons
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.add-to-inquiry');
            if (!btn) return;
            
            const model = btn.dataset.model || '';
            const type = btn.dataset.type || 'Product';
            const brand = btn.dataset.brand || 'Industrial';
            
            if (!model) {
                showToast('Product model is required');
                return;
            }
            
            const added = addItem({ model, type, brand });
            
            if (!added) {
                showToast('Product already in inquiry list');
                btn.classList.add('already');
                setTimeout(() => {
                    btn.classList.remove('already');
                }, 700);
                return;
            }
            
            // Visual feedback
            const originalText = btn.textContent;
            btn.textContent = 'Added ✓';
            btn.disabled = true;
            
            showToast('Added to inquiry list!');
            
            // Update badge
            updateCartBadge();
            
            setTimeout(() => {
                btn.textContent = originalText;
                btn.disabled = false;
            }, 1500);
        });
    }
    
    /**
     * Wait for DOM to be ready, then initialize
     */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    /**
     * Export functions for external use if needed
     */
    window.InquiryHandler = {
        getItems,
        setItems,
        addItem,
        removeItem,
        clearAllItems,
        updateCartBadge,
        showToast
    };
})();
