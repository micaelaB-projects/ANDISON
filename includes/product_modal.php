<?php
/**
 * Product Detail Modal
 * Included by brand.php (and any other page that shows clickable product cards).
 * Requires the calling page to set JS global:  BRAND_NAME  before including this file.
 */
?>
<!-- ══ PRODUCT DETAIL MODAL ══ -->
<div id="prodDetailOverlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.6);backdrop-filter:blur(4px);z-index:9999;align-items:flex-start;justify-content:center;padding:20px;box-sizing:border-box;overflow-y:auto;animation:fadeIn .2s ease;">
    <div id="prodDetailModal" style="background:#fff;border-radius:24px;max-width:90vw;width:90vw;max-height:calc(100vh - 40px);height:auto;overflow:hidden;box-shadow:0 25px 70px rgba(0,0,0,.32),0 0 1px rgba(0,0,0,.1);position:relative;animation:modalIn .3s cubic-bezier(0.34, 1.56, 0.64, 1);flex-shrink:0;margin:auto;">
    <button id="prodDetailClose" style="position:absolute;top:18px;right:18px;background:rgba(0,0,0,0.05);border:none;font-size:28px;cursor:pointer;color:#555;line-height:1;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:all .2s;z-index:10;\">&times;</button>
    
    <div style="display:flex;gap:0;flex-wrap:wrap;height:100%;">
      <!-- LEFT SIDE: Product Images Gallery -->
      <div style="flex:0 0 35%;background:linear-gradient(180deg, #f9f9fc 0%, #f0f3ff 50%, #f8fafd 100%);border-radius:24px 0 0 24px;padding:32px;display:flex;flex-direction:column;gap:22px;position:relative;overflow:hidden;">
        <!-- Decorative top accent -->
        <div style="position:absolute;top:0;right:0;width:120px;height:120px;background:linear-gradient(135deg, rgba(43,17,219,0.06) 0%, transparent 70%);border-radius:0 0 0 100%;pointer-events:none;"></div>
        
        <div id="prodImageGallery" style="height:400px;background:#fff;border-radius:20px;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;flex-shrink:0;box-shadow:0 16px 40px rgba(43,17,219,0.18), 0 0 1px rgba(43,17,219,0.3), inset 0 1px 0 rgba(255,255,255,0.9);border:1px solid rgba(43,17,219,0.12);">
          <img id="prodMainImg" src="" alt="" style="max-width:100%;max-height:100%;object-fit:contain;padding:24px;width:100%;">
          <i id="prodNoImg" class="bi bi-tools" style="display:none;font-size:80px;color:#d4d9e6;"></i>
        </div>
        
        <!-- Image thumbnails carousel -->
        <div id="prodThumbnailsWrap" style="display:none;background:linear-gradient(135deg, rgba(43,17,219,0.05) 0%, rgba(43,17,219,0.02) 100%);border-radius:16px;padding:12px;border:1.5px solid rgba(43,17,219,0.1);flex-shrink:0;">
          <div id="prodThumbnails" style="display:flex;gap:12px;flex-wrap:wrap;max-height:85px;overflow-y:auto;justify-content:center;"></div>
        </div>
        
        <!-- Datasheet button -->
        <div id="prodDatasheetWrap" style="display:none;flex-shrink:0;position:relative;z-index:2;">
          <a id="prodDatasheetBtn" href="" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:10px;background:linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);color:#fff;padding:13px 20px;border-radius:14px;font-size:11px;font-weight:950;text-decoration:none;box-shadow:0 8px 22px rgba(43,17,219,0.3);transition:all 0.35s cubic-bezier(0.34,1.56,0.64,1);width:100%;border:1px solid rgba(255,255,255,0.2);letter-spacing:1px;text-transform:uppercase;position:relative;overflow:hidden;" onmouseover="this.style.boxShadow='0 12px 32px rgba(43,17,219,0.45)';this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 8px 22px rgba(43,17,219,0.3)';this.style.transform='translateY(0)';">
            <i class="bi bi-file-pdf" style="font-size:13px;"></i>
            <span>DATASHEET</span>
          </a>
        </div>
        
        <!-- Related Products -->
        <div id="relatedProductsWrap" style="display:none;flex-shrink:0;position:relative;z-index:2;">
          <div style="font-size:9px;font-weight:950;color:#2B11DB;letter-spacing:1.5px;margin-bottom:12px;text-transform:uppercase;display:flex;align-items:center;gap:6px;"><i class="bi bi-boxes"></i> MORE FROM THIS BRAND</div>
          <div id="relatedProductsGrid" style="display:grid;grid-template-columns:repeat(2, 1fr);gap:10px;"></div>
        </div>
      </div>
      
      <!-- Decorative divider line -->
      <div style="position:absolute;left:35%;top:0;bottom:0;width:1px;background:linear-gradient(180deg, transparent 0%, rgba(43,17,219,0.1) 30%, rgba(43,17,219,0.1) 70%, transparent 100%);pointer-events:none;"></div>
      
      <!-- RIGHT SIDE: Product Information -->
      <div style="flex:0 0 65%;display:flex;flex-direction:column;overflow:hidden;">
        <!-- Scrollable content area -->
        <div style="flex:1 1 auto;overflow-y:auto;padding:42px 48px 24px;min-height:0;">
          <!-- Header Section -->
          <div style="margin-bottom:24px;padding-bottom:20px;border-bottom:2px solid #f0f4ff;">
            <span id="prodDetailBrand" style="font-size:9px;font-weight:950;color:#2B11DB;letter-spacing:2px;text-transform:uppercase;margin-bottom:8px;display:block;background:linear-gradient(135deg, rgba(43,17,219,0.12) 0%, rgba(43,17,219,0.06) 100%);padding:8px 14px;border-radius:8px;width:fit-content;border-left:3px solid #2B11DB;"></span>
            <h2 id="prodDetailName" style="font-size:42px;font-weight:980;color:#0a0a1a;margin:10px 0 12px;line-height:1.15;letter-spacing:-1px;text-shadow:0 2px 8px rgba(0,0,0,0.08);"></h2>
            <span id="prodDetailType" style="display:inline-block;background:linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);color:#fff;font-size:10px;font-weight:950;padding:10px 20px;border-radius:24px;text-transform:uppercase;letter-spacing:1.2px;box-shadow:0 6px 16px rgba(43,17,219,0.28);"></span>
          </div>
          
          <!-- Description -->
          <div id="prodDescSection" style="display:none;margin-bottom:22px;padding:18px 20px;background:linear-gradient(135deg, rgba(43,17,219,0.06) 0%, rgba(43,17,219,0.03) 100%);border-radius:14px;border-left:4px solid #2B11DB;">
            <div style="font-size:9px;font-weight:950;color:#2B11DB;letter-spacing:1.2px;margin-bottom:10px;text-transform:uppercase;display:flex;align-items:center;gap:6px;"><i class="bi bi-file-text"></i> OVERVIEW</div>
            <p id="prodDetailDesc" style="font-size:13px;color:#444;line-height:1.8;margin:0;font-weight:500;"></p>
          </div>
          
          <!-- Specs table -->
          <div id="prodSpecsSection" style="display:none;margin-bottom:24px;">
            <div style="font-size:9px;font-weight:950;color:#2B11DB;letter-spacing:1.2px;margin-bottom:14px;text-transform:uppercase;display:flex;align-items:center;gap:6px;"><i class="bi bi-speedometer2"></i> SPECIFICATIONS</div>
            <div style="border-radius:14px;box-shadow:0 6px 18px rgba(43,17,219,0.12);overflow:hidden;border:1px solid rgba(43,17,219,0.08);">
              <table id="prodDetailSpecsTable" style="width:100%;border-collapse:collapse;font-size:12px;"></table>
            </div>
          </div>
          
          <!-- Additional Details -->
          <div id="prodAdditionalDetails" style="display:none;margin-bottom:24px;">
            <div id="prodAdditionalContent"></div>
          </div>
          
          <!-- Default message -->
          <div id="prodNoDetails" style="font-size:12px;color:#9a9fb5;line-height:1.8;font-style:italic;">
            <p style="margin:0;">Complete specifications and datasheets available upon request.</p>
          </div>
        </div>

        <!-- PINNED Button footer — always visible -->
        <div style="flex-shrink:0;padding:16px 48px 28px;border-top:1px solid #f0f0fa;background:#fff;">
          <button id="prodDetailInquiry" style="width:100%;padding:16px 22px;background:linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);color:#fff;border:none;border-radius:14px;font-size:12px;font-weight:950;letter-spacing:1.2px;cursor:pointer;transition:all .4s cubic-bezier(0.34,1.56,0.64,1);text-transform:uppercase;box-shadow:0 10px 28px rgba(43,17,219,0.38);position:relative;overflow:hidden;transform:translateY(0);" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 14px 36px rgba(43,17,219,0.5)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 28px rgba(43,17,219,0.38)';">ADD TO INQUIRY LIST</button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
