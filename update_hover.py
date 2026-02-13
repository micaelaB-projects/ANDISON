#!/usr/bin/env python3
import os
import re

# Original code to find (without the full context)
old_pattern = r"""        document\.querySelectorAll\('\.sub-indicator'\)\.forEach\(function\(arrow\) \{\s*arrow\.addEventListener\('click', arrowHandler, true\);\s*\}\);\s*// ============================================\s*// MAIN SIDEBAR CATEGORY AND SUBCATEGORY LINKS"""

new_code = """        document.querySelectorAll('.sub-indicator').forEach(function(arrow) {
            arrow.addEventListener('click', arrowHandler, true);
        });

        // Hover handlers for mini-sidebar icons - show popover on mouseenter
        var popoverHideTimeout = null;
        
        document.querySelectorAll('.mini-sidebar-icon.has-sub').forEach(function(icon) {
            icon.addEventListener('mouseenter', function(e) {
                clearTimeout(popoverHideTimeout);
                var dataTarget = icon.getAttribute('data-target') || '';
                var categoryKey = getCategoryKeyFromTarget(dataTarget);
                if (!categoryKey) return;
                showPopoverForKey(categoryKey, icon);
            });
            
            icon.addEventListener('mouseleave', function(e) {
                popoverHideTimeout = setTimeout(function() {
                    hidePopover();
                }, 150);
            });
        });

        // Keep popover visible when hovering over the popover itself
        if (miniPopover) {
            miniPopover.addEventListener('mouseenter', function(e) {
                clearTimeout(popoverHideTimeout);
            });
            
            miniPopover.addEventListener('mouseleave', function(e) {
                popoverHideTimeout = setTimeout(function() {
                    hidePopover();
                }, 150);
            });
        }

        // ============================================
        // MAIN SIDEBAR CATEGORY AND SUBCATEGORY LINKS"""

files = ['contact.php', 'brands.php']

for fname in files:
    file_path = fname
    if not os.path.exists(file_path):
        print(f"File not found: {file_path}")
        continue
    
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Try simple string replacement first
    old_simple = """        document.querySelectorAll('.sub-indicator').forEach(function(arrow) {
            arrow.addEventListener('click', arrowHandler, true);
        });

        // ============================================
        // MAIN SIDEBAR CATEGORY AND SUBCATEGORY LINKS"""
    
    if old_simple in content:
        new_content = content.replace(old_simple, new_code)
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Successfully updated {fname}")
    else:
        print(f"Could not find target code in {fname}")
        # Try with different whitespace
        content_with_tabs = content.replace('\t', '    ')
        if old_simple in content_with_tabs:
            new_content = content_with_tabs.replace(old_simple, new_code)
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Successfully updated {fname} (with tab conversion)")
        else:
            print(f"Target code not found in {fname} even after tab conversion")
