<?php
/**
 * Product Detail Modal
 * Included by brand.php (and any other page that shows clickable product cards).
 * Requires the calling page to set JS global:  BRAND_NAME  before including this file.
 */
?>
<!-- ══ PRODUCT DETAIL MODAL ══ -->
<div id="prodDetailOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:9999;align-items:center;justify-content:center;">
  <div id="prodDetailModal" style="background:#fff;border-radius:16px;max-width:720px;width:92%;max-height:88vh;overflow-y:auto;box-shadow:0 8px 40px rgba(0,0,0,.25);position:relative;animation:modalIn .25s ease;">
    <button id="prodDetailClose" style="position:absolute;top:14px;right:18px;background:none;border:none;font-size:26px;cursor:pointer;color:#555;line-height:1;">&times;</button>
    <div style="display:flex;flex-wrap:wrap;gap:0;">
      <!-- Image panel -->
      <div id="prodDetailImgWrap" style="width:100%;max-width:320px;min-height:280px;background:#f6f8fa;border-radius:16px 0 0 16px;display:flex;align-items:center;justify-content:center;overflow:hidden;flex:0 0 320px;">
        <img id="prodDetailImg" src="" alt="" style="max-width:100%;max-height:320px;object-fit:contain;padding:24px;">
        <i id="prodDetailNoImg" class="bi bi-tools" style="display:none;font-size:64px;color:#bbb;"></i>
      </div>
      <!-- Info panel -->
      <div style="flex:1;min-width:240px;padding:32px 28px 28px;">
        <span id="prodDetailBrand" style="font-size:12px;font-weight:700;color:#00bfb3;letter-spacing:.5px;text-transform:uppercase;"></span>
        <h2 id="prodDetailName" style="font-size:20px;font-weight:800;color:#1a1a2e;margin:8px 0 4px;line-height:1.3;"></h2>
        <span id="prodDetailType" style="display:inline-block;background:#f0f4ff;color:#2B11DB;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;margin-bottom:18px;"></span>
        <!-- Specs table -->
        <div id="prodDetailSpecsWrap" style="display:none;margin-bottom:18px;">
          <div style="font-size:12px;font-weight:700;color:#888;letter-spacing:.5px;margin-bottom:8px;">SPECIFICATIONS</div>
          <table id="prodDetailSpecsTable" style="width:100%;border-collapse:collapse;font-size:13px;"></table>
        </div>
        <div id="prodDetailNoSpecs" style="font-size:13px;color:#aaa;margin-bottom:18px;">Contact us for detailed specifications.</div>
        <!-- Add to Inquiry -->
        <button id="prodDetailInquiry" style="width:100%;padding:12px;background:#2B11DB;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;letter-spacing:.5px;cursor:pointer;transition:background .2s;">ADD TO INQUIRY LIST</button>
      </div>
    </div>
  </div>
</div>

<style>
@keyframes modalIn { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
#prodDetailModal::-webkit-scrollbar{width:6px}
#prodDetailModal::-webkit-scrollbar-thumb{background:#ddd;border-radius:3px}
#prodDetailSpecsTable tr:nth-child(even) td{background:#f9f9f9}
#prodDetailSpecsTable td{padding:6px 10px;border-bottom:1px solid #eee;}
#prodDetailSpecsTable td:first-child{font-weight:600;color:#555;width:45%;}
@media(max-width:600px){
  #prodDetailModal>div{flex-direction:column!important}
  #prodDetailImgWrap{max-width:100%!important;flex:0 0 auto!important;border-radius:16px 16px 0 0!important;min-height:200px!important}
}
</style>

<script>
(function(){
    var overlay          = document.getElementById('prodDetailOverlay');
    var closeBtn         = document.getElementById('prodDetailClose');
    var detailInquiryBtn = document.getElementById('prodDetailInquiry');
    var currentDetailItem = {};

    /* ── Open ── */
    window.openProductModal = function(card) {
        var model    = card.getAttribute('data-model')  || '';
        var type     = card.getAttribute('data-type')   || '';
        var imgSrc   = card.getAttribute('data-image')  || '';
        var brand    = (typeof BRAND_NAME !== 'undefined') ? BRAND_NAME : '';
        var specsRaw = card.getAttribute('data-specs')  || '[]';
        var specs    = [];
        try { specs = JSON.parse(specsRaw); } catch(e){}

        document.getElementById('prodDetailBrand').textContent = brand;
        document.getElementById('prodDetailName').textContent  = model;
        document.getElementById('prodDetailType').textContent  = type;

        var img   = document.getElementById('prodDetailImg');
        var noImg = document.getElementById('prodDetailNoImg');
        if (imgSrc) {
            img.src = imgSrc; img.style.display = 'block'; noImg.style.display = 'none';
            img.onerror = function(){ img.style.display='none'; noImg.style.display='block'; };
        } else {
            img.style.display = 'none'; noImg.style.display = 'block';
        }

        /* Specs */
        var specsArr  = Array.isArray(specs) ? specs : Object.entries(specs).map(function(e){ return {label:e[0],value:e[1]}; });
        var table     = document.getElementById('prodDetailSpecsTable');
        var specsWrap = document.getElementById('prodDetailSpecsWrap');
        var noSpecs   = document.getElementById('prodDetailNoSpecs');
        table.innerHTML = '';
        if (specsArr.length > 0) {
            specsArr.forEach(function(s){
                var tr = document.createElement('tr');
                tr.innerHTML = '<td>' + (s.label||'') + '</td><td>' + (s.value||'') + '</td>';
                table.appendChild(tr);
            });
            specsWrap.style.display = 'block';
            noSpecs.style.display   = 'none';
        } else {
            specsWrap.style.display = 'none';
            noSpecs.style.display   = 'block';
        }

        currentDetailItem = { model: model, type: type, brand: brand };
        detailInquiryBtn.textContent      = 'ADD TO INQUIRY LIST';
        detailInquiryBtn.style.background = '#2B11DB';

        overlay.style.display    = 'flex';
        document.body.style.overflow = 'hidden';
    };

    /* ── Close ── */
    window.closeProductModal = function() {
        overlay.style.display    = 'none';
        document.body.style.overflow = '';
    };

    /* ── Event listeners ── */
    if (closeBtn) closeBtn.addEventListener('click', closeProductModal);
    overlay.addEventListener('click', function(e){ if (e.target === overlay) closeProductModal(); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeProductModal(); });

    detailInquiryBtn.addEventListener('click', function(){
        var list = [];
        try { list = JSON.parse(localStorage.getItem('inquiryList') || '[]'); } catch(err){}
        var exists = list.some(function(x){ return x.model === currentDetailItem.model && x.brand === currentDetailItem.brand; });
        if (!exists) { list.push(currentDetailItem); localStorage.setItem('inquiryList', JSON.stringify(list)); }
        detailInquiryBtn.textContent      = exists ? 'ALREADY IN LIST' : 'ADDED TO LIST!';
        detailInquiryBtn.style.background = exists ? '#888' : '#00bfb3';
        var badge = document.querySelector('.cart-badge');
        if (badge) { badge.textContent = list.length; badge.classList.toggle('hidden', list.length === 0); }
    });
})();
</script>