@keyframes fadeIn { from{opacity:0} to{opacity:1} }
@keyframes modalIn { from{opacity:0;transform:scale(.92) translateY(20px)} to{opacity:1;transform:scale(1) translateY(0)} }
@keyframes thumbnailSlide { from{transform:translateY(10px);opacity:0} to{transform:translateY(0);opacity:1} }

#prodDetailModal {
  display: flex !important;
  overflow: hidden !important;
  flex-direction: column !important;
}
#prodDetailModal::-webkit-scrollbar{width:10px}
#prodDetailModal::-webkit-scrollbar-track{background:transparent}
#prodDetailModal::-webkit-scrollbar-thumb{background:linear-gradient(180deg, #2B11DB 0%, #1f0aa1 100%);border-radius:10px}
#prodDetailModal::-webkit-scrollbar-thumb:hover{background:#b8c1d6}

/* Two-column layout */
#prodDetailModal > div {
  display: flex !important;
  gap: 0 !important;
  width: 100% !important;
  flex: 1 1 auto !important;
  overflow-y: auto !important;
  min-height: 0 !important;
}

#prodDetailModal > div > div:first-child {
  flex: 0 0 35%;
  background: linear-gradient(180deg, #f9f9fc 0%, #f0f4ff 100%);
  border-radius:24px 0 0 24px;
  padding:32px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

#prodDetailModal > div > div:last-child {
  flex: 0 0 65%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

#prodImageGallery {
  height:400px;
  background: #fff;
  border-radius:20px;
  display:flex;
  align-items:center;
  justify-content:center;
  overflow:hidden;
  position:relative;
  flex-shrink:0;
  box-shadow: 0 16px 40px rgba(43,17,219,0.18), 0 0 1px rgba(43,17,219,0.3), inset 0 1px 0 rgba(255,255,255,0.9);
  border:1px solid rgba(43,17,219,0.12);
}

