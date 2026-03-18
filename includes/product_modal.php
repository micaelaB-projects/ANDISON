<?php
/**
 * Product Detail Modal
 * Included by brand.php (and any other page that shows clickable product cards).
 * Requires the calling page to set JS global:  BRAND_NAME  before including this file.
 */

include_once __DIR__ . '/brand_logo_map.php';
$modal_brand_logo_map_json = '{}';
if (isset($brand_logo_map) && is_array($brand_logo_map)) {
    $encoded_brand_logo_map = json_encode($brand_logo_map, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($encoded_brand_logo_map !== false) {
        $modal_brand_logo_map_json = $encoded_brand_logo_map;
    }
}
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
        <!-- (Datasheet button/preview moved to right column) -->
        
        <!-- Related Products -->
        <div id="relatedProductsWrap" style="display:none;flex-shrink:0;position:relative;z-index:2;">
          <div style="font-size:9px;font-weight:950;color:#2B11DB;letter-spacing:1.5px;margin-bottom:12px;text-transform:uppercase;display:flex;align-items:center;gap:6px;"><i class="bi bi-boxes"></i> MORE FROM THIS BRAND</div>
          <div id="relatedProductsGrid" style="display:grid;grid-template-columns:repeat(2, 1fr);grid-template-rows:repeat(2, 1fr);gap:10px;max-height:280px;overflow:hidden;"></div>
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
            <div id="prodDetailSubname" style="display:none;font-size:15px;font-weight:600;color:#555;margin-top:8px;letter-spacing:0.2px;"></div>
            <div id="prodDetailPrice" style="display:none;margin-top:10px;font-size:13px;font-weight:800;color:#2B11DB;background:rgba(43,17,219,0.08);padding:6px 14px;border-radius:20px;width:fit-content;letter-spacing:0.5px;"></div>
            <!-- Datasheet button and preview directly below the line -->
            <div id="prodDatasheetWrap" style="display:none;flex-shrink:0;position:relative;z-index:2;flex-direction:column;gap:12px;margin:24px 0 0 0;">
                            <a id="prodDatasheetBtn" href="" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:10px;background:linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);color:#fff;padding:13px 20px;border-radius:14px;font-size:18px;font-weight:950;text-decoration:none;box-shadow:0 8px 22px rgba(43,17,219,0.3);transition:all 0.35s cubic-bezier(0.34,1.56,0.64,1);max-width:340px;margin:0 auto 12px auto;border:1px solid rgba(255,255,255,0.2);letter-spacing:1px;text-transform:uppercase;position:relative;overflow:hidden;" onmouseover="this.style.boxShadow='0 12px 32px rgba(43,17,219,0.45)';this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 8px 22px rgba(43,17,219,0.3)';this.style.transform='translateY(0)';">
                                <i class="bi bi-file-pdf" style="font-size:18px;"></i>
                <span>DATASHEET</span>
              </a>
            </div>
          </div>
          
          <!-- Description -->
          <div id="prodDescSection" style="display:none;margin-bottom:22px;padding:18px 20px;background:linear-gradient(135deg, rgba(43,17,219,0.06) 0%, rgba(43,17,219,0.03) 100%);border-radius:14px;border-left:4px solid #2B11DB;">
            <div style="font-size:9px;font-weight:950;color:#2B11DB;letter-spacing:1.2px;margin-bottom:10px;text-transform:uppercase;display:flex;align-items:center;gap:6px;"><i class="bi bi-file-text"></i> OVERVIEW</div>
                        <div id="prodDetailDesc" style="font-size:13px;color:#444;line-height:1.8;margin:0;font-weight:500;"></div>
          </div>
          
                    <!-- Specs -->
          <div id="prodSpecsSection" style="display:none;margin-bottom:24px;">
            <div style="font-size:9px;font-weight:950;color:#2B11DB;letter-spacing:1.2px;margin-bottom:14px;text-transform:uppercase;display:flex;align-items:center;gap:6px;"><i class="bi bi-speedometer2"></i> SPECIFICATIONS</div>
                        <div id="prodDetailSpecsText" style="display:none;margin:0 0 12px;padding:12px 14px;border-radius:12px;border-left:4px solid #2B11DB;background:linear-gradient(135deg, rgba(43,17,219,0.06) 0%, rgba(43,17,219,0.02) 100%);font-size:13px;color:#3f4459;line-height:1.75;"></div>
                        <div id="prodSpecsTableWrap" style="display:none;border-radius:14px;box-shadow:0 6px 18px rgba(43,17,219,0.12);overflow:hidden;border:1px solid rgba(43,17,219,0.08);">
              <table id="prodDetailSpecsTable" style="width:100%;border-collapse:collapse;font-size:12px;">
                <thead>
                  <tr>
                    <th style="padding:13px 12px; text-align:left; border-bottom:1px solid rgba(255,255,255,0.3); color:#fff; font-size:10px; font-weight:950; text-transform:uppercase; letter-spacing:1px; font-weight: bold;">
                      <span>Parameter</span>
                    </th>
                    <th style="padding:13px 12px; text-align:left; border-bottom:1px solid rgba(255,255,255,0.3); color:#fff; font-size:10px; font-weight:950; text-transform:uppercase; letter-spacing:1px; font-weight: bold;">
                      <span>Value</span>
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td style="padding:11px 12px; border-bottom:1px solid #f1f3f8; color:#3a4559; font-size:11px; line-height:1.5; font-weight:500;">
                      <span>Parameter</span>
                    </td>
                    <td style="padding:11px 12px; border-bottom:1px solid #f1f3f8; color:#3a4559; font-size:11px; line-height:1.5; font-weight:500;">
                      <span>Value</span>
                    </td>
                  </tr>
                </tbody>
              </table>
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
          <button id="prodDetailInquiry" style="width:100%;padding:16px 22px;background:linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);color:#fff;border:none;border-radius:14px;font-size:18px;font-weight:950;letter-spacing:1.2px;cursor:pointer;transition:all .4s cubic-bezier(0.34,1.56,0.64,1);text-transform:uppercase;box-shadow:0 10px 28px rgba(43,17,219,0.38);position:relative;overflow:hidden;transform:translateY(0);" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 14px 36px rgba(43,17,219,0.5)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 28px rgba(43,17,219,0.38)';">ADD TO INQUIRY LIST</button>
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
  font-size:10px;
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
  font-size:11px;
  line-height:1.5;
  font-weight:500;
}
#prodDetailSpecsTable td:first-child{
  font-weight:900;
  color:#2B11DB;
  background:linear-gradient(90deg, rgba(43,17,219,0.06) 0%, rgba(43,17,219,0.03) 100%);
  width:32%;
  font-size:10.5px;
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

/* Modal description formatter output */
#prodDetailDesc .prod-desc-paragraph {
    margin: 0 0 12px;
    text-align: justify;
    text-justify: inter-word;
    word-break: break-word;
}

#prodDetailDesc .prod-desc-heading {
    margin: 10px 0 6px;
    font-weight: 700;
    color: #3d4254;
}

#prodDetailDesc .prod-desc-list {
    margin: 0 0 10px;
    padding-left: 24px;
    list-style: disc;
}

#prodDetailDesc .prod-desc-list li {
    margin: 0 0 6px;
    line-height: 1.75;
    text-align: justify;
    text-justify: inter-word;
}

#prodDetailSpecsText .prod-specs-list {
    margin: 0;
    padding-left: 20px;
    list-style: disc;
}

#prodDetailSpecsText .prod-specs-list li {
    margin: 0 0 6px;
    line-height: 1.7;
    word-break: break-word;
}

#prodDetailSpecsText .prod-specs-paragraph {
    margin: 0;
    line-height: 1.7;
    word-break: break-word;
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
  #prodDetailName{font-size:36px!important}
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

