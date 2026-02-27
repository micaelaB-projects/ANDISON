<?php
/**
 * Simple Product Modal (standalone demo)
 * This is a simplified version inspired by includes/product_modal.php
 * Usage: Include this file and call openSimpleProductModal(cardElement) from a product card click.
 */
?>
<!-- ══ SIMPLE PRODUCT MODAL DEMO ══ -->
<div id="simpleProdModalOverlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
  <div id="simpleProdModal" style="background:#fff;border-radius:18px;max-width:900px;width:96vw;box-shadow:0 8px 48px rgba(43,17,219,0.13);display:flex;flex-direction:row;overflow:hidden;position:relative;">
    <button id="simpleProdModalClose" style="position:absolute;top:16px;right:16px;background:#f4f6ff;border:none;border-radius:50%;width:36px;height:36px;font-size:1.5rem;color:#333;cursor:pointer;">&times;</button>
    <div style="flex:1.1;background:#f4f6ff;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:32px 18px;">
      <img id="simpleProdModalImg" src="" alt="Product Image" style="width:220px;height:220px;object-fit:contain;border-radius:10px;background:#fff;">
    </div>
    <div style="flex:2;padding:36px 32px 32px 32px;position:relative;">
      <div id="simpleProdModalBrand" style="background:#e0e3f7;color:#2B11DB;border-radius:16px;padding:4px 18px;font-size:1rem;margin-bottom:18px;display:inline-block;"></div>
      <div id="simpleProdModalType" style="background:#2B11DB;color:#fff;border-radius:16px;padding:4px 18px;font-size:1rem;margin-bottom:18px;display:inline-block;margin-left:10px;"></div>
      <div id="simpleProdModalTitle" style="font-size:2.1rem;color:#1790d0;font-weight:700;margin-bottom:12px;"></div>
      <div id="simpleProdModalDesc" style="font-size:1.1rem;color:#444;margin-bottom:32px;"></div>
      <button id="simpleProdModalInquiryBtn" style="display:block;width:100%;background:#2B11DB;color:#fff;border:none;border-radius:10px;font-size:1.2rem;font-weight:600;padding:18px 0;margin-top:32px;cursor:pointer;">ADD TO INQUIRY LIST</button>
    </div>
  </div>
</div>
<script>
(function(){
  var overlay = document.getElementById('simpleProdModalOverlay');
  var modal = document.getElementById('simpleProdModal');
  var closeBtn = document.getElementById('simpleProdModalClose');
  var inquiryBtn = document.getElementById('simpleProdModalInquiryBtn');
  window.openSimpleProductModal = function(card) {
    var model = card.getAttribute('data-model') || '';
    var type = card.getAttribute('data-type') || '';
    var imgSrc = card.getAttribute('data-image') || '';
    var brand = card.getAttribute('data-brand') || '';
    var desc = card.getAttribute('data-desc') || '';
    document.getElementById('simpleProdModalImg').src = imgSrc;
    document.getElementById('simpleProdModalTitle').textContent = model;
    document.getElementById('simpleProdModalType').textContent = type;
    document.getElementById('simpleProdModalBrand').textContent = brand;
    document.getElementById('simpleProdModalDesc').textContent = desc;
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  };
  function closeModal(){ overlay.style.display = 'none'; document.body.style.overflow = ''; }
  closeBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', function(e){ if(e.target === overlay) closeModal(); });
  document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeModal(); });
  inquiryBtn.addEventListener('click', function(){
    inquiryBtn.textContent = 'ADDED TO LIST!';
    setTimeout(function(){ inquiryBtn.textContent = 'ADD TO INQUIRY LIST'; }, 1500);
  });
})();
</script>
<style>
#simpleProdModalOverlay { display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center; }
#simpleProdModal { animation: modalIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
@keyframes modalIn { from{opacity:0;transform:scale(.92) translateY(20px)} to{opacity:1;transform:scale(1) translateY(0)} }
</style>