#prodImageGallery::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 30% 20%, rgba(255,255,255,0.6) 0%, transparent 50%);
  pointer-events: none;
  opacity: 0.4;
}

#prodMainImg { 
  max-width:100%;
  max-height:400px;
  object-fit:contain;
  padding:32px;
  width:100%;
  animation: modalIn 0.4s ease-out;
  position: relative;
  z-index: 1;
  filter: drop-shadow(0 8px 20px rgba(0,0,0,0.06));
}#prodDetailSpecsTable { width:100%; border-collapse:collapse; background: #fff; }
#prodDetailSpecsTable thead { 
  background: linear-gradient(90deg, #2B11DB 0%, #1f0aa1 100%);
  position: relative;
  z-index: 10;
}

#prodDetailSpecsTable th {
  padding:13px 12px;
  text-align:left;
  border-bottom:1px solid rgba(255,255,255,0.3);
  color:#fff;
  font-size:18px;
  font-weight:950;
  text-transform:uppercase;
  letter-spacing:1px;
  font-weight: bold;
}
#prodDetailSpecsTable tbody tr { border-bottom:1px solid #f1f3f8; transition:all 0.2s ease; }
#prodDetailSpecsTable tbody tr:hover { background:linear-gradient(90deg, rgba(43,17,219,0.04) 0%, rgba(43,17,219,0.02) 100%); }
#prodDetailSpecsTable tbody tr:nth-child(odd) { background:#fbfbfd; }
#prodDetailSpecsTable tbody tr:nth-child(even) { background:#fff; }
#prodDetailSpecsTable td {
  padding:11px 12px;
  border-bottom:1px solid #f1f3f8;
  color:#3a4559;
  font-size:18px;
  line-height:1.5;
  font-weight:500;
}
#prodDetailSpecsTable td:first-child{
  font-weight:900;
  color:#2B11DB;
  background:linear-gradient(90deg, rgba(43,17,219,0.06) 0%, rgba(43,17,219,0.03) 100%);
  width:32%;
  font-size:18px;
  letter-spacing:0.2px;
}
#prodDetailSpecsTable tbody tr:nth-child(odd) td:first-child {
  background:linear-gradient(90deg, rgba(43,17,219,0.1) 0%, rgba(43,17,219,0.04) 100%);
}
#prodDetailSpecsTable tbody tr:last-child td { border-bottom:none; }

#prodThumbnails img {
  width:68px;
  height:68px;
  object-fit:contain;
  padding:8px;
  border-radius:12px;
  cursor:pointer;
  transition:all 0.38s cubic-bezier(0.34, 1.56, 0.64, 1);
  border:2px solid rgba(43,17,219,0.08);
  background: linear-gradient(135deg, #fff 0%, #f9fafc 100%);
  animation:thumbnailSlide 0.3s ease-out;
  flex-shrink:0;
  box-shadow: 0 6px 16px rgba(43,17,219,0.12);
}
#prodThumbnails img:hover {
  border-color:#2B11DB;
  background: linear-gradient(135deg, #f0f4ff 0%, #fff 100%);
  transform:scale(1.12);
  box-shadow: 0 10px 28px rgba(43,17,219,0.28);
  filter: drop-shadow(0 4px 12px rgba(43,17,219,0.2));
}
#prodThumbnails img.active {
  border-color:#2B11DB;
  background: linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);
  box-shadow: 0 10px 30px rgba(43,17,219,0.42);
  opacity:1;
  transform:scale(1.08);
  filter: drop-shadow(0 6px 16px rgba(43,17,219,0.35));
}

#prodDatasheetBtn:hover {
  transform:translateY(-2px);
  box-shadow:0 6px 16px rgba(43,17,219,0.35)!important;
}

.prod-detail-label {
  font-size:11px;
  font-weight:800;
  color:#5a6278;
  letter-spacing:0.6px;
  text-transform:uppercase;
  display:block;
  margin-bottom:12px;
}

/* Scrollbar Styling for all scrollable areas */
[style*="overflow-y:auto"]::-webkit-scrollbar {
  width: 10px;
}
[style*="overflow-y:auto"]::-webkit-scrollbar-track {
  background: transparent;
}
[style*="overflow-y:auto"]::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #2B11DB 0%, #1f0aa1 100%);
  border-radius: 10px;
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
}
[style*="overflow-y:auto"]::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, #1f0aa1 0%, #2B11DB 100%);
}

@media(max-width:900px){
  #prodDetailModal{max-width:92vw!important;width:92vw!important;max-height:90vh!important}
  #prodDetailName{font-size:28px!important}
  #prodDetailModal > div > div:first-child { flex: 0 0 38% !important; padding: 28px !important; }
  #prodDetailModal > div > div:last-child { flex: 0 0 62% !important; padding: 36px 40px !important; }
  #prodImageGallery { height: 340px !important; }
  #prodMainImg { max-height: 340px !important; padding: 28px !important; }
  #prodThumbnails img { width: 60px !important; height: 60px !important; }
  #relatedProductsGrid { grid-template-columns: repeat(2, 1fr) !important; gap: 8px !important; }
}

@media(max-width:768px){
  #prodDetailModal{border-radius:24px 24px 0 0!important;max-height:95vh!important;width:100%!important;max-width:100%!important;height:auto!important;}
  #prodDetailModal > div {
    flex-wrap: wrap !important;
  }
  #prodDetailModal > div > div:first-child { flex: 0 0 100% !important; border-radius: 0 !important; padding: 28px !important; margin-bottom: 8px; }
  #prodDetailModal > div > div:last-child { flex: 0 0 100% !important; padding: 28px !important; }
  #prodImageGallery{height:300px!important}
  #prodMainImg{max-height:300px!important;padding:24px!important}
  #prodDetailName{font-size:32px!important}
  #prodThumbnails img { width:54px !important; height:54px !important; }
  #relatedProductsGrid { grid-template-columns: repeat(3, 1fr) !important; gap: 8px !important; }
}