.related-product-item button,
.related-product-item a {
  display: none !important;
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

    function resolveBasePath() {
        var pathParts = window.location.pathname.split('/').filter(function(part) {
            return part !== '';
        });
        var markers = ['andison', 'andison-1'];
        for (var i = 0; i < pathParts.length; i++) {
            if (markers.indexOf(pathParts[i].toLowerCase()) !== -1) {
                return '/' + pathParts.slice(0, i + 1).join('/');
            }
        }
        return '';
    }

    var _modalBase = resolveBasePath();

    function resolveModalPath(path) {
        var raw = String(path || '').trim();
        if (!raw) return '';

        if (/^(?:https?:)?\/\//i.test(raw) || raw.indexOf('data:') === 0 || raw.indexOf('blob:') === 0) {
            return raw;
        }

        raw = raw.replace(/\\/g, '/');
        if (raw.indexOf('/ANDISON/') === 0) {
            raw = raw.substring('/ANDISON'.length);
        }
        raw = raw.replace(/^\/andison\//i, '/Andison/');
        raw = raw.replace(/^(\.\.\/)+/, '');

        if (raw.indexOf('/') === 0) {
            return (_modalBase !== '' ? _modalBase : '') + raw;
        }

        return (_modalBase !== '' ? _modalBase + '/' : '/') + raw.replace(/^\.\//, '');
    }

    var modalBrandLogoMap = <?php echo $modal_brand_logo_map_json; ?>;

    function getModalBrandLogo(brandName) {
        var cleanBrand = String(brandName || '').trim();
        if (!cleanBrand) return '';

        if (modalBrandLogoMap && Object.prototype.hasOwnProperty.call(modalBrandLogoMap, cleanBrand)) {
            return resolveModalPath(modalBrandLogoMap[cleanBrand]);
        }

        var lowerBrand = cleanBrand.toLowerCase();
        for (var key in modalBrandLogoMap) {
            if (!Object.prototype.hasOwnProperty.call(modalBrandLogoMap, key)) continue;
            if (key.toLowerCase() === lowerBrand) {
                return resolveModalPath(modalBrandLogoMap[key]);
            }
        }

        return '';
    }

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function preserveInlineSpacing(value) {
        var escaped = escapeHtml(value).replace(/\t/g, '    ');
        return escaped.replace(/  +/g, function(spaces) {
            return ' ' + new Array(spaces.length).join('&nbsp;');
        });
    }

    function formatDescriptionHtml(rawText) {
        var normalized = String(rawText || '')
            .replace(/\r\n?/g, '\n')
            .replace(/\u00A0/g, ' ')
            .replace(/[\u200B-\u200D\uFEFF]/g, '');

        var lines = normalized.split('\n');
        var htmlParts = [];
        var paragraphBuffer = [];
        var listBuffer = [];

        function flushParagraph() {
            if (!paragraphBuffer.length) return;
            var paragraphText = paragraphBuffer.join(' ').trim();
            if (paragraphText !== '') {
                htmlParts.push('<p class="prod-desc-paragraph">' + preserveInlineSpacing(paragraphText) + '</p>');
            }
            paragraphBuffer = [];
        }

        function flushList() {
            if (!listBuffer.length) return;
            var listHtml = '<ul class="prod-desc-list">';
            for (var i = 0; i < listBuffer.length; i++) {
                listHtml += '<li>' + preserveInlineSpacing(listBuffer[i]) + '</li>';
            }
            listHtml += '</ul>';
            htmlParts.push(listHtml);
            listBuffer = [];
        }

        for (var i = 0; i < lines.length; i++) {
            var rawLine = lines[i].replace(/\s+$/g, '');
            var trimmed = rawLine.trim();

            if (trimmed === '') {
                flushList();
                flushParagraph();
                continue;
            }

            if (/^features:?$/i.test(trimmed)) {
                flushList();
                flushParagraph();
                htmlParts.push('<p class="prod-desc-heading">' + preserveInlineSpacing(trimmed) + '</p>');
                continue;
            }

            var bulletMatch = rawLine.match(/^\s*(?:[-*•●◦▪▫■□]+|[^\x00-\x7F])\s*(.+)$/);
            if (bulletMatch && bulletMatch[1]) {
                flushParagraph();
                listBuffer.push(bulletMatch[1]);
                continue;
            }

            flushList();
            paragraphBuffer.push(rawLine);
        }

        flushList();
        flushParagraph();

        if (!htmlParts.length) {
            return '<p class="prod-desc-paragraph">' + preserveInlineSpacing(normalized.trim()) + '</p>';
        }

        return htmlParts.join('');
    }

    function formatSpecificationsHtml(rawText) {
        var normalized = String(rawText || '')
            .replace(/\r\n?/g, '\n')
            .replace(/\u00A0/g, ' ')
            .replace(/[\u200B-\u200D\uFEFF]/g, '');

        var lines = normalized.split('\n');
        var items = [];

        for (var i = 0; i < lines.length; i++) {
            var rawLine = lines[i].replace(/\s+$/g, '');
            var trimmed = rawLine.trim();
            if (trimmed === '') continue;

            if (/^specifications?:?$/i.test(trimmed)) {
                continue;
            }

            var bulletMatch = rawLine.match(/^\s*(?:[-*•●◦▪▫■□·]+|[^\x00-\x7F])\s*(.+)$/);
            if (bulletMatch && bulletMatch[1]) {
                items.push(bulletMatch[1].trim());
                continue;
            }

            if (items.length > 0) {
                items[items.length - 1] = (items[items.length - 1] + ' ' + trimmed).trim();
            } else {
                items.push(trimmed);
            }
        }

        if (items.length === 0) {
            var fallback = normalized.trim();
            if (fallback === '') return '';
            return '<p class="prod-specs-paragraph">' + preserveInlineSpacing(fallback) + '</p>';
        }

        var html = '<ul class="prod-specs-list">';
        for (var j = 0; j < items.length; j++) {
            html += '<li>' + preserveInlineSpacing(items[j]) + '</li>';
        }
        html += '</ul>';
        return html;
    }

    function normalizeSpecsArray(specsValue) {
        if (Array.isArray(specsValue)) {
            return specsValue.map(function(item) {
                if (item && typeof item === 'object') {
                    return {
                        label: String(item.label || item.key || '').trim(),
                        value: String(item.value || '').trim(),
                    };
                }
                return { label: '', value: '' };
            }).filter(function(item) {
                return item.label !== '' || item.value !== '';
            });
        }

        if (specsValue && typeof specsValue === 'object') {
            return Object.entries(specsValue).map(function(entry) {
                return {
                    label: String(entry[0] || '').trim(),
                    value: String(entry[1] || '').trim(),
                };
            }).filter(function(item) {
                return item.label !== '' || item.value !== '';
            });
        }

        return [];
    }

    function parseSpecificationPayload(rawText) {
        var payload = {
            text: '',
            table: [],
            matrix: null,
        };

        var source = String(rawText || '').trim();
        if (!source) return payload;

        try {
            var parsed = JSON.parse(source);
            if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                var hasMatrix = parsed.tableMatrix && typeof parsed.tableMatrix === 'object';
                var looksLikeV2 = parsed.format === 'andison_specs_v2' || hasMatrix;

                if (looksLikeV2) {
                    payload.text = String(parsed.text || '').trim();
                    var matrixRaw = parsed.tableMatrix || {};
                    var headers = Array.isArray(matrixRaw.headers)
                        ? matrixRaw.headers.map(function(h){ return String(h || '').trim(); }).filter(function(h){ return h !== ''; })
                        : [];
                    if (headers.length > 0) {
                        var rows = Array.isArray(matrixRaw.rows) ? matrixRaw.rows : [];
                        var normalizedRows = rows.map(function(row) {
                            var out = Array.isArray(row) ? row.slice(0, headers.length) : [];
                            while (out.length < headers.length) out.push('');
                            return out.map(function(cell) { return String(cell || '').trim(); });
                        });

                        var mode = matrixRaw.mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';
                        var groups = [];
                        if (mode === 'grouped-pairs') {
                            var dataCols = Math.max(1, headers.length - 1);
                            var rawGroups = Array.isArray(matrixRaw.groups) ? matrixRaw.groups : [];
                            groups = rawGroups.map(function(g) {
                                if (g && typeof g === 'object' && !Array.isArray(g)) {
                                    var spanObj = parseInt(g.span, 10);
                                    return {
                                        title: String(g.title || g.label || g.name || '').trim(),
                                        span: (isFinite(spanObj) && spanObj > 0) ? spanObj : 1,
                                    };
                                }
                                return { title: String(g || '').trim(), span: 2 };
                            });

                            if (groups.length === 0) {
                                groups = [{ title: 'Free Air', span: dataCols }];
                            }

                            var remaining = dataCols;
                            for (var gi = 0; gi < groups.length; gi++) {
                                var groupsLeft = groups.length - gi - 1;
                                if (gi === groups.length - 1) {
                                    groups[gi].span = Math.max(1, remaining);
                                    remaining = 0;
                                    break;
                                }
                                var maxSpan = Math.max(1, remaining - groupsLeft);
                                var s = groups[gi].span;
                                if (!isFinite(s) || s < 1) s = 1;
                                if (s > maxSpan) s = maxSpan;
                                groups[gi].span = s;
                                remaining -= s;
                            }
                            if (remaining > 0 && groups.length > 0) {
                                groups[groups.length - 1].span += remaining;
                            }

                            groups = groups.map(function(g, idx) {
                                var title = String(g.title || '').trim();
                                if (title === '') title = idx === 0 ? 'Free Air' : ('Group ' + (idx + 1));
                                return { title: title, span: g.span };
                            });
                        }

                        payload.matrix = {
                            mode: mode,
                            headers: headers,
                            rows: normalizedRows,
                            groups: groups,
                        };
                    }
                    return payload;
                }

                var hasTable = Array.isArray(parsed.table);
                var looksLikePayload = parsed.format === 'andison_specs_v1' || hasTable;
                if (looksLikePayload) {
                    payload.text = String(parsed.text || '').trim();
                    if (hasTable) {
                        payload.table = parsed.table.map(function(row) {
                            if (Array.isArray(row)) {
                                return {
                                    label: String(row[0] || '').trim(),
                                    value: String(row[1] || '').trim(),
                                };
                            }
                            if (row && typeof row === 'object') {
                                return {
                                    label: String(row.label || row.key || '').trim(),
                                    value: String(row.value || '').trim(),
                                };
                            }
                            return { label: '', value: '' };
                        }).filter(function(item) {
                            return item.label !== '' || item.value !== '';
                        });
                    }
                    return payload;
                }
            }
        } catch (e) {
            // Plain text specifications are valid and expected.
        }

        payload.text = source;
        return payload;
    }

    function renderSpecMatrixTable(matrix, table) {
        if (!matrix || !Array.isArray(matrix.headers) || matrix.headers.length === 0 || !table) return false;

        table.innerHTML = '';

        var headers = matrix.headers.map(function(h) { return String(h || '').trim(); });
        var rows = Array.isArray(matrix.rows) ? matrix.rows : [];
        var mode = matrix.mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';

        var thead = document.createElement('thead');
        var tbody = document.createElement('tbody');

        if (mode === 'grouped-pairs' && headers.length >= 2) {
            var dataColCount = Math.max(1, headers.length - 1);
            var rawGroups = Array.isArray(matrix.groups) ? matrix.groups : [];
            var groups = rawGroups.map(function(g) {
                if (g && typeof g === 'object' && !Array.isArray(g)) {
                    var spanObj = parseInt(g.span, 10);
                    return {
                        title: String(g.title || g.label || g.name || '').trim(),
                        span: (isFinite(spanObj) && spanObj > 0) ? spanObj : 1,
                    };
                }
                return { title: String(g || '').trim(), span: 2 };
            });

            if (groups.length === 0) {
                groups = [{ title: 'Free Air', span: dataColCount }];
            }

            var remainingCols = dataColCount;
            for (var gi = 0; gi < groups.length; gi++) {
                var groupsLeft = groups.length - gi - 1;
                if (gi === groups.length - 1) {
                    groups[gi].span = Math.max(1, remainingCols);
                    remainingCols = 0;
                    break;
                }

                var maxSpan = Math.max(1, remainingCols - groupsLeft);
                var s = groups[gi].span;
                if (!isFinite(s) || s < 1) s = 1;
                if (s > maxSpan) s = maxSpan;
                groups[gi].span = s;
                remainingCols -= s;
            }

            if (remainingCols > 0 && groups.length > 0) {
                groups[groups.length - 1].span += remainingCols;
            }

            var topTr = document.createElement('tr');

            var firstTop = document.createElement('th');
            firstTop.rowSpan = 2;
            firstTop.textContent = headers[0] || 'Model';
            firstTop.style.padding = '12px 10px';
            firstTop.style.textAlign = 'center';
            firstTop.style.borderBottom = '1px solid #4b5563';
            firstTop.style.background = 'linear-gradient(180deg,#2f3238 0%,#1f2126 100%)';
            firstTop.style.color = '#fff';
            firstTop.style.fontSize = '11px';
            firstTop.style.fontWeight = '900';
            topTr.appendChild(firstTop);

            for (var g = 0; g < groups.length; g++) {
                var gTh = document.createElement('th');
                var span = parseInt(groups[g].span, 10);
                if (!isFinite(span) || span < 1) span = 1;
                gTh.colSpan = span;
                gTh.textContent = groups[g].title || (g === 0 ? 'Free Air' : ('Group ' + (g + 1)));
                gTh.style.padding = '10px 8px';
                gTh.style.textAlign = 'center';
                gTh.style.borderBottom = '1px solid #4b5563';
                gTh.style.background = 'linear-gradient(180deg,#2f3238 0%,#1f2126 100%)';
                gTh.style.color = '#fff';
                gTh.style.fontSize = '11px';
                gTh.style.fontWeight = '900';
                topTr.appendChild(gTh);
            }

            thead.appendChild(topTr);

            var subTr = document.createElement('tr');
            for (var c = 1; c < headers.length; c++) {
                var subTh = document.createElement('th');
                subTh.textContent = headers[c] || ('Column ' + c);
                subTh.style.padding = '8px 8px';
                subTh.style.textAlign = 'center';
                subTh.style.borderBottom = '1px solid #4b5563';
                subTh.style.background = 'linear-gradient(180deg,#26292f 0%,#1a1c21 100%)';
                subTh.style.color = '#f3f4f6';
                subTh.style.fontSize = '10px';
                subTh.style.fontWeight = '800';
                subTr.appendChild(subTh);
            }
            thead.appendChild(subTr);
        } else {
            var headerTr = document.createElement('tr');
            headerTr.style.fontWeight = '700';
            headerTr.style.backgroundColor = '#e8eeff';

            for (var h = 0; h < headers.length; h++) {
                var th = document.createElement('th');
                th.textContent = headers[h] || ('Column ' + (h + 1));
                th.style.padding = '12px 14px';
                th.style.textAlign = 'left';
                th.style.borderBottom = '2px solid #2B11DB';
                th.style.color = '#2B11DB';
                th.style.fontSize = '12px';
                headerTr.appendChild(th);
            }
            thead.appendChild(headerTr);
        }

        var renderedRows = 0;
        for (var r = 0; r < rows.length; r++) {
            var row = Array.isArray(rows[r]) ? rows[r] : [];
            var safeRow = row.slice(0, headers.length);
            while (safeRow.length < headers.length) safeRow.push('');

            var rowHasAnyData = safeRow.some(function(cell) { return String(cell || '').trim() !== ''; });
            if (!rowHasAnyData) continue;

            var tr = document.createElement('tr');
            if (renderedRows % 2 === 0) tr.style.backgroundColor = '#f8fafb';

            for (var col = 0; col < headers.length; col++) {
                var td = document.createElement('td');
                td.textContent = safeRow[col] || '';
                td.style.padding = mode === 'grouped-pairs' ? '10px 10px' : '12px 14px';
                td.style.borderBottom = '1px solid #e8ecf4';
                td.style.fontSize = '13px';
                if (col === 0) {
                    td.style.fontWeight = '700';
                    td.style.color = '#2d3748';
                    td.style.backgroundColor = mode === 'grouped-pairs' ? 'rgba(17,24,39,0.04)' : 'rgba(43,17,219,0.04)';
                }
                tr.appendChild(td);
            }
            tbody.appendChild(tr);
            renderedRows++;
        }

        table.appendChild(thead);
        table.appendChild(tbody);

        return headers.length > 0;
    }

    function renderSpecsTable(specsArr, table, matrix) {
        if (!table) return false;

        if (matrix && renderSpecMatrixTable(matrix, table)) {
            return true;
        }

        table.innerHTML = '';
        if (!Array.isArray(specsArr) || specsArr.length === 0) return false;

        var thead = document.createElement('thead');
        var tbody = document.createElement('tbody');
        var isMultiColumn = false;

        for (var i = 0; i < specsArr.length; i++) {
            if (specsArr[i].value && specsArr[i].value.includes('|')) {
                isMultiColumn = true;
                break;
            }
        }

        if (isMultiColumn) {
            var headerTr = document.createElement('tr');
            headerTr.style.fontWeight = '700';
            headerTr.style.backgroundColor = '#e8eeff';

            for (var h = 0; h < specsArr.length; h++) {
                var hSpec = specsArr[h];
                if (hSpec.label === '' || !hSpec.value) continue;

                var th = document.createElement('th');
                th.textContent = hSpec.label;
                th.style.padding = '12px 14px';
                th.style.textAlign = 'left';
                th.style.borderBottom = '2px solid #2B11DB';
                th.style.color = '#2B11DB';
                th.style.fontSize = '12px';
                headerTr.appendChild(th);
            }
            thead.appendChild(headerTr);

            var maxRows = 1;
            for (var r = 0; r < specsArr.length; r++) {
                var splitLen = specsArr[r].value.split('|').length;
                if (splitLen > maxRows) maxRows = splitLen;
            }

            for (var rowIdx = 0; rowIdx < maxRows; rowIdx++) {
                var tr = document.createElement('tr');
                if (rowIdx % 2 === 0) tr.style.backgroundColor = '#f8fafb';

                for (var colIdx = 0; colIdx < specsArr.length; colIdx++) {
                    var colSpec = specsArr[colIdx];
                    if (colSpec.label === '' || !colSpec.value) continue;

                    var values = colSpec.value.split('|').map(function(v) { return v.trim(); });
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
            specsArr.forEach(function(s) {
                if (s.label === '') return;
                var tr = document.createElement('tr');
                var td1 = document.createElement('td');
                var td2 = document.createElement('td');
                td1.textContent = s.label;
                td2.textContent = s.value || '';
                td1.style.fontWeight = '700';
                td1.style.color = '#2d3748';
                td1.style.backgroundColor = 'rgba(43,17,219,0.04)';
                tr.appendChild(td1);
                tr.appendChild(td2);
                tbody.appendChild(tr);
            });
        }

        table.appendChild(tbody);
        return true;
    }

    function renderSpecifications(specsValue, rawSpecsText) {
        var specsSection = document.getElementById('prodSpecsSection');
        var specsTextEl = document.getElementById('prodDetailSpecsText');
        var tableWrap = document.getElementById('prodSpecsTableWrap');
        var table = document.getElementById('prodDetailSpecsTable');

        if (!specsSection || !table) return false;

        var specsArr = normalizeSpecsArray(specsValue);
        var payload = parseSpecificationPayload(rawSpecsText);
        var hasText = payload.text !== '';

        if (specsTextEl) {
            if (hasText) {
                specsTextEl.innerHTML = formatSpecificationsHtml(payload.text);
                specsTextEl.style.display = 'block';
            } else {
                specsTextEl.innerHTML = '';
                specsTextEl.style.display = 'none';
            }
        }

        var hasTable = false;
        if (payload.matrix) {
            hasTable = renderSpecsTable([], table, payload.matrix);
        } else {
            var tableRows = specsArr.length > 0 ? specsArr : payload.table;
            hasTable = renderSpecsTable(tableRows, table, null);
        }

        if (tableWrap) {
            tableWrap.style.display = hasTable ? 'block' : 'none';
        }

        var hasAny = hasText || hasTable;
        specsSection.style.display = hasAny ? 'block' : 'none';
        return hasAny;
    }

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

    var _jsonPath = resolveModalPath((typeof MODAL_JSON_PATH !== 'undefined') ? MODAL_JSON_PATH : 'Andison/data/brands_info_api.php');

    /* Load product details from JSON data */
    function loadProductDetails(brand, model, fallbackSpecs, fallbackSpecsText) {
        fetch(_jsonPath)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var product = null;
                
                // Check if data is an array (category JSON format) or object (brands_info format)
                if (Array.isArray(data)) {
                    // Category JSON format: [ { model, brand, specs, images, ... } ]
                    product = data.find(function(p) { 
                        return (p.model === model || p.name === model) && 
                               (!brand || p.brand === brand || p.brand.toLowerCase() === brand.toLowerCase());
                    });
                    console.log('Searching in category JSON array format, found:', !!product);
                } else {
                    // brands_info JSON format: { "BRAND": { products: [...] } }
                    var brandData = data[brand];
                    if (!brandData) {
                        // Try case-insensitive brand search
                        var lc = brand.toLowerCase();
                        for (var k in data) { 
                            if (k.toLowerCase() === lc) { 
                                brandData = data[k]; 
                                break; 
                            } 
                        }
                    }
                    
                    if (brandData && brandData.products) {
                        product = brandData.products.find(function(p) { return p.model === model; });
                        console.log('Searching in brands_info format, found:', !!product);
                    }
                }
                
                if (!product) {
                    console.log('Product not found - brand:', brand, 'model:', model);
                    return;
                }
                
                console.log('Found product - loading specs and images');
                
                // Load description
                if (product.description) {
                    document.getElementById('prodDetailDesc').innerHTML = formatDescriptionHtml(product.description);
                    document.getElementById('prodDescSection').style.display = 'block';
                } else {
                    document.getElementById('prodDescSection').style.display = 'none';
                }
                
                // Update specifications (plain text + optional table)
                var specsToRender = (Array.isArray(product.specs) && product.specs.length > 0)
                    ? product.specs
                    : (fallbackSpecs || []);
                var specsTextToRender = product.specifications || fallbackSpecsText || '';
                renderSpecifications(specsToRender, specsTextToRender);
                
                // Only enrich images from JSON if the card provided none (prevents
                // stale JSON cache paths from overwriting the correct Supabase image path)
                if (productImages.length === 0 && (product.image || (product.images && product.images.length > 0))) {
                    var images_to_load = [];
                    if (product.images && Array.isArray(product.images)) {
                        images_to_load = product.images.slice();
                    } else if (product.image) {
                        images_to_load = [product.image];
                    }
                    if (images_to_load.length > 0) {
                        loadProductImagesFromCard(images_to_load);
                    }
                }
            })
            .catch(function(err){
                console.log('Error loading product details from brands_info:', err);
            });
    }

    /* Load all product images from card data */
    function loadProductImagesFromCard(images) {
        productImages = images && images.length > 0 ? images : [];
        currentImageIndex = 0;
        var mainImg = document.getElementById('prodMainImg');
        var noImg = document.getElementById('prodNoImg');
        var thumbnailsWrap = document.getElementById('prodThumbnails');
        thumbnailsWrap.innerHTML = '';
        
        console.log('loadProductImagesFromCard: received', productImages.length, 'images:', productImages);
        
        if (productImages.length === 0) {
            console.log('loadProductImagesFromCard: no images, showing placeholder');
            mainImg.src = '';
            mainImg.style.display = 'none';
            noImg.style.display = 'block';
            document.getElementById('prodThumbnailsWrap').style.display = 'none';
            return;
        }
        
        // Decode URL-encoded paths and make absolute
        var processedImages = productImages.map(function(path) {
            // Decode URL-encoded characters (%20 -> space, etc.)
            var decoded = decodeURIComponent(path);
            
            // Convert relative paths to absolute
            if (/^[aA][nN][dD][iI][sS][oO][nN]\/assets\//i.test(decoded)) {
                // handles Andison/assets/, ANDISON/assets/, andison/assets/ — all cases
                decoded = decoded.replace(/^[^/]+\//, 'Andison/');
            } else if (decoded.indexOf('../') === 0) {
                // Convert relative paths from subdirectories to absolute
                decoded = decoded.replace(/^(\.\.\/)+/, '');
            }
            return resolveModalPath(decoded);
        });
        productImages = processedImages;
        
        // Show main image
        console.log('loadProductImagesFromCard: setting main image to:', productImages[0]);
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
            console.log('loadProductImagesFromCard: showing thumbnails for', productImages.length, 'images');
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

    /* Load related products - FROM SAME BRAND ONLY */
    function loadRelatedProducts(currentBrand, currentModel) {
        var grid = document.getElementById('relatedProductsGrid');
        var wrap = document.getElementById('relatedProductsWrap');
        grid.innerHTML = '';
        
        fetch(_jsonPath)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var allProducts = [];
                
                // Check if data is an array (category JSON format) or object (brands_info format)
                if (Array.isArray(data)) {
                    // Category JSON format: [ { model, brand, ... } ]
                    // Collect products from same brand only
                    for (var i = 0; i < data.length; i++) {
                        var product = data[i];
                        if (product.model && product.model !== currentModel) {
                            // Match by brand (case-insensitive)
                            if (product.brand && product.brand.toLowerCase() === currentBrand.toLowerCase()) {
                                product._brand = currentBrand;
                                allProducts.push(product);
                            }
                        }
                    }
                    console.log('loadRelatedProducts: found', allProducts.length, 'related products in category format');
                } else {
                    // brands_info JSON format: { "BRAND": { products: [...] } }
                    var brandData = data[currentBrand];
                    if (!brandData) {
                        var lc = currentBrand.toLowerCase();
                        for (var k in data) { if (k.toLowerCase() === lc) { brandData = data[k]; break; } }
                    }
                    if (!brandData || !brandData.products) { wrap.style.display = 'none'; return; }

                    // Collect products from same brand only
                    for (var i = 0; i < brandData.products.length; i++) {
                        var product = brandData.products[i];
                        if (product.model && product.model !== currentModel) {
                            product._brand = currentBrand;
                            allProducts.push(product);
                        }
                    }
                    console.log('loadRelatedProducts: found', allProducts.length, 'related products in brands_info format');
                }
                
                if (allProducts.length === 0) {
                    wrap.style.display = 'none';
                    return;
                }
                
                // Shuffle and take first 4 only
                var relatedProducts = allProducts.sort(function() { return Math.random() - 0.5; }).slice(0, 4);
                
                console.log('loadRelatedProducts: displaying', relatedProducts.length, 'items out of', allProducts.length, 'available');
                
                // Safety: limit to exactly 4 items maximum
                relatedProducts = relatedProducts.slice(0, 4);
                
                if (relatedProducts.length === 0) {
                    wrap.style.display = 'none';
                    return;
                }
                
                // Try to find matching cards on page (brand.php uses .brand-product-card)
                var allPageCards = document.querySelectorAll('.product-card, .brand-product-card');
                var cardMap = {};
                for (var i = 0; i < allPageCards.length; i++) {
                    var cardModel = allPageCards[i].getAttribute('data-model') || 
                                   (allPageCards[i].querySelector('h4') ? allPageCards[i].querySelector('h4').textContent.trim() : '');
                    if (cardModel) {
                        cardMap[cardModel] = allPageCards[i];
                    }
                }
                
                // Create items - STRICTLY limit to exactly 4 items - no more
                var createdCount = 0;
                for (var itemIdx = 0; itemIdx < relatedProducts.length; itemIdx++) {
                    if (createdCount >= 4) break; // HARD STOP at 4 items
                    createdCount++;
                    
                    var product = relatedProducts[itemIdx];
                    (function(product) {
                        var item = document.createElement('div');
                        item.className = 'related-product-item';
                        item.style.cursor = 'pointer';
                        
                        var img = document.createElement('img');
                        img.style.maxWidth = '100%';
                        img.style.maxHeight = '70px';
                        img.style.objectFit = 'contain';
                        
                        // Try to get image from page card first, then JSON
                        var pageCard = cardMap[product.model];
                        var imgSrc = '';
                        
                        if (pageCard) {
                            var pageImg = pageCard.querySelector('img');
                            if (pageImg && pageImg.src && pageImg.src.trim() !== '') {
                                imgSrc = pageImg.src;
                            }
                        }
                        
                        if (!imgSrc && product.image && product.image.trim() !== '') {
                            imgSrc = product.image;
                            // Decode percent-encoding (e.g. %20 → space)
                            imgSrc = decodeURIComponent(imgSrc);
                            // Strip leading ../ sequences (stored relative to subdir pages)
                            imgSrc = imgSrc.replace(/^(\.\.\/)+/, '');
                            // Now normalize to absolute /ANDISON/... URL
                            if (/^andison\/assets\/uploads/i.test(imgSrc)) {
                                // Upload path: Andison/assets/uploads/... → /ANDISON/Andison/assets/uploads/...
                                imgSrc = '/ANDISON/' + imgSrc.charAt(0).toUpperCase() + imgSrc.slice(1);
                            } else if (/^andison\//i.test(imgSrc)) {
                                // Brand-item path stored with andison/ prefix — strip it
                                imgSrc = '/ANDISON/' + imgSrc.replace(/^andison\//i, '');
                            } else if (imgSrc.indexOf('assets/') === 0) {
                                imgSrc = '/ANDISON/' + imgSrc;
                            } else if (!imgSrc.startsWith('/') && !imgSrc.startsWith('http')) {
                                imgSrc = '/ANDISON/' + imgSrc;
                            }
                        }

                        if (imgSrc) {
                            imgSrc = resolveModalPath(imgSrc);
                        }
                        
                        img.src = imgSrc || 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%2270%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22100%22 height=%2270%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22 font-size=%2212%22%3ENo Image%3C/text%3E%3C/svg%3E';
                        
                        var name = document.createElement('div');
                        name.className = 'related-product-name';
                        name.textContent = product.model;
                        
                        item.appendChild(img);
                        item.appendChild(name);
                        
                        // Closure to capture correct brand
                        (function(product, pageCard) {
                            item.onclick = function(e) {
                                e.stopPropagation();
                                if (pageCard) {
                                    // Use the actual product card from the page
                                    openProductModal(pageCard);
                                } else {
                                    // Create synthetic card with all product data
                                    var syntheticCard = document.createElement('div');
                                    var productBrand = product._brand || currentBrand;
                                    syntheticCard.setAttribute('data-model', product.model);
                                    syntheticCard.setAttribute('data-type', product.type || '');
                                    syntheticCard.setAttribute('data-brand', productBrand);
                                    syntheticCard.setAttribute('data-image', product.image || '');
                                    syntheticCard.setAttribute('data-badge', product.badge || '');
                                    syntheticCard.setAttribute('data-datasheet', product.datasheet || product.datasheet_url || '');
                                    syntheticCard.setAttribute('data-images', JSON.stringify(product.images || []));
                                    syntheticCard.setAttribute('data-specs', JSON.stringify(product.specs || []));
                                    openProductModal(syntheticCard);
                                }
                            };
                        })(product, pageCard);
                        
                        item.onmouseover = function() {
                            item.style.transform = 'translateY(-4px)';
                        };
                        item.onmouseout = function() {
                            item.style.transform = 'translateY(0)';
                        };
                        
                        grid.appendChild(item);
                    })(product);
                }
                
                // FINAL SAFETY: Remove any items beyond 4
                while (grid.children.length > 4) {
                    grid.removeChild(grid.lastChild);
                }
                
                console.log('Final grid item count:', grid.children.length);
                
                wrap.style.display = 'block';
            })
            .catch(function(err){
                console.log('Error loading related products:', err);
                wrap.style.display = 'none';
            });
    }

    /* ── Open ── */
    window.openProductModal = function(card) {
        if (!overlay) {
            console.error('prodDetailOverlay not found!');
            return;
        }

        // SHOW MODAL IMMEDIATELY - don't wait for data loading
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        var model    = card.getAttribute('data-model')  || '';
        var type     = card.getAttribute('data-type')   || '';
        var imgSrc   = card.getAttribute('data-image')  || '';
        var brand    = card.getAttribute('data-brand') || (typeof BRAND_NAME !== 'undefined' ? BRAND_NAME : '');
        var specsRaw = card.getAttribute('data-specs')  || '[]';
        var imagesRaw = card.getAttribute('data-images') || '[]';
        var specs    = [];
        var images   = [];
        try { specs = JSON.parse(specsRaw); } catch(e){}
        try { images = JSON.parse(imagesRaw); } catch(e){}

        // Extra fields from Supabase products
        var description    = card.getAttribute('data-description')    || '';
        var specsText      = card.getAttribute('data-specifications') || '';
        var price          = card.getAttribute('data-price')          || '';
        var productName    = card.getAttribute('data-product-name')   || '';

        // DEBUG: Log the data being received
        console.log('openProductModal called with:', {
            model: model,
            type: type,
            brand: brand,
            imgSrc: imgSrc,
            specsCount: Array.isArray(specs) ? specs.length : 0,
            imagesCount: Array.isArray(images) ? images.length : 0
        });

        // Update modal header info (these MUST display)
        var brandEl = document.getElementById('prodDetailBrand');
        var nameEl = document.getElementById('prodDetailName');
        var typeEl = document.getElementById('prodDetailType');

        if (brandEl) {
            var brandLogoPath = getModalBrandLogo(brand);
            if (brandLogoPath) {
                brandEl.innerHTML = '<img src="' + brandLogoPath + '" alt="' + escapeHtml(brand || 'Brand') + '" style="display:block;width:100%;height:100%;object-fit:contain;object-position:left center;filter:drop-shadow(0 3px 8px rgba(20, 24, 68, 0.28));">';
                brandEl.style.display = 'inline-flex';
                brandEl.style.alignItems = 'center';
                brandEl.style.justifyContent = 'flex-start';
                brandEl.style.width = 'clamp(140px, 22vw, 260px)';
                brandEl.style.height = '48px';
                brandEl.style.minHeight = '48px';
                brandEl.style.padding = '6px 10px';
                brandEl.style.boxSizing = 'border-box';
                brandEl.style.background = 'transparent';
                brandEl.style.borderLeft = 'none';
                brandEl.style.border = 'none';
                brandEl.style.borderRadius = '0';

                var logoImg = brandEl.querySelector('img');
                if (logoImg) {
                    logoImg.onerror = function() {
                        brandEl.textContent = brand || 'Product';
                        brandEl.style.display = brand ? 'block' : 'none';
                        brandEl.style.alignItems = '';
                        brandEl.style.justifyContent = '';
                        brandEl.style.width = 'fit-content';
                        brandEl.style.height = 'auto';
                        brandEl.style.minHeight = '';
                        brandEl.style.padding = '8px 14px';
                        brandEl.style.boxSizing = '';
                        brandEl.style.background = 'linear-gradient(135deg, rgba(43,17,219,0.12) 0%, rgba(43,17,219,0.06) 100%)';
                        brandEl.style.borderLeft = '3px solid #2B11DB';
                        brandEl.style.border = '';
                        brandEl.style.borderRadius = '8px';
                    };
                }
            } else {
                brandEl.textContent = brand || 'Product';
                brandEl.style.display = brand ? 'block' : 'none';
                brandEl.style.alignItems = '';
                brandEl.style.justifyContent = '';
                brandEl.style.width = 'fit-content';
                brandEl.style.height = 'auto';
                brandEl.style.minHeight = '';
                brandEl.style.padding = '8px 14px';
                brandEl.style.boxSizing = '';
                brandEl.style.background = 'linear-gradient(135deg, rgba(43,17,219,0.12) 0%, rgba(43,17,219,0.06) 100%)';
                brandEl.style.borderLeft = '3px solid #2B11DB';
                brandEl.style.border = '';
                brandEl.style.borderRadius = '8px';
            }
            console.log('Set brand to:', brand, '| logo:', brandLogoPath ? 'yes' : 'no');
        } else {
            console.error('prodDetailBrand not found!');
        }

        if (nameEl) {
            nameEl.textContent = model || 'Product';
            nameEl.style.display = model ? 'block' : 'none';
            console.log('Set name/model to:', model);
        } else {
            console.error('prodDetailName not found!');
        }

        if (typeEl) {
            typeEl.textContent = type || 'Product';
            typeEl.style.display = type ? 'block' : 'none';
            console.log('Set type to:', type);
        } else {
            console.error('prodDetailType not found!');
        }

        // Load product images from card data (must run BEFORE loadProductDetails so the
        // async JSON fetch cannot overwrite the correct Supabase image path)
        // Fall back to single data-image if the images array is empty
        if (images.length === 0 && imgSrc) {
            images = [imgSrc];
        }
        loadProductImagesFromCard(images);

        // Show description & price from card data immediately (no async fetch needed)
        var descSection = document.getElementById('prodDescSection');
        var descEl      = document.getElementById('prodDetailDesc');
        var noDetailsEl = document.getElementById('prodNoDetails');
        if (description) {
            if (descEl)      descEl.innerHTML = formatDescriptionHtml(description);
            if (descSection) descSection.style.display = 'block';
            if (noDetailsEl) noDetailsEl.style.display = 'none';
        }
        // Show price badge below type if available
        var priceEl = document.getElementById('prodDetailPrice');
        if (priceEl) {
            if (price) { priceEl.textContent = '\u20B1 ' + price; priceEl.style.display = 'inline-block'; }
            else        { priceEl.style.display = 'none'; }
        }
        // Show product name as subtitle if it differs from model
        var prodSubnameEl = document.getElementById('prodDetailSubname');
        if (prodSubnameEl) {
            if (productName && productName !== model) {
                prodSubnameEl.textContent = productName;
                prodSubnameEl.style.display = 'block';
            } else {
                prodSubnameEl.style.display = 'none';
            }
        }

        // Load additional data from JSON (description, specs) — enriches old hardcoded products
        loadProductDetails(brand, model, specs, specsText);

        /* Specifications (plain text + optional table) */
        var hasSpecs = renderSpecifications(specs, specsText);
        if (hasSpecs && noDetailsEl) {
            noDetailsEl.style.display = 'none';
        }

        /* Load Datasheet from assets folder */
        var datasheetWrap = document.getElementById('prodDatasheetWrap');
        var datasheetBtn = document.getElementById('prodDatasheetBtn');
        datasheetWrap.style.display = 'none';

                function showDatasheet(url) {
                    var clean = resolveModalPath(url);
                        if (!clean) return false;
                        datasheetBtn.href = clean;
                        datasheetWrap.style.display = 'flex';
                        return true;
                }

                function tryLegacyDatasheetSearch() {
                        if (!(brand && model)) return;

                        function normalize(str) {
                            return str.replace(/\s+/g, '').replace(/_/g, '').toLowerCase();
                        }
                        var brandFolder = brand;
                        if (brand === 'Panasonic Connect') brandFolder = 'PANASONIC';
                        var brandNorm = normalize(brandFolder);
                        var modelCode = model.split(/[^A-Za-z0-9]+/).filter(function(p){ return p.match(/^[A-Z0-9]+[-\d]*$/i); })[0] || model.split(/\s/)[0];
                        var modelNorm = normalize(modelCode);
                        var modelFullNorm = normalize(model);

                        // Extract series code (e.g., "KR2" from "YD-350KR2")
                        var seriesMatch = model.match(/([A-Z]+\d+)$/i);
                        var seriesCode = seriesMatch ? seriesMatch[1] : '';

                        // Use absolute paths that work from any directory (brand.php, category pages, etc.)
                        var patterns = [
                            '/ANDISON/assets/brands items/' + brandFolder + '/Datasheet/' + brandFolder + ' ' + modelCode + '.pdf',
                            '/ANDISON/assets/brands items/' + brandFolder + '/Datasheet/Datasheet ' + modelCode + '.pdf',
                            '/ANDISON/assets/brands items/' + brandFolder + '/Datasheet/' + modelCode + '.pdf',
                            '/ANDISON/assets/brands items/' + brandFolder + '/Datasheet/' + model + '.pdf'
                        ];

                        // Add series-based pattern (e.g., "KR2 Series.pdf" for YD-350KR2)
                        if (seriesCode) {
                            patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/Datasheet/' + seriesCode + ' Series.pdf');
                            // Also check in product-type subdirectories (e.g., TIG Welding Machine/Datasheet/Panasonic BP4 Series.pdf)
                            patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/TIG Welding Machine/Datasheet/' + brandFolder + ' ' + seriesCode + ' Series.pdf');
                            patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/CO2,MAG,MIG Welding Machine/Datasheet/' + brandFolder + ' ' + seriesCode + ' Series.pdf');
                            patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/Arc Welding Robot/Datasheet/' + brandFolder + ' ' + seriesCode + ' Series.pdf');
                            // Also check in Arc Welding Robot Brochure folder with proper casing (e.g., Panasonic G3 Welding Robot.pdf)
                            patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/Arc Welding Robot/Brochure/Panasonic ' + seriesCode + ' Welding Robot.pdf');
                            patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/Arc Welding Robot/Brochure/' + seriesCode + ' Welding Robot.pdf');
                        }

                        // Check in product-type subdirectories for model-specific datasheets
                        patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/TIG Welding Machine/Datasheet/' + brandFolder + ' ' + model + '.pdf');
                        patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/TIG Welding Machine/Datasheet/' + model + 'YNA.pdf');
                        // Convert YD model to YC for datasheet search (e.g., YD-200BL3 -> YC-200BL3YNA)
                        var ycModel = model.replace(/^YD/, 'YC');
                        patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/TIG Welding Machine/Datasheet/' + brandFolder + ' ' + ycModel + 'YNA.pdf');
                        patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/TIG Welding Machine/Datasheet/' + ycModel + 'YNA.pdf');
                        patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/TIG Welding Machine/Datasheet/' + brandFolder + ' ' + ycModel + '.pdf');
                        patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/TIG Welding Machine/Datasheet/' + ycModel + '.pdf');
                        patterns.push('/ANDISON/assets/brands items/' + brandFolder + '/CO2,MAG,MIG Welding Machine/Datasheet/' + brandFolder + ' ' + model + '.pdf');

                        patterns.push('/ANDISON/assets/brands items/' + brandNorm + '/Datasheet/' + brandNorm + modelNorm + '.pdf');
                        patterns.push('/ANDISON/assets/brands items/' + brandNorm + '/Datasheet/datasheet' + modelNorm + '.pdf');
                        patterns.push('/ANDISON/assets/brands items/' + brandNorm + '/Datasheet/' + modelNorm + '.pdf');
                        patterns.push('/ANDISON/assets/brands items/' + brandNorm + '/Datasheet/' + modelFullNorm + '.pdf');

                        console.log('[DATASHEET DEBUG] brand:', brand, '| model:', model, '| modelCode:', modelCode, '| seriesCode:', seriesCode);
                        console.log('[DATASHEET DEBUG] Checking legacy patterns:', patterns);
                        (function tryDatasheet(i) {
                            if (i >= patterns.length) {
                                return;
                            }
                            var candidate = resolveModalPath(patterns[i].replace(/%20/g, ' '));
                            fetch(candidate, { method: 'HEAD' })
                                .then(function(r) {
                                    if (r.ok) {
                                        console.log('[DATASHEET DEBUG] LEGACY FOUND:', candidate);
                                        showDatasheet(candidate);
                                    } else {
                                        tryDatasheet(i+1);
                                    }
                                })
                                .catch(function(){ tryDatasheet(i+1); });
                        })(0);
                }

                var directDatasheet = resolveModalPath((card.getAttribute('data-datasheet') || '').trim());
                if (!showDatasheet(directDatasheet) && brand && model) {
                        // Preferred source: live products API (Supabase-backed).
                    fetch(resolveModalPath('Andison/data/brands_info_api.php'), { cache: 'no-store' })
                                .then(function(r) { return r.json(); })
                                .then(function(data) {
                                        var datasheetUrl = '';
                                        var targetBrand = String(brand || '').toLowerCase();
                                        var targetModel = String(model || '').toLowerCase();

                                        function getProductDatasheet(p) {
                                                if (!p || typeof p !== 'object') return '';
                                                return String(p.datasheet || p.datasheet_url || p.datasheetUrl || '').trim();
                                        }

                                        function scanProducts(list) {
                                                if (!Array.isArray(list)) return '';
                                                for (var i = 0; i < list.length; i++) {
                                                        var pModel = String((list[i] && list[i].model) || '').toLowerCase();
                                                        if (pModel === targetModel) {
                                                                var ds = getProductDatasheet(list[i]);
                                                                if (ds) return ds;
                                                        }
                                                }
                                                return '';
                                        }

                                        var brandData = data[brand];
                                        if (!brandData) {
                                                for (var key in data) {
                                                        if (String(key).toLowerCase() === targetBrand) {
                                                                brandData = data[key];
                                                                break;
                                                        }
                                                }
                                        }

                                        if (brandData && Array.isArray(brandData.products)) {
                                                datasheetUrl = scanProducts(brandData.products);
                                        }

                                        // Fallback scan across all brands by model.
                                        if (!datasheetUrl) {
                                                for (var bKey in data) {
                                                        var bData = data[bKey];
                                                        if (bData && Array.isArray(bData.products)) {
                                                                datasheetUrl = scanProducts(bData.products);
                                                                if (datasheetUrl) break;
                                                        }
                                                }
                                        }

                                        if (!showDatasheet(datasheetUrl)) {
                                                tryLegacyDatasheetSearch();
                                        }
                                })
                                .catch(function() {
                                        tryLegacyDatasheetSearch();
                                });
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
})();
</script>

<style>
/* Shared modern footer for legacy product/category pages */
footer.footer-modernized {
    background: linear-gradient(135deg, #2209c9 0%, #2b11db 52%, #1b0893 100%) !important;
    color: #eef1ff !important;
    padding: 56px 0 0 !important;
    border-top: 1px solid rgba(255, 255, 255, 0.14) !important;
    position: relative;
    overflow: hidden;
}

footer.footer-modernized::before {
    content: '';
    position: absolute;
    inset: -180px -200px auto auto;
    width: 420px;
    height: 420px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.14) 0%, rgba(255, 255, 255, 0) 72%);
    pointer-events: none;
}

footer.footer-modernized .footer-content {
    max-width: 1460px;
    margin: 0 auto;
    padding: 0 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 34px;
    position: relative;
    z-index: 1;
}

footer.footer-modernized .footer-main-grid {
    display: grid;
    grid-template-columns: minmax(240px, 1.25fr) minmax(220px, 1fr) minmax(220px, 1fr) minmax(200px, 1fr);
    gap: 90px;
    align-items: start;
}

footer.footer-modernized .footer-brand-logo {
    display: inline-block;
    margin-bottom: 12px;
}

footer.footer-modernized .footer-brand-logo img {
    width: 228px;
    max-width: 100%;
    height: auto;
    filter: brightness(0) invert(1);
}

footer.footer-modernized .footer-brand-blurb {
    margin: 0;
    font-size: 10px;
    line-height: 1.58;
    color: rgba(239, 243, 255, 0.9);
    max-width: 330px;
}

footer.footer-modernized .footer-col-title {
    margin: 4px 0 14px;
    color: #ffffff;
    font-size: 10px;
    line-height: 1.05;
    letter-spacing: 0.5px;
    font-weight: 800;
    text-transform: uppercase;
}

footer.footer-modernized .footer-contact-list {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

footer.footer-modernized .footer-contact-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    color: rgba(240, 244, 255, 0.92);
    font-size: 10px;
    line-height: 1.5;
}

footer.footer-modernized .footer-contact-item i {
    color: #dde4ff;
    font-size: 10px;
    margin-top: 4px;
    flex-shrink: 0;
}

footer.footer-modernized .footer-nav-links {
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
}

footer.footer-modernized .footer-nav-links a {
    color: rgba(255, 255, 255, 0.96);
    text-decoration: none;
    font-size: 10px;
    font-weight: 600;
    line-height: 1.2;
    width: fit-content;
    position: relative;
    background: transparent !important;
}

footer.footer-modernized .footer-nav-links a::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 0;
    height: 2px;
    background: #ffffff;
    transition: width 0.3s ease;
}

footer.footer-modernized .footer-nav-links a:hover::after {
    width: 100%;
}

footer.footer-modernized .footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.16);
    padding: 18px 86px 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 14px;
    position: relative;
}

