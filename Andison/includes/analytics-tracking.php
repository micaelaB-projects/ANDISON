<?php
/**
 * Include this file in your public website layout to enable click tracking.
 * Place it in the <head> or before </body> tag.
 */
?>
<script>
(function(){
    // Track clicks on product, brand, and category links
    function initClickTracking(){
        // Track product links
        document.addEventListener('click', function(e){
            var target = e.target;
            
            // Walk up to find product link
            while (target && target !== document){
                var href = target.getAttribute('href') || '';
                var dataProduct = target.getAttribute('data-product') || '';
                var dataBrand = target.getAttribute('data-brand') || '';
                var dataCategory = target.getAttribute('data-category') || '';
                
                // Track product click
                if (dataProduct){
                    trackClick('product', dataProduct, dataCategory);
                    break;
                }
                
                // Track brand click  
                if (dataBrand){
                    trackClick('brand', dataBrand, '');
                    break;
                }
                
                // Track category click
                if (dataCategory){
                    trackClick('category', dataCategory, '');
                    break;
                }
                
                target = target.parentElement;
            }
        });
    }
    
    function trackClick(type, target, category){
        // Send tracking beacon
        var url = '/Andison/includes/track-click.php?type=' + encodeURIComponent(type) 
                + '&target=' + encodeURIComponent(target)
                + '&category=' + encodeURIComponent(category)
                + '&t=' + Date.now();
        
        // Use fetch with no-cors to avoid CORS issues
        if (navigator.sendBeacon) {
            navigator.sendBeacon(url);
        } else if (fetch) {
            fetch(url, {mode: 'no-cors', method: 'GET'}).catch(function(){});
        } else {
            // Fallback to image beacon
            new Image().src = url;
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', initClickTracking);
    } else {
        initClickTracking();
    }
})();
</script>