/* Related Products Styling */
.related-product-item {
  position: relative;
  border-radius: 12px;
  background: linear-gradient(135deg, #fff 0%, #f9fafc 100%);
  border: 1px solid rgba(43,17,219,0.08);
  overflow: hidden;
  cursor: pointer;
  transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
  box-shadow: 0 4px 12px rgba(43,17,219,0.1);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 10px;
  min-height: 100px;
}

.related-product-item:hover {
  border-color: #2B11DB;
  box-shadow: 0 8px 24px rgba(43,17,219,0.22);
  transform: translateY(-4px);
}

.related-product-item img {
  width: 90%;
  height: 70px;
  object-fit: contain;
  transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.related-product-item:hover img {
  transform: scale(1.08);
}

.related-product-name {
  font-size: 9px;
  font-weight: 800;
  color: #2B11DB;
  text-align: center;
  margin-top: 6px;
  line-height: 1.2;
  letter-spacing: 0.3px;
}
</style>

<script>
(function(){
    var overlay          = document.getElementById('prodDetailOverlay');
    var closeBtn         = document.getElementById('prodDetailClose');
    var detailInquiryBtn = document.getElementById('prodDetailInquiry');
    var currentDetailItem = {};
    var productImages = [];
    var currentImageIndex = 0;
    var modalSliderTimer = null;

    function startModalSlider() {
        stopModalSlider();
        if (productImages.length < 2) return;
        function tick() {
            var mainImg = document.getElementById('prodMainImg');
            if (!mainImg) return;
            mainImg.style.transition = 'opacity 0.4s';
            mainImg.style.opacity = '0';
            setTimeout(function() {
                currentImageIndex = (currentImageIndex + 1) % productImages.length;
                switchImage(currentImageIndex);
                mainImg.style.opacity = '1';
                modalSliderTimer = setTimeout(tick, 2500);
            }, 420);
        }
        modalSliderTimer = setTimeout(tick, 2000);
    }

    function stopModalSlider() {
        if (modalSliderTimer) { clearTimeout(modalSliderTimer); modalSliderTimer = null; }
        var mainImg = document.getElementById('prodMainImg');
        if (mainImg) { mainImg.style.transition = ''; mainImg.style.opacity = '1'; }
    }

    var _jsonPath = (typeof MODAL_JSON_PATH !== 'undefined') ? MODAL_JSON_PATH : 'Andison/data/brands_info.json';

    /* Generation counter – incremented on every openProductModal call so that
       stale XHR responses from a previously-opened product are discarded. */
    var _detailGen = 0;

    /* Load product details from JSON data */
    function loadProductDetails(brand, model) {
        var myGen = ++_detailGen;
        fetch(_jsonPath + '?v=' + Date.now())
            .then(function(r) { return r.json(); })
            .then(function(data) {
                // Discard if a newer openProductModal call has already fired
                if (myGen !== _detailGen) return;
                var brandData = data[brand];
                // Case-insensitive brand lookup
                if (!brandData) {
                    var lc = brand.toLowerCase();
                    for (var k in data) { 
                        if (k.toLowerCase() === lc) { 
                            brandData = data[k]; 
                            break; 
                        } 
                    }
                }
                if (!brandData || !brandData.products) return;
                
                // Case-insensitive model lookup
                var product = null;
                var modelLower = model.toLowerCase().trim();
                for (var i = 0; i < brandData.products.length; i++) {
                    if (brandData.products[i].model && brandData.products[i].model.toLowerCase().trim() === modelLower) {
                        product = brandData.products[i];
                        break;
                    }
                }
                if (!product) return;
                
                // Load description
                if (product.description) {
                    document.getElementById('prodDetailDesc').textContent = product.description;
                    document.getElementById('prodDescSection').style.display = 'block';
                }
                
                // Load specs from JSON if available
                if (product.specs) {
                    var specsArr = [];
                    if (Array.isArray(product.specs)) {
                        specsArr = product.specs;
                    } else if (typeof product.specs === 'object') {
                        // Convert object to array of {label, value}
                        for (var key in product.specs) {
                            if (product.specs.hasOwnProperty(key)) {
                                specsArr.push({ label: key, value: product.specs[key] });
                            }
                        }
                    }
                    
                    if (specsArr.length > 0) {
                        var table = document.getElementById('prodDetailSpecsTable');
                        var specsSection = document.getElementById('prodSpecsSection');
                        table.innerHTML = '';
                        
                        var thead = document.createElement('thead');
                        var tbody = document.createElement('tbody');
                        var isMultiColumn = false;
                        
                        // Check for multi-column format
                        for (var i = 0; i < specsArr.length; i++) {
                            if (specsArr[i].value && specsArr[i].value.toString().includes('|')) {
                                isMultiColumn = true;
                                break;
                            }
                        }
                        
                        if (isMultiColumn) {
                            // Multi-column format
                            var headerTr = document.createElement('tr');
                            headerTr.style.fontWeight = '700';
                            headerTr.style.backgroundColor = '#e8eeff';
                            
                            for (var i = 0; i < specsArr.length; i++) {
                                var spec = specsArr[i];
                                if (spec.label === '' || !spec.value) continue;
                                var th = document.createElement('th');
                                th.textContent = spec.label;
                                th.style.padding = '12px 14px';
                                th.style.textAlign = 'left';
                                th.style.borderBottom = '2px solid #2B11DB';
                                th.style.color = '#2B11DB';
                                th.style.fontSize = '12px';
                                headerTr.appendChild(th);
                            }
                            thead.appendChild(headerTr);
                            
                            var maxRows = 1;
                            for (var i = 0; i < specsArr.length; i++) {
                                if (specsArr[i].value) {
                                    var rows = specsArr[i].value.toString().split('|').length;
                                    if (rows > maxRows) maxRows = rows;
                                }
                            }
                            
                            for (var rowIdx = 0; rowIdx < maxRows; rowIdx++) {
                                var tr = document.createElement('tr');
                                if (rowIdx % 2 === 0) tr.style.backgroundColor = '#f8fafb';
                                
                                for (var i = 0; i < specsArr.length; i++) {
                                    var spec = specsArr[i];
                                    if (spec.label === '' || !spec.value) continue;
                                    var values = spec.value.toString().split('|').map(function(v) { return v.trim(); });
                                    var td = document.createElement('td');
                                    td.textContent = values[rowIdx] || '';
                                    td.style.padding = '12px 14px';
                                    td.style.borderBottom = '1px solid #e8ecf4';
                                    td.style.fontSize = '13px';
                                    tr.appendChild(td);
                                }
                                tbody.appendChild(tr);
                            }
                            table.appendChild(thead);
                        } else {
                            // Simple 2-column format
                            specsArr.forEach(function(s){
                                if (s.label === '') return;
                                var tr = document.createElement('tr');
                                var td1 = document.createElement('td');
                                var td2 = document.createElement('td');
                                td1.textContent = s.label;
                                td2.textContent = s.value;
                                td1.style.fontWeight = '700';
                                td1.style.color = '#2d3748';
                                td1.style.backgroundColor = 'rgba(43,17,219,0.04)';
                                tr.appendChild(td1);
                                tr.appendChild(td2);
                                tbody.appendChild(tr);
                            });
                        }
                        
                        table.appendChild(tbody);
                        specsSection.style.display = 'block';
                    }
                }
            })
            .catch(function(){});
    }

    /* Helper to ensure image path is absolute from assets if not already */
    function normalizeImagePath(path) {
      if (!path || typeof path !== 'string') return '';
      path = path.trim();
      if (path.startsWith('data:') || path.startsWith('http://') || path.startsWith('https://')) return path;
      // If already starts with 'assets/' or '/assets/', return as is (remove leading slash for '/assets/')
      if (path.startsWith('assets/')) return path;
      if (path.startsWith('/assets/')) return path.substring(1);
      // Remove leading './' or '/'
      path = path.replace(/^\.?\/?/, '');
      return 'assets/' + path;
    }

    /* Load all product images from card data */
    function loadProductImagesFromCard(images) {
      // Filter out empty, null, or undefined images
      productImages = (images && images.length > 0 ? images : []).filter(function(img) {
        return img && typeof img === 'string' && img.trim() !== '';
      }).map(normalizeImagePath);
      currentImageIndex = 0;
      var mainImg = document.getElementById('prodMainImg');
      var noImg = document.getElementById('prodNoImg');
      var thumbnailsWrap = document.getElementById('prodThumbnails');
      thumbnailsWrap.innerHTML = '';

      if (productImages.length === 0) {
        // Show default SVG in main image if no images at all
        mainImg.src = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22200%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22300%22 height=%22200%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22 font-size=%2220%22%3ENo Image%3C/text%3E%3C/svg%3E';
        mainImg.style.display = 'block';
        noImg.style.display = 'none';
        document.getElementById('prodThumbnailsWrap').style.display = 'none';
        return;
      }

      // Show main image
      mainImg.src = productImages[0];
      mainImg.style.display = 'block';
      noImg.style.display = 'none';

      // Add thumbnails
      productImages.forEach(function(path, idx) {
        var thumb = document.createElement('img');
        thumb.src = path;
        thumb.onclick = function() { switchImageManual(idx); };
        if (idx === 0) thumb.classList.add('active');
        thumbnailsWrap.appendChild(thumb);
      });

      // Show thumbnails only if more than one image
      if (productImages.length > 1) {
        document.getElementById('prodThumbnailsWrap').style.display = 'block';
        startModalSlider();
      } else {
        document.getElementById('prodThumbnailsWrap').style.display = 'none';
        stopModalSlider();
      }
    }

    /* Switch between product images */
    function switchImage(idx) {
        if (idx < 0 || idx >= productImages.length) return;
        currentImageIndex = idx;
        document.getElementById('prodMainImg').src = productImages[idx];
        
        // Update active thumbnail
        var thumbnails = document.querySelectorAll('#prodThumbnails img');
        thumbnails.forEach(function(t, i) {
            if (i === idx) t.classList.add('active');
            else t.classList.remove('active');
        });
    }

    /* Manual thumbnail click — reset slider timer so auto doesn't fight with user */
    function switchImageManual(idx) {
        stopModalSlider();
        switchImage(idx);
        startModalSlider();
    }

    /* Load related products - FROM SAME BRAND ONLY, using DOM cards as source */
    function loadRelatedProducts(currentBrand, currentModel) {
        var grid = document.getElementById('relatedProductsGrid');
        var wrap = document.getElementById('relatedProductsWrap');
        grid.innerHTML = '';

        var currentBrandLower = currentBrand.toLowerCase().trim();
        var currentModelLower = currentModel.toLowerCase().trim();
        var fallbackImg = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%2270%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22100%22 height=%2270%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22 font-size=%2212%22%3ENo Image%3C/text%3E%3C/svg%3E';

        // Collect all page cards that belong to the same brand (excluding current model)
        var allPageCards = document.querySelectorAll('.brand-product-card, .product-card');
        var sameBrandCards = [];
        for (var i = 0; i < allPageCards.length; i++) {
            var c = allPageCards[i];
            // data-brand may be on the card itself or on the inner inquiry button
            var cBrand = (c.getAttribute('data-brand') ||
                          (c.querySelector('[data-brand]') ? c.querySelector('[data-brand]').getAttribute('data-brand') : '') || '').toLowerCase().trim();
            var cModel = (c.getAttribute('data-model') || '').toLowerCase().trim();
            if (cBrand === currentBrandLower && cModel !== currentModelLower) {
                sameBrandCards.push(c);
            }
        }

        if (sameBrandCards.length === 0) {
            wrap.style.display = 'none';
            return;
        }

        // Shuffle and take up to 4
        sameBrandCards.sort(function() { return Math.random() - 0.5; });
        var relatedCards = sameBrandCards.slice(0, 4);

        relatedCards.forEach(function(pageCard) {
            var model = pageCard.getAttribute('data-model') || '';
            var item = document.createElement('div');
            item.className = 'related-product-item';
            item.style.cursor = 'pointer';

            var img = document.createElement('img');
            img.style.maxWidth = '100%';
            img.style.maxHeight = '70px';
            img.style.objectFit = 'contain';

            // Get image from page card
            var pageImg = pageCard.querySelector('img');
            var imgSrc = (pageImg && pageImg.src) ? pageImg.src : (pageCard.getAttribute('data-image') || '');
            img.src = imgSrc || fallbackImg;
            img.onerror = function() { this.onerror = null; this.src = fallbackImg; };

            var name = document.createElement('div');
            name.className = 'related-product-name';
            name.textContent = model;

            item.appendChild(img);
            item.appendChild(name);

            (function(card) {
                item.onclick = function(e) {
                    e.stopPropagation();
                    openProductModal(card);
                };
            })(pageCard);

            item.onmouseover = function() { item.style.transform = 'translateY(-4px)'; };
            item.onmouseout  = function() { item.style.transform = 'translateY(0)'; };

            grid.appendChild(item);
        });

        wrap.style.display = 'block';
    }

    /* ── Open ── */
    window.openProductModal = function(card) {
        var model    = card.getAttribute('data-model')  || '';
        var type     = card.getAttribute('data-type')   || '';
        var imgSrc   = card.getAttribute('data-image')  || '';
        var brand    = card.getAttribute('data-brand') || (typeof BRAND_NAME !== 'undefined' ? BRAND_NAME : '');
        var specsRaw = card.getAttribute('data-specs')  || '[]';
        var imagesRaw = card.getAttribute('data-images') || '[]';
        var specs    = [];
        var images   = [];
        console.log('MODAL OPEN - specsRaw:', specsRaw, 'model:', model, 'brand:', brand);
        try { specs = JSON.parse(specsRaw); } catch(e){console.log('Failed to parse specs:', e);}
        try { images = JSON.parse(imagesRaw); } catch(e){}
        console.log('Parsed specs:', specs, 'specs length:', Array.isArray(specs) ? specs.length : 'not array');

        // Show brand logo using PHP endpoint to guarantee match
        var brandElem = document.getElementById('prodDetailBrand');
        var logoApi = 'includes/brand_logo_resolver.php?brand=' + encodeURIComponent(brand);
        fetch(logoApi)
          .then(function(r) { return r.text(); })
          .then(function(path) {
            brandElem.innerHTML = '';
            if (path && path.trim() && !path.match(/^\s*notfound/i)) {
              var img = document.createElement('img');
              img.src = path.trim();
              img.alt = brand + ' logo';
              img.title = path.trim();
              img.style.maxHeight = '38px';
              img.style.maxWidth = '120px';
              img.style.display = 'inline-block';
              img.style.verticalAlign = 'middle';
              img.style.marginRight = '8px';
              brandElem.appendChild(img);
            } else {
              // Only show brand name if logo is missing
              var span = document.createElement('span');
              span.textContent = brand;
              span.style.fontWeight = 'bold';
              span.style.fontSize = '15px';
              span.style.verticalAlign = 'middle';
              brandElem.appendChild(span);
            }
          })
          .catch(function() {
            brandElem.innerHTML = '';
            var span = document.createElement('span');
            span.textContent = brand;
            span.style.fontWeight = 'bold';
            span.style.fontSize = '15px';
            span.style.verticalAlign = 'middle';
            brandElem.appendChild(span);
          });
        // Also set textContent for accessibility/SEO
        brandElem.setAttribute('data-brand', brand);
        document.getElementById('prodDetailName').textContent  = model;
        document.getElementById('prodDetailType').textContent  = type;


        // Fallback: show description from card attribute if available (while waiting for JSON)
        var descFromAttr = card.getAttribute('data-description');
        if (descFromAttr) {
          document.getElementById('prodDetailDesc').textContent = descFromAttr;
          document.getElementById('prodDescSection').style.display = 'block';
        } else {
          document.getElementById('prodDescSection').style.display = 'none';
        }

        // Fallback: show specs from card attribute if available (for synthetic cards)
        var specsArr  = Array.isArray(specs) ? specs : Object.entries(specs).map(function(e){ return {label:e[0],value:e[1]}; });
        var table     = document.getElementById('prodDetailSpecsTable');
        var specsSection = document.getElementById('prodSpecsSection');
        table.innerHTML = '';
        console.log('specsArr after conversion:', specsArr, 'length:', specsArr.length);
        if (specsArr.length > 0) {
          var thead = document.createElement('thead');
          var tbody = document.createElement('tbody');
          var isMultiColumn = false;
          var colCount = 1;
          for (var i = 0; i < specsArr.length; i++) {
            var _sv = String(specsArr[i].value != null ? specsArr[i].value : '');
            if (_sv.includes('|')) {
              isMultiColumn = true;
              var cols = _sv.split('|').length;
              if (cols > colCount) colCount = cols;
            }
          }
          if (isMultiColumn) {
            var headerTr = document.createElement('tr');
            headerTr.style.fontWeight = '700';
            headerTr.style.backgroundColor = '#e8eeff';
            for (var i = 0; i < specsArr.length; i++) {
              var spec = specsArr[i];
              if (spec.label === '' || !spec.value) continue;
              var th = document.createElement('th');
              th.textContent = spec.label;
              th.style.padding = '12px 14px';
              th.style.textAlign = 'left';
              th.style.borderBottom = '2px solid #2B11DB';
              th.style.color = '#2B11DB';
              th.style.fontSize = '12px';
              headerTr.appendChild(th);
            }
            thead.appendChild(headerTr);
            var maxRows = 1;
            for (var i = 0; i < specsArr.length; i++) {
              var rows = String(specsArr[i].value != null ? specsArr[i].value : '').split('|').length;
              if (rows > maxRows) maxRows = rows;
            }
            for (var rowIdx = 0; rowIdx < maxRows; rowIdx++) {
              var tr = document.createElement('tr');
              if (rowIdx % 2 === 0) {
                tr.style.backgroundColor = '#f8fafb';
              }
              for (var colIdx = 0; colIdx < specsArr.length; colIdx++) {
                var spec = specsArr[colIdx];
                if (spec.label === '' || spec.value == null) continue;
                var values = String(spec.value).split('|').map(function(v) { return v.trim(); });
                var td = document.createElement('td');
                td.textContent = values[rowIdx] || '';
                td.style.padding = '12px 14px';
                td.style.borderBottom = '1px solid #e8ecf4';
                td.style.fontSize = '13px';
                tr.appendChild(td);
              }
              tbody.appendChild(tr);
            }
            table.appendChild(thead);
          } else {
            specsArr.forEach(function(s){
              if (s.label === '') return;
              var tr = document.createElement('tr');
              var td1 = document.createElement('td');
              var td2 = document.createElement('td');
              td1.textContent = s.label;
              td2.textContent = s.value;
              td1.style.fontWeight = '700';
              td1.style.color = '#2d3748';
              td1.style.backgroundColor = 'rgba(43,17,219,0.04)';
              tr.appendChild(td1);
              tr.appendChild(td2);
              tbody.appendChild(tr);
            });
          }
          table.appendChild(tbody);
          specsSection.style.display = 'block';
        } else {
          specsSection.style.display = 'none';
        }

        // Load product details from JSON (will override fallback if available)
        loadProductDetails(brand, model);
        // Load product images from card data
        loadProductImagesFromCard(images);

        /* Load Datasheet from assets folder */
        var datasheetWrap = document.getElementById('prodDatasheetWrap');
        var datasheetBtn = document.getElementById('prodDatasheetBtn');
        datasheetWrap.style.display = 'none';
        
        // Try to find datasheet for this product model
        if (brand && model) {
            var modelCode = model.split(/[\s\-]/).filter(function(p){ return p.match(/^[A-Z0-9]+[-\d]*$/i); })[0] || model.split(/[\s]/)[0];
            
            // Common datasheet patterns to try
            var patterns = [
                'assets/brands%20items/' + brand + '/Datasheet/' + brand + '%20' + modelCode + '.pdf',
                'assets/brands%20items/' + brand + '/Datasheet/Datasheet%20' + modelCode + '.pdf',
                'assets/brands%20items/' + brand + '/Datasheet/' + modelCode + '.pdf',
                'assets/brands%20items/' + brand + '/Datasheet/' + model + '.pdf'
            ];
            
            // Try to find the datasheet
            for (var i = 0; i < patterns.length; i++) {
                (function(pattern) {
                    fetch(pattern.replace(/%20/g, ' '), { method: 'HEAD' })
                        .then(function(r) {
                            if (r.ok) {
                                datasheetBtn.href = pattern;
                                datasheetWrap.style.display = 'block';
                            }
                        })
                        .catch(function(){});
                })(patterns[i]);
            }
        }

        currentDetailItem = { name: model, type: type, brand: brand };
        detailInquiryBtn.textContent      = 'ADD TO INQUIRY LIST';
        detailInquiryBtn.style.background = 'linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%)';
        detailInquiryBtn.style.boxShadow = '0 6px 20px rgba(43,17,219,0.28)';

        // Load related products
        loadRelatedProducts(brand, model);

        // Measure actual header height and position overlay below it
        var header = document.querySelector('header');
        var headerBottom = header ? header.getBoundingClientRect().bottom : 0;
        overlay.style.top = headerBottom + 'px';
        // Update modal max-height to fit in remaining space (minus 40px for top+bottom padding)
        var modal = document.getElementById('prodDetailModal');
        if (modal) modal.style.maxHeight = (window.innerHeight - headerBottom - 40) + 'px';

        overlay.style.display    = 'flex';
        document.body.style.overflow = 'hidden';

        // Scroll modal content back to top whenever a new product is opened
        setTimeout(function(){
            var modalInner = document.querySelector('#prodDetailModal > div');
            if (modalInner) modalInner.scrollTop = 0;
            var leftPanel = document.querySelector('#prodDetailModal > div > div:first-child');
            if (leftPanel) leftPanel.scrollTop = 0;
            var rightPanel = document.querySelector('#prodDetailModal > div > div:last-child');
            if (rightPanel) rightPanel.scrollTop = 0;
        }, 0);
    };

    /* ── Close ── */
    window.closeProductModal = function() {
        stopModalSlider();
        overlay.style.display    = 'none';
        document.body.style.overflow = '';
    };

    /* ── Event listeners ── */
    if (closeBtn) {
        closeBtn.addEventListener('click', closeProductModal);
        closeBtn.addEventListener('mouseenter', function(){ this.style.background = 'rgba(0,0,0,0.1)'; });
        closeBtn.addEventListener('mouseleave', function(){ this.style.background = 'rgba(0,0,0,0.05)'; });
    }
    overlay.addEventListener('click', function(e){ if (e.target === overlay) closeProductModal(); });
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeProductModal(); });

    detailInquiryBtn.addEventListener('click', function(){
        var list = [];
        try { list = JSON.parse(localStorage.getItem('inquiryItems') || '[]'); } catch(err){}
        var exists = list.some(function(x){ return x.name === currentDetailItem.name && x.brand === currentDetailItem.brand; });
        if (!exists) { 
            var itemToAdd = {
                name: currentDetailItem.name || currentDetailItem.model,
                brand: currentDetailItem.brand,
                qty: 1
            };
            list.push(itemToAdd); 
            localStorage.setItem('inquiryItems', JSON.stringify(list)); 
            window.dispatchEvent(new StorageEvent('storage', {
                key: 'inquiryItems',
                newValue: JSON.stringify(list),
                oldValue: JSON.stringify(list.slice(0, -1))
            }));
        }
        if (exists) {
            detailInquiryBtn.textContent = 'ALREADY IN LIST';
            detailInquiryBtn.style.background = 'linear-gradient(135deg, #999 0%, #777 100%)';
            detailInquiryBtn.style.boxShadow = '0 4px 12px rgba(100, 100, 100, 0.25)';
        } else {
            detailInquiryBtn.textContent = 'ADDED TO LIST!';
            detailInquiryBtn.style.background = 'linear-gradient(135deg, #4caf50 0%, #388e3c 100%)';
            detailInquiryBtn.style.boxShadow = '0 4px 12px rgba(76, 175, 80, 0.3)';
        }
        var badge = document.querySelector('.cart-badge');
        if (badge) { badge.textContent = list.length; badge.classList.toggle('hidden', list.length === 0); }
    });
    
    // Button hover effects
    if (detailInquiryBtn) {
        detailInquiryBtn.addEventListener('mouseenter', function() {
            if (this.textContent === 'ADD TO INQUIRY LIST') {
                this.style.transform = 'translateY(-2px)';
            }
        });
        detailInquiryBtn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    }
  // ──────────────── PRODUCT CARD IMAGE SLIDER ────────────────
// (Removed misplaced style append)
  function initProductCardSliders() {
    // Add smooth transition to all product card images (only once)
    if (!document.getElementById('slider-style-tag')) {
      var style = document.createElement('style');
      style.id = 'slider-style-tag';
      style.innerHTML = '[data-images] img { transition: opacity 0.9s cubic-bezier(0.34,1.56,0.64,1) !important; }';
      document.head.appendChild(style);
    }
    var cards = document.querySelectorAll('[data-images]');
    cards.forEach(function(card) {
      var imagesRaw = card.getAttribute('data-images') || '[]';
      var images = [];
      try { images = JSON.parse(imagesRaw); } catch(e){}
      images = (Array.isArray(images) ? images : []).filter(function(img){ return img && typeof img === 'string' && img.trim() !== ''; }).map(normalizeImagePath);
      if (images.length < 2) return; // Only slider if more than 1 image

      var imgTag = card.querySelector('img');
      if (!imgTag) return;

      var idx = 0;
      var timer = null;

      function showImage(i) {
        if (!images[i]) return;
        imgTag.style.transition = 'opacity 0.9s cubic-bezier(0.34,1.56,0.64,1)';
        imgTag.style.opacity = '0';
        setTimeout(function() {
          imgTag.src = images[i];
          imgTag.onload = function() {
            imgTag.style.opacity = '1';
          };
        }, 350);
      }

      function startSlider() {
        if (timer) clearTimeout(timer);
        function tick() {
          idx = (idx + 1) % images.length;
          showImage(idx);
          timer = setTimeout(tick, 2200);
        }
        timer = setTimeout(tick, 2200);
      }

      // Pause slider on hover
      card.addEventListener('mouseenter', function(){ if (timer) clearTimeout(timer); });
      card.addEventListener('mouseleave', function(){ startSlider(); });

      // Init
      showImage(0);
      startSlider();
    });
  }

  // Run on DOMContentLoaded (in case this file is included at the end, still safe)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProductCardSliders);
  } else {
    initProductCardSliders();
  }

})();
</script>