footer.footer-modernized .footer-copyright {
    margin: 0;
    font-size: 10px;
    color: rgba(243, 247, 255, 0.95);
    font-weight: 500;
    width: 100%;
    text-align: center;
}

footer.footer-modernized .footer-copyright strong {
    color: #ffffff;
    font-weight: 700;
}

footer.footer-modernized .footer-scroll-top {
    position: absolute;
    right: 26px;
    bottom: 20px;
    width: 54px;
    height: 54px;
    border-radius: 50%;
    border: none;
    background: #f1f4ff;
    color: #2b11db;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 24px rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: transform 0.24s ease, box-shadow 0.24s ease;
    z-index: 2;
}

footer.footer-modernized .footer-scroll-top:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 26px rgba(0, 0, 0, 0.28);
}

@media (max-width: 1180px) {
    footer.footer-modernized .footer-main-grid {
        grid-template-columns: repeat(2, minmax(220px, 1fr));
        gap: 24px 28px;
    }

    footer.footer-modernized .footer-col-title { font-size: 10px; }
    footer.footer-modernized .footer-nav-links a { font-size: 10px; }
    footer.footer-modernized .footer-copyright { font-size: 10px; }
}

@media (max-width: 768px) {
    footer.footer-modernized .footer-main-grid { grid-template-columns: 1fr; }
    footer.footer-modernized .footer-col-title { font-size: 10px; }
    footer.footer-modernized .footer-nav-links a { font-size: 10px; }

    footer.footer-modernized .footer-bottom {
        flex-direction: column;
        align-items: center;
        padding-right: 0;
        padding-left: 0;
        padding-bottom: 80px;
    }

    footer.footer-modernized .footer-copyright { font-size: 10px; }

    footer.footer-modernized .footer-scroll-top {
        width: 46px;
        height: 46px;
        right: 20px;
        bottom: 18px;
    }
}
</style>

<script>
/* Upgrade legacy footer markup to the new multi-column layout on product/category pages */
(function(){
    function modernizeLegacyFooter() {
        var footer = document.querySelector('footer');
        if (!footer) return;
        if (footer.classList.contains('footer-modernized')) return;

        var footerContent = footer.querySelector('.footer-content');
        var legacyLinks = footer.querySelector('.footer-links');
        var legacyCopyright = footer.querySelector('.footer-copyright');

        // Only transform pages still using the old footer block.
        if (!footerContent || !legacyLinks || !legacyCopyright || footer.querySelector('.footer-main-grid')) {
            return;
        }

        footer.classList.add('footer-modernized');

        var copyrightText = (legacyCopyright.textContent || '').trim();
        if (!copyrightText) {
            copyrightText = 'Copyright 2021 Andison Industrial Sales Inc.';
        }

        var footerBase = (function() {
            var parts = window.location.pathname.split('/').filter(function(part) {
                return part !== '';
            });
            for (var i = 0; i < parts.length; i++) {
                var lower = parts[i].toLowerCase();
                if (lower === 'andison' || lower === 'andison-1') {
                    return '/' + parts.slice(0, i + 1).join('/');
                }
            }
            return '';
        })();

        footerContent.innerHTML = (
            ''
            + '<div class="footer-main-grid">'
                + '<div class="footer-brand-col">'
                    + '<a href="/ANDISON/home.php" class="footer-brand-logo" aria-label="Andison Industrial Home">'
                        + '<img src="/ANDISON/assets/HOME/image-removebg-preview.png" alt="Andison Industrial">'
                    + '</a>'
                    + '<p class="footer-brand-blurb">Andison Industrial Sales Inc., is a leading local industrial supply company, delivering high quality solutions, representing various world-class brands since 1994.</p>'
                + '</div>'

                + '<div class="footer-col">'
                    + '<h4 class="footer-col-title">Manila</h4>'
                    + '<ul class="footer-contact-list">'
                        + '<li class="footer-contact-item"><i class="bi bi-geo-alt-fill"></i><span>Andison Bldg., Ground Flr. 917-919 Luzon St., Sta. Cruz, Manila, 1003 Philippines</span></li>'
                        + '<li class="footer-contact-item"><i class="bi bi-telephone-fill"></i><span>Phone: (+632) 8584-4958</span></li>'
                        + '<li class="footer-contact-item"><i class="bi bi-telephone-fill"></i><span>(+632) 8243-2873</span></li>'
                        + '<li class="footer-contact-item"><i class="bi bi-printer-fill"></i><span>Fax: (+632) 8252-9224</span></li>'
                    + '</ul>'
                + '</div>'

                + '<div class="footer-col">'
                    + '<h4 class="footer-col-title">Calabarzon</h4>'
                    + '<ul class="footer-contact-list">'
                        + '<li class="footer-contact-item"><i class="bi bi-geo-alt-fill"></i><span>29B P. Zamora Street, Batangas City, 4200 Philippines</span></li>'
                        + '<li class="footer-contact-item"><i class="bi bi-telephone-fill"></i><span>Phone: (+6343) 425 4126</span></li>'
                        + '<li class="footer-contact-item"><i class="bi bi-printer-fill"></i><span>Fax: (+6343) 723-3108</span></li>'
                    + '</ul>'
                + '</div>'

                + '<div class="footer-col">'
                    + '<h4 class="footer-col-title">Navigation</h4>'
                    + '<nav class="footer-nav-links" aria-label="Footer navigation">'
                        + '<a href="/ANDISON/home.php">Home</a>'
                        + '<a href="/ANDISON/industries.php">Industries and Services</a>'
                        + '<a href="/ANDISON/aboutus.php">Our Company</a>'
                        + '<a href="/ANDISON/brands.php">Products</a>'
                        + '<a href="/ANDISON/contact.php">Contact Us</a>'
                    + '</nav>'
                + '</div>'
            + '</div>'
            + '<div class="footer-bottom">'
                + '<p class="footer-copyright">' + copyrightText + '</p>'
            + '</div>'
        ).replace(/\/ANDISON(?=\/)/g, footerBase);

        if (!footer.querySelector('.footer-scroll-top')) {
            var btn = document.createElement('button');
            btn.className = 'footer-scroll-top';
            btn.type = 'button';
            btn.setAttribute('aria-label', 'Scroll to top');
            btn.innerHTML = '<i class="bi bi-chevron-up"></i>';
            btn.addEventListener('click', function(){
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            footer.appendChild(btn);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function(){
            modernizeLegacyFooter();
            setTimeout(modernizeLegacyFooter, 80);
        });
    } else {
        modernizeLegacyFooter();
        setTimeout(modernizeLegacyFooter, 80);
    }
})();
</script>

<script>
/* Limit category pagination to 4 page numbers plus prev/next arrows */
(function(){
    function installCompactPagination() {
        if (typeof window.updatePaginationButtons !== 'function') return;

        window.updatePaginationButtons = function(totalPages) {
            var paginationDiv = document.querySelector('.pagination');
            if (!paginationDiv) return;

            totalPages = Math.max(1, parseInt(totalPages, 10) || 1);
            var current = Math.max(1, Math.min(totalPages, parseInt(window.currentPage, 10) || 1));
            var maxVisiblePages = 4;

            var startPage = current - Math.floor(maxVisiblePages / 2);
            if (startPage < 1) startPage = 1;

            var endPage = startPage + maxVisiblePages - 1;
            if (endPage > totalPages) {
                endPage = totalPages;
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            var html = '';

            html += '<a href="#" class="' + (current === 1 ? 'disabled' : '') + '" onclick="goToPage(' + (current - 1) + '); return false;" title="Previous"><i class="bi bi-chevron-left"></i></a>';

            for (var i = startPage; i <= endPage; i++) {
                if (i === current) {
                    html += '<span class="active">' + i + '</span>';
                } else {
                    html += '<a href="#" onclick="goToPage(' + i + '); return false;">' + i + '</a>';
                }
            }

            html += '<a href="#" class="' + (current === totalPages ? 'disabled' : '') + '" onclick="goToPage(' + (current + 1) + '); return false;" title="Next"><i class="bi bi-chevron-right"></i></a>';
            html += '<span class="page-info">Page ' + current + ' of ' + totalPages + '</span>';

            paginationDiv.innerHTML = html;
        };

        if (typeof window.updatePagination === 'function') {
            window.updatePagination();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function(){
            installCompactPagination();
            setTimeout(installCompactPagination, 80);
        });
    } else {
        installCompactPagination();
        setTimeout(installCompactPagination, 80);
    }
})();
</script>

<style>
/* Keep nav/contact popovers above the product modal overlay when hovering header */
header {
    z-index: 10050 !important;
}

header .nav-dropdown,
header .contact-popover {
    z-index: 10060 !important;
}

/* Remove legacy category-page filter sidebar and use full-width product area */
.category-content .product-filters {
    display: none !important;
}

.category-content {
    gap: 0 !important;
}

.category-content .main-product-area {
    flex: 1 1 100% !important;
    width: 100% !important;
    max-width: 100% !important;
}

/* Product descriptions should only appear in the modal, not on listing cards */
.product-card .product-description {
    display: none !important;
}

/* Card-level image slider */
.card-img-slider { position:relative; width:100%; height:100%; }
.card-img-slider img {
    position:absolute; inset:0; width:100%; height:100%;
    object-fit:contain; opacity:0;
    transition:opacity 0.7s ease;
    pointer-events:none;
}
.card-img-slider img.active { opacity:1; pointer-events:auto; }
/* Tiny dots indicator */
.card-slider-dots {
    position:absolute; bottom:6px; left:50%; transform:translateX(-50%);
    display:flex; gap:5px; z-index:2; pointer-events:none;
}
.card-slider-dots span {
    width:5px; height:5px; border-radius:50%;
    background:rgba(43,17,219,0.25); display:block; transition:background .3s;
}
.card-slider-dots span.active { background:#2B11DB; }
</style>

<script>
/* ── Card-level image slider ──
   Runs on every product card that has 2+ images in data-images.
   Works for .brand-product-card (.brand-product-img) and
   .product-card (.product-image).
*/
(function(){
    function initCardSliders() {
        var cards = document.querySelectorAll(
            '.brand-product-card[data-images], .product-card[data-images]'
        );
        cards.forEach(function(card) {
            var raw = card.getAttribute('data-images') || '[]';
            var imgs;
            try { imgs = JSON.parse(raw); } catch(e) { return; }
            if (!Array.isArray(imgs) || imgs.length < 2) return;

            // Already initialised?
            if (card.getAttribute('data-slider-init')) return;
            card.setAttribute('data-slider-init', '1');

            // Find the image container
            var wrap = card.querySelector('.brand-product-img, .product-image');
            if (!wrap) return;

            // Build slider markup inside the container
            var sliderDiv = document.createElement('div');
            sliderDiv.className = 'card-img-slider';
            sliderDiv.style.cssText = 'position:relative;width:100%;height:100%;';

            imgs.forEach(function(src, i) {
                var img = document.createElement('img');
                img.src = src;
                img.alt = card.getAttribute('data-model') || '';
                img.style.padding = '8px';
                if (i === 0) img.className = 'active';
                sliderDiv.appendChild(img);
            });

            // Dots
            var dotsDiv = document.createElement('div');
            dotsDiv.className = 'card-slider-dots';
            imgs.forEach(function(_, i) {
                var dot = document.createElement('span');
                if (i === 0) dot.className = 'active';
                dotsDiv.appendChild(dot);
            });
            sliderDiv.appendChild(dotsDiv);

            // Clear the container and insert slider
            wrap.innerHTML = '';
            wrap.style.overflow = 'hidden';
            wrap.appendChild(sliderDiv);

            // Auto-advance
            var idx = 0;
            var imgEls = sliderDiv.querySelectorAll('img');
            var dotEls = dotsDiv.querySelectorAll('span');
            setInterval(function() {
                imgEls[idx].classList.remove('active');
                dotEls[idx].classList.remove('active');
                idx = (idx + 1) % imgs.length;
                imgEls[idx].classList.add('active');
                dotEls[idx].classList.add('active');
            }, 2500);
        });
    }

    // Run after DOM ready and also after pagination re-renders cards
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCardSliders);
    } else {
        initCardSliders();
    }

    // Re-run when pagination changes visibility of cards
    var _origUpdatePagination = window.updatePagination;
    if (typeof _origUpdatePagination === 'function') {
        window.updatePagination = function() {
            _origUpdatePagination.apply(this, arguments);
            setTimeout(initCardSliders, 50);
        };
    }

    // Also expose so pagination can call it manually
    window.initCardSliders = initCardSliders;
})();
</script>

