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
<div id="prodDetailOverlay" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(7,10,26,.62);backdrop-filter:blur(9px) saturate(118%);-webkit-backdrop-filter:blur(9px) saturate(118%);z-index:99999;align-items:center;justify-content:center;padding:clamp(18px,2.2vw,28px) clamp(8px,1.1vw,14px) clamp(16px,2vw,24px);box-sizing:border-box;overflow:hidden;animation:fadeIn .2s ease;">
    <div id="prodDetailModal" style="background:#fff;border-radius:22px;max-width:min(92vw,1220px);width:min(92vw,1220px);max-height:88vh;height:88vh;overflow:hidden;box-shadow:0 25px 70px rgba(0,0,0,.32),0 0 1px rgba(0,0,0,.1);position:relative;animation:modalIn .3s cubic-bezier(0.34, 1.56, 0.64, 1);flex-shrink:0;margin:0 auto;">
    <button id="prodDetailClose" style="position:absolute;top:18px;right:18px;background:rgba(0,0,0,0.05);border:none;font-size:28px;cursor:pointer;color:#555;line-height:1;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;transition:all .2s;z-index:10;\">&times;</button>
    
    <div style="display:flex;gap:0;flex-wrap:wrap;height:100%;">
      <!-- LEFT SIDE: Product Images Gallery -->
    <div style="flex:0 0 33%;background:linear-gradient(180deg, #f9f9fc 0%, #f0f3ff 50%, #f8fafd 100%);border-radius:24px 0 0 24px;padding:clamp(12px,1.5vw,20px) clamp(18px,2vw,30px);display:grid;grid-template-rows:auto auto minmax(0,1fr);gap:10px;position:relative;overflow:hidden;min-height:0;">
        <!-- Decorative top accent -->
        <div style="position:absolute;top:0;right:0;width:120px;height:120px;background:linear-gradient(135deg, rgba(43,17,219,0.06) 0%, transparent 70%);border-radius:0 0 0 100%;pointer-events:none;"></div>
        
        <div id="prodImageGallery" style="height:clamp(170px,30vh,300px);background:#fff;border-radius:20px;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;flex-shrink:0;box-shadow:0 16px 40px rgba(43,17,219,0.18), 0 0 1px rgba(43,17,219,0.3), inset 0 1px 0 rgba(255,255,255,0.9);border:1px solid rgba(43,17,219,0.12);">
          <img id="prodMainImg" src="" alt="" style="max-width:100%;max-height:100%;object-fit:contain;padding:clamp(14px,1.5vw,24px);width:100%;">
          <i id="prodNoImg" class="bi bi-tools" style="display:none;font-size:80px;color:#d4d9e6;"></i>
        </div>
        
        <!-- Image thumbnails carousel -->
        <div id="prodThumbnailsWrap" style="display:none;background:linear-gradient(135deg, rgba(43,17,219,0.05) 0%, rgba(43,17,219,0.02) 100%);border-radius:16px;padding:12px;border:1.5px solid rgba(43,17,219,0.1);flex-shrink:0;width:100%;">
          <div id="prodThumbnails" style="display:flex;gap:12px;flex-wrap:nowrap;overflow-x:auto;overflow-y:hidden;justify-content:flex-start;padding-bottom:8px;"></div>
        </div>
        
        <!-- Datasheet button -->
        <!-- (Datasheet button/preview moved to right column) -->
        
        <!-- Related Products -->
                <div id="relatedProductsWrap" style="display:none;position:relative;z-index:2;margin-top:4px;min-height:0;overflow-y:auto;">
                    <div class="related-products-title"><i class="bi bi-boxes"></i> MORE FROM THIS BRAND</div>
                                        <div id="relatedProductsGrid" style="display:grid;grid-template-columns:repeat(2, minmax(0, 1fr));gap:12px;align-content:start;"></div>
        </div>
      </div>
      
      <!-- Decorative divider line -->
    <div style="position:absolute;left:33%;top:0;bottom:0;width:1px;background:linear-gradient(180deg, transparent 0%, rgba(43,17,219,0.1) 30%, rgba(43,17,219,0.1) 70%, transparent 100%);pointer-events:none;"></div>
      
      <!-- RIGHT SIDE: Product Information -->
    <div style="flex:0 0 67%;display:grid;grid-template-rows:minmax(0,1fr) auto;overflow:hidden;min-height:0;height:100%;">
        <!-- Scrollable content area -->
        <div id="prodDetailContentScroll" style="overflow-y:auto;overflow-x:hidden;padding:clamp(14px,2vw,24px) clamp(22px,3vw,44px) clamp(12px,1.5vw,18px);min-height:0;height:100%;">
          <!-- Header Section -->
          <div style="margin-bottom:24px;padding-bottom:20px;border-bottom:2px solid #f0f4ff;">
            <span id="prodDetailBrand" style="font-size:9px;font-weight:950;color:#2B11DB;letter-spacing:2px;text-transform:uppercase;margin-bottom:8px;display:block;background:linear-gradient(135deg, rgba(43,17,219,0.12) 0%, rgba(43,17,219,0.06) 100%);padding:8px 14px;border-radius:8px;width:fit-content;border-left:3px solid #2B11DB;"></span>
            <h2 id="prodDetailName" style="font-size:clamp(28px,3vw,42px);font-weight:500;color:#0a0a1a;margin:10px 0 12px;line-height:1.12;letter-spacing:-0.7px;text-shadow:0 2px 8px rgba(0,0,0,0.08);"></h2>
            <span id="prodDetailType" style="display:inline-block;background:linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);color:#fff;font-size:10px;font-weight:950;padding:10px 20px;border-radius:24px;text-transform:uppercase;letter-spacing:1.2px;box-shadow:0 6px 16px rgba(43,17,219,0.28);"></span>
            <div id="prodDetailSubname" style="display:none;font-size:15px;font-weight:600;color:#555;margin-top:8px;letter-spacing:0.2px;"></div>
            <div id="prodDetailPrice" style="display:none;margin-top:10px;font-size:13px;font-weight:800;color:#2B11DB;background:rgba(43,17,219,0.08);padding:6px 14px;border-radius:20px;width:fit-content;letter-spacing:0.5px;"></div>
          </div>
          
          <!-- Description -->
          <div id="prodDescSection" style="display:none;margin-bottom:22px;padding:18px 20px;background:linear-gradient(135deg, rgba(43,17,219,0.06) 0%, rgba(43,17,219,0.03) 100%);border-radius:14px;border-left:4px solid #2B11DB;">
            <div style="font-size:17px;font-weight:950;color:#2B11DB;letter-spacing:1px;margin-bottom:10px;text-transform:uppercase;display:flex;align-items:center;gap:6px;"><i class="bi bi-file-text"></i> OVERVIEW</div>
                        <div id="prodDetailDesc" style="font-size:16px;color:#444;line-height:1.8;margin:0;font-weight:500;"></div>
                        <div id="prodOverviewMediaWrap" style="display:none;margin:12px auto 0;width:72%;max-width:520px;">
                            <img id="prodOverviewMediaImg" src="" alt="Overview image" style="display:block;width:100%;height:140px;object-fit:contain;border-radius:12px;border:none;background:transparent;box-shadow:none;padding:0;">
                        </div>
          </div>
          
                    <!-- Specs -->
          <div id="prodSpecsSection" style="display:none;margin-bottom:24px;">
            <div style="font-size:17px;font-weight:950;color:#2B11DB;letter-spacing:1px;margin-bottom:14px;text-transform:uppercase;display:flex;align-items:center;gap:6px;"><i class="bi bi-speedometer2"></i> SPECIFICATIONS</div>
                        <div id="prodSpecsContent" style="display:block;">
                            <div id="prodSpecsDetails" style="min-width:0;">
                                <div id="prodDetailSpecsText" style="display:none;margin:0 0 12px;padding:12px 14px;border-radius:12px;border-left:4px solid #2B11DB;background:linear-gradient(135deg, rgba(43,17,219,0.06) 0%, rgba(43,17,219,0.02) 100%);font-size:16px;color:#3f4459;line-height:1.75;"></div>
                                <div id="prodSpecsTableWrap" style="display:none;"></div>
                            </div>
                        </div>
          </div>
          
          <!-- Additional Details -->
          <div id="prodAdditionalDetails" style="display:none;margin-bottom:24px;">
            <div id="prodAdditionalContent"></div>
          </div>
          
          <!-- Default message -->
          <div id="prodNoDetails" style="font-size:14px;color:#9a9fb5;line-height:1.8;font-style:italic;">
            <p style="margin:0;">Complete specifications and datasheets available upon request.</p>
          </div>
        </div>

        <!-- PINNED Button footer — always visible -->
        <div style="flex-shrink:0;padding:10px clamp(22px,3vw,44px) clamp(12px,1.5vw,18px);border-top:1px solid #f0f0fa;background:#fff;">
                    <div id="prodFooterActions" style="display:flex;align-items:stretch;gap:12px;flex-wrap:wrap;">
                        <div id="prodDatasheetWrap" style="display:none;flex:1 1 300px;position:relative;z-index:2;align-items:stretch;">
                            <a id="prodDatasheetBtn" href="" target="_blank" style="display:flex;align-items:center;justify-content:center;gap:10px;background:linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);color:#fff;padding:16px 22px;border-radius:14px;font-size:18px;font-weight:950;text-decoration:none;box-shadow:0 8px 22px rgba(43,17,219,0.3);transition:all 0.35s cubic-bezier(0.34,1.56,0.64,1);width:100%;border:1px solid rgba(255,255,255,0.2);letter-spacing:1.2px;text-transform:uppercase;position:relative;overflow:hidden;white-space:nowrap;" onmouseover="this.style.boxShadow='0 12px 32px rgba(43,17,219,0.45)';this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 8px 22px rgba(43,17,219,0.3)';this.style.transform='translateY(0)';">
                                <i class="bi bi-file-pdf" style="font-size:18px;"></i>
                                <span>DATASHEET</span>
                            </a>
                        </div>
                        <button id="prodDetailInquiry" style="flex:1 1 300px;width:auto;padding:16px 22px;background:linear-gradient(135deg, #2B11DB 0%, #1f0aa1 100%);color:#fff;border:none;border-radius:14px;font-size:18px;font-weight:950;letter-spacing:1.2px;cursor:pointer;transition:all .4s cubic-bezier(0.34,1.56,0.64,1);text-transform:uppercase;box-shadow:0 10px 28px rgba(43,17,219,0.38);position:relative;overflow:hidden;transform:translateY(0);" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 14px 36px rgba(43,17,219,0.5)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 28px rgba(43,17,219,0.38)';">ADD TO INQUIRY LIST</button>
                    </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="prodImageZoomOverlay" style="display:none;position:fixed;inset:0;background:rgba(8,10,22,0.92);backdrop-filter:blur(3px);z-index:100120;align-items:center;justify-content:center;padding:24px;box-sizing:border-box;">
    <button id="prodImageZoomClose" type="button" aria-label="Close zoomed image" style="position:absolute;top:20px;right:20px;width:44px;height:44px;border:none;border-radius:999px;background:rgba(255,255,255,0.14);color:#fff;font-size:28px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;">&times;</button>
    <img id="prodImageZoomImg" src="" alt="" style="max-width:min(96vw,1600px);max-height:92vh;object-fit:contain;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,0.45), 0 0 0 1px rgba(255,255,255,0.14);background:rgba(255,255,255,0.02);">
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

/* Branded scrollbars for modal surfaces */
#prodDetailOverlay,
#prodDetailModal > div,
#prodDetailModal > div > div:first-child,
#prodDetailContentScroll,
#prodThumbnails,
#prodSpecsTableWrap {
    scrollbar-width: thin;
    scrollbar-color: #4a35ea rgba(43, 17, 219, 0.14);
}

#prodDetailOverlay::-webkit-scrollbar,
#prodDetailModal > div::-webkit-scrollbar,
#prodDetailModal > div > div:first-child::-webkit-scrollbar,
#prodDetailContentScroll::-webkit-scrollbar,
#prodThumbnails::-webkit-scrollbar,
#prodSpecsTableWrap::-webkit-scrollbar {
    width: 12px;
    height: 12px;
}

#prodDetailOverlay::-webkit-scrollbar-track,
#prodDetailModal > div::-webkit-scrollbar-track,
#prodDetailModal > div > div:first-child::-webkit-scrollbar-track,
#prodDetailContentScroll::-webkit-scrollbar-track,
#prodThumbnails::-webkit-scrollbar-track,
#prodSpecsTableWrap::-webkit-scrollbar-track {
    background: linear-gradient(180deg, rgba(43,17,219,0.06) 0%, rgba(43,17,219,0.02) 100%);
    border-radius: 999px;
    border: 1px solid rgba(43,17,219,0.08);
}

#prodDetailOverlay::-webkit-scrollbar-thumb,
#prodDetailModal > div::-webkit-scrollbar-thumb,
#prodDetailModal > div > div:first-child::-webkit-scrollbar-thumb,
#prodDetailContentScroll::-webkit-scrollbar-thumb,
#prodThumbnails::-webkit-scrollbar-thumb,
#prodSpecsTableWrap::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #5a44f2 0%, #2B11DB 52%, #1f0aa1 100%);
    border-radius: 999px;
    border: 2px solid rgba(255,255,255,0.72);
    box-shadow: 0 1px 5px rgba(43,17,219,0.35);
}

#prodDetailOverlay::-webkit-scrollbar-thumb:hover,
#prodDetailModal > div::-webkit-scrollbar-thumb:hover,
#prodDetailModal > div > div:first-child::-webkit-scrollbar-thumb:hover,
#prodDetailContentScroll::-webkit-scrollbar-thumb:hover,
#prodThumbnails::-webkit-scrollbar-thumb:hover,
#prodSpecsTableWrap::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #6b57ff 0%, #3a1df1 50%, #220cae 100%);
}

/* Two-column layout */
#prodDetailModal > div {
  display: flex !important;
  gap: 0 !important;
  width: 100% !important;
  height: 100% !important;
  overflow: hidden !important;
  min-height: 0 !important;
}

#prodDetailModal > div > div:first-child {
    flex: 0 0 33%;
  background: linear-gradient(180deg, #f9f9fc 0%, #f0f4ff 100%);
  border-radius:24px 0 0 24px;
    padding:clamp(18px,2vw,30px);
  overflow: hidden !important;
    display: grid;
    grid-template-rows: auto auto minmax(0, 1fr);
    min-height: 0 !important;
    gap: 12px;
}

#prodDetailModal > div > div:last-child {
    flex: 0 0 67%;
    display: grid;
    grid-template-rows: minmax(0, 1fr) auto;
    overflow: hidden;
    min-height: 0 !important;
    height: 100% !important;
}

#prodDetailContentScroll {
    overflow-y: auto !important;
    overflow-x: hidden !important;
    min-height: 0 !important;
    height: 100% !important;
    max-height: 100% !important;
    -webkit-overflow-scrolling: touch;
}

#prodImageGallery {
    height:clamp(170px, 30vh, 300px);
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
    max-height:100%;
  object-fit:contain;
    padding:clamp(14px,1.5vw,24px);
  width:100%;
  animation: modalIn 0.4s ease-out;
  position: relative;
  z-index: 1;
    cursor: zoom-in;
  filter: drop-shadow(0 8px 20px rgba(0,0,0,0.06));
}
#prodSpecsTableWrap {
    overflow-x:visible;
}

#prodSpecsTableWrap .prod-detail-specs-table + .prod-detail-specs-table {
    margin-top: 14px;
}

#prodDetailSpecsTable,
.prod-detail-specs-table {
    width:100%;
    min-width:0;
    max-width:100%;
    table-layout:fixed;
    border-collapse:collapse;
    background:#fff;
    margin-left:auto;
    margin-right:auto;
}
#prodDetailSpecsTable thead,
.prod-detail-specs-table thead { 
    background: linear-gradient(180deg, #2f5f9d 0%, #1f4a82 55%, #183a67 100%);
  position: relative;
  z-index: 10;
}
#prodDetailSpecsTable th,
.prod-detail-specs-table th { 
    padding:18px 18px; 
      text-align:center; 
  border-bottom:1px solid rgba(255,255,255,0.3);
  color:#fff;
        font-size:16px;
  font-weight:950;
  text-transform:uppercase;
  letter-spacing:1px;
  font-weight: bold;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
}
#prodDetailSpecsTable tbody tr,
.prod-detail-specs-table tbody tr { border-bottom:1px solid #f1f3f8; transition:all 0.2s ease; }
#prodDetailSpecsTable tbody tr:hover,
.prod-detail-specs-table tbody tr:hover { background:linear-gradient(90deg, rgba(43,17,219,0.04) 0%, rgba(43,17,219,0.02) 100%); }
#prodDetailSpecsTable tbody tr:nth-child(odd),
.prod-detail-specs-table tbody tr:nth-child(odd) { background:#fbfbfd; }
#prodDetailSpecsTable tbody tr:nth-child(even),
.prod-detail-specs-table tbody tr:nth-child(even) { background:#fff; }
#prodDetailSpecsTable td,
.prod-detail-specs-table td { 
    padding:16px 18px;
  border-bottom:1px solid #f1f3f8;
    color:#374151;
        font-size:16px;
  line-height:1.5;
  font-weight:500;
        text-align:left;
    vertical-align:middle;
    white-space: normal;
    overflow-wrap: anywhere;
    word-break: break-word;
}
#prodDetailSpecsTable td:first-child,
.prod-detail-specs-table td:first-child{
  font-weight:900;
    color:#374151;
  background:linear-gradient(90deg, rgba(43,17,219,0.06) 0%, rgba(43,17,219,0.03) 100%);
    width:auto;
        font-size:16px;
  letter-spacing:0.2px;
    text-align:left;
}
#prodDetailSpecsTable tbody tr:nth-child(odd) td:first-child,
.prod-detail-specs-table tbody tr:nth-child(odd) td:first-child {
  background:linear-gradient(90deg, rgba(43,17,219,0.1) 0%, rgba(43,17,219,0.04) 100%);
}
#prodDetailSpecsTable tbody tr:last-child td,
.prod-detail-specs-table tbody tr:last-child td { border-bottom:none; }

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

#prodFooterActions {
    width: 100%;
}

#prodFooterActions #prodDatasheetWrap {
    flex: 1 1 300px;
}

#prodFooterActions #prodDetailInquiry {
    flex: 1 1 300px;
    width: auto;
}

#prodFooterActions #prodDatasheetBtn {
    width: 100%;
    height: 100%;
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

#prodDetailDesc .prod-desc-image {
    display: block;
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 10px 0 14px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
}

#prodDetailDesc .prod-desc-table,
#prodDetailDesc table.desc-custom-table,
#prodDetailDesc table.prod-desc-table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    margin: 14px 0 18px;
    background: #fff;
    border: 1px solid rgba(43, 17, 219, 0.14);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 10px 24px rgba(43, 17, 219, 0.08);
}

#prodDetailDesc .prod-desc-table thead,
#prodDetailDesc table.desc-custom-table thead,
#prodDetailDesc table.prod-desc-table thead {
    background: linear-gradient(180deg, #2f5f9d 0%, #1f4a82 55%, #183a67 100%);
}

#prodDetailDesc .prod-desc-table th,
#prodDetailDesc .prod-desc-table td,
#prodDetailDesc table.desc-custom-table th,
#prodDetailDesc table.desc-custom-table td,
#prodDetailDesc table.prod-desc-table th,
#prodDetailDesc table.prod-desc-table td {
    border: 1px solid rgba(43, 17, 219, 0.12);
    padding: 10px 12px;
    vertical-align: top;
    line-height: 1.6;
    word-break: break-word;
}

#prodDetailDesc .prod-desc-table th,
#prodDetailDesc table.desc-custom-table th,
#prodDetailDesc table.prod-desc-table th {
    color: #fff;
    font-weight: 700;
    text-align: center;
}

#prodDetailDesc .prod-desc-table tbody tr:nth-child(odd),
#prodDetailDesc table.desc-custom-table tbody tr:nth-child(odd),
#prodDetailDesc table.prod-desc-table tbody tr:nth-child(odd) {
    background: #fbfbfd;
}

#prodDetailDesc .prod-desc-table tbody tr:nth-child(even),
#prodDetailDesc table.desc-custom-table tbody tr:nth-child(even),
#prodDetailDesc table.prod-desc-table tbody tr:nth-child(even) {
    background: #fff;
}

#prodDetailDesc .prod-desc-table td,
#prodDetailDesc table.desc-custom-table td,
#prodDetailDesc table.prod-desc-table td {
    color: #374151;
}

#prodDetailDesc .prod-desc-table img,
#prodDetailDesc table.desc-custom-table img,
#prodDetailDesc table.prod-desc-table img {
    max-width: 100%;
    height: auto;
    display: block;
}

#prodDetailDesc .desc-cell-image-wrap,
#prodDetailDesc .prod-desc-image-wrap {
    position: relative;
    display: block;
    max-width: 100%;
    margin: 0;
}

#prodDetailDesc .desc-cell-image-wrap img,
#prodDetailDesc .prod-desc-image-wrap img {
    display: block;
    width: 100%;
    height: auto;
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
    line-height: 1.65;
    word-break: break-word;
    white-space: pre-line;
}

#prodDetailSpecsText .prod-specs-rows {
    display: grid;
    gap: 6px;
}

#prodDetailSpecsText .prod-specs-row {
    display: grid;
    grid-template-columns: minmax(130px, 220px) 14px minmax(0, 1fr);
    align-items: start;
    column-gap: 8px;
    font-size: 14px;
    line-height: 1.6;
    color: #3f4459;
}

#prodDetailSpecsText .prod-specs-row .spec-label {
    font-weight: 500;
}

#prodDetailSpecsText .prod-specs-row .spec-sep {
    text-align: center;
    opacity: 0.8;
}

#prodDetailSpecsText .prod-specs-row .spec-value {
    min-width: 0;
}

#prodDetailSpecsText .prod-specs-line,
#prodDetailSpecsText .prod-specs-numline,
#prodDetailSpecsText .prod-specs-spacer {
    font-size: 14px;
    line-height: 1.6;
    color: #3f4459;
}

#prodDetailSpecsText .prod-specs-numline {
    padding-left: 172px;
}

#prodDetailSpecsText .prod-specs-spacer {
    height: 6px;
}

#prodOverviewMediaWrap {
    width: 72%;
    max-width: 520px;
    margin-left: auto;
    margin-right: auto;
}

#prodOverviewMediaImg {
    display: block;
    width: 100%;
    height: 140px;
    object-fit: contain;
    border: none;
    background: transparent;
    box-shadow: none;
}

@media(max-width:900px){
    #prodDetailModal{max-width:95vw!important;width:95vw!important;max-height:90vh!important}
    #prodDetailName{font-size:clamp(26px,4vw,34px)!important}
    #prodDetailModal > div > div:first-child { flex: 0 0 38% !important; padding: 18px 28px !important; }
    #prodDetailModal > div > div:last-child { flex: 0 0 62% !important; padding: 22px 40px !important; }
    #prodImageGallery { height: clamp(220px, 38vh, 320px) !important; }
    #prodMainImg { max-height: 100% !important; padding: 18px !important; }
  #prodThumbnails img { width: 60px !important; height: 60px !important; }
    #relatedProductsGrid { grid-template-columns: repeat(2, 1fr) !important; gap: 8px !important; }
    #prodOverviewMediaWrap { width: 78%; max-width: 460px; }
    #prodOverviewMediaImg { height: 125px; background:transparent; box-shadow:none; border:none; }
}

@media(max-width:768px){
    #prodDetailModal{border-radius:24px 24px 0 0!important;max-height:92vh!important;width:100%!important;max-width:100%!important;height:auto!important;}
  #prodDetailModal > div {
    flex-wrap: wrap !important;
  }
        #prodDetailModal > div > div:first-child { order: 2; flex: 0 0 100% !important; border-radius: 0 !important; padding: 18px 24px !important; margin-bottom: 0; margin-top: 10px; }
        #prodDetailModal > div > div:last-child { order: 1; flex: 0 0 100% !important; padding: 18px 24px !important; }
        #prodDetailModal > div > div:nth-child(2) { display: none !important; }
  #prodImageGallery{height:300px!important}
  #prodMainImg{max-height:300px!important;padding:24px!important}
  #prodDetailName{font-size:32px!important}
  #prodThumbnails img { width:54px !important; height:54px !important; }
    #relatedProductsGrid { grid-template-columns: repeat(2, 1fr) !important; gap: 8px !important; }
    #prodFooterActions { flex-direction: column !important; gap: 10px !important; }
    #prodFooterActions #prodDatasheetWrap,
    #prodFooterActions #prodDetailInquiry { flex: 1 1 auto !important; width: 100% !important; }
    #prodFooterActions #prodDatasheetBtn { min-width: 0 !important; width: 100% !important; }
    #prodOverviewMediaWrap { width: 86% !important; max-width: 360px !important; margin-left: auto !important; margin-right: auto !important; }
    #prodOverviewMediaImg { height: 110px !important; background:transparent !important; box-shadow:none !important; border:none !important; }
}

@media(max-width:640px){
    #prodImageZoomOverlay{padding:16px!important}
    #prodImageZoomClose{top:12px!important;right:12px!important}
}

@media(max-width:768px){
    #prodDetailSpecsText .prod-specs-row {
        grid-template-columns: minmax(96px, 150px) 12px minmax(0, 1fr);
        column-gap: 6px;
    }

    #prodDetailSpecsText .prod-specs-numline {
        padding-left: 114px;
    }
}

/* Related Products Styling */
#relatedProductsWrap {
    padding: 6px;
    border-radius: 12px;
    background: linear-gradient(180deg, rgba(43,17,219,0.06) 0%, rgba(43,17,219,0.02) 100%);
    border: 1px solid rgba(43,17,219,0.12);
    min-height: 0;
    overflow-y: auto;
}

#relatedProductsGrid {
    gap: 6px;
    align-content: start;
    max-height: none;
    overflow: visible;
}

.related-products-title {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 900;
    color: #2B11DB;
    letter-spacing: 0.7px;
    margin-bottom: 8px;
    text-transform: uppercase;
    padding: 4px 8px;
    border-radius: 999px;
    border: 1px solid rgba(43,17,219,0.18);
    background: linear-gradient(135deg, rgba(43,17,219,0.08) 0%, rgba(43,17,219,0.03) 100%);
}

.related-products-title i {
    font-size: 10px;
}

.related-product-item {
  position: relative;
    border-radius: 14px;
    background: linear-gradient(155deg, #ffffff 0%, #f7f9ff 52%, #eef2ff 100%);
    border: 1px solid rgba(43,17,219,0.12);
  overflow: hidden;
  cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    box-shadow: 0 10px 24px rgba(43,17,219,0.13);
  display: flex;
  flex-direction: column;
  align-items: center;
    justify-content: flex-start;
    padding: 7px 6px 8px;
    min-height: 104px;
    height: auto;
}

.related-product-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, rgba(43,17,219,0.65) 0%, rgba(70,44,247,0.25) 100%);
    opacity: 0;
    transition: opacity 0.25s ease;
}

.related-product-item button,
.related-product-item a {
  display: none !important;
}

.related-product-item:hover {
  border-color: #2B11DB;
    box-shadow: 0 16px 32px rgba(43,17,219,0.26);
    transform: translateY(-5px);
}

.related-product-item:hover::before {
    opacity: 1;
}

.related-product-item:focus-visible {
    outline: 3px solid rgba(43,17,219,0.35);
    outline-offset: 2px;
}

.related-product-media {
    width: 100%;
    min-height: 58px;
    border-radius: 10px;
    background: radial-gradient(circle at 30% 25%, rgba(255,255,255,0.95) 0%, rgba(243,246,255,0.9) 58%, rgba(233,238,255,0.85) 100%);
    border: 1px solid rgba(43,17,219,0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
}

.related-product-item img {
    width: 80%;
    max-width: 118px;
        height: 46px;
  object-fit: contain;
    transition: transform 0.3s ease;
    margin-top: 0;
}

.related-product-item:hover img {
    transform: scale(1.06);
}

.related-product-name {
    font-size: clamp(11px, 0.72vw, 13.5px);
  font-weight: 800;
  color: #2B11DB;
  text-align: center;
    margin-top: 5px;
    line-height: 1.16;
    letter-spacing: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 24px;
    text-wrap: balance;
}

@media (max-width: 768px) {
    #relatedProductsWrap {
        padding: 6px;
        border-radius: 12px;
    }

    .related-products-title {
        font-size: 10.5px;
        letter-spacing: 0.6px;
        padding: 4px 7px;
    }

    .related-product-item {
        min-height: 112px;
        border-radius: 12px;
        padding: 7px 6px 8px;
    }

    .related-product-media {
        min-height: 62px;
    }

    .related-product-item img {
        height: 52px;
    }

    .related-product-name {
        font-size: 11px;
        min-height: 28px;
    }
}
</style>

<script>
(function(){
    var overlay          = document.getElementById('prodDetailOverlay');
    var modalShell       = document.getElementById('prodDetailModal');
    var closeBtn         = document.getElementById('prodDetailClose');
    var zoomOverlay      = document.getElementById('prodImageZoomOverlay');
    var zoomCloseBtn     = document.getElementById('prodImageZoomClose');
    var zoomImg          = document.getElementById('prodImageZoomImg');
    var detailInquiryBtn = document.getElementById('prodDetailInquiry');
    var currentDetailItem = {};
    var productImages = [];
    var currentImageIndex = 0;
    var modalSliderTimer = null;
    var resumeSliderAfterZoom = false;

    function getHeaderBottomOffset() {
        var selectors = ['header', '.site-header', '.main-header', '.top-header', '.navbar', '.header'];
        var maxBottom = 0;

        selectors.forEach(function(sel) {
            var nodes = document.querySelectorAll(sel);
            for (var i = 0; i < nodes.length; i++) {
                var el = nodes[i];
                if (!el) continue;
                var style = window.getComputedStyle(el);
                if (style.display === 'none' || style.visibility === 'hidden') continue;

                var rect = el.getBoundingClientRect();
                if (rect.height < 30) continue;
                if (rect.bottom <= 0) continue;

                // Prioritize bars docked near top of viewport.
                if (rect.top <= 8 && rect.bottom > maxBottom) {
                    maxBottom = rect.bottom;
                }
            }
        });

        return Math.max(0, Math.round(maxBottom));
    }

    function applyModalViewportLayout() {
        if (!overlay) return;

        var sidePad = Math.max(8, Math.round(window.innerWidth * 0.008));
        var verticalPad = Math.max(8, Math.round(window.innerHeight * 0.01));

        overlay.style.top = '0px';
        overlay.style.alignItems = 'center';
        overlay.style.paddingTop = verticalPad + 'px';
        overlay.style.paddingLeft = sidePad + 'px';
        overlay.style.paddingRight = sidePad + 'px';
        overlay.style.paddingBottom = verticalPad + 'px';

        if (modalShell) {
            var availableHeight = window.innerHeight - (verticalPad * 2);
            modalShell.style.maxHeight = Math.max(320, availableHeight) + 'px';
        }

        var contentScroll = document.getElementById('prodDetailContentScroll');
        if (contentScroll) {
            contentScroll.style.overflowY = 'auto';
            contentScroll.style.overflowX = 'hidden';
            contentScroll.style.maxHeight = '100%';
        }
    }

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

    function normalizeModalBrandName(brandName) {
        var cleanBrand = String(brandName || '').trim();
        if (!cleanBrand) return '';

        var lower = cleanBrand.toLowerCase();
        var compact = lower.replace(/[^a-z0-9]+/g, '');
        if (lower === 'bw' || lower === 'bw technologies' || compact === 'bw' || compact === 'bwtechnologies') return 'BW Technologies';
        if (lower === 'panasonic' || lower === 'panasonic connect') return 'Panasonic Connect';
        if (lower === 'hard worker' || lower === 'hard workers' || lower === 'hardworker') return 'HARDWORKER';
        if (lower === 'dryrod. ii' || lower === 'dryrod ii' || lower === 'phoenix dryrod' || lower === 'phoenix dry rod') return 'DryRod. II';
        if (lower === 'rae' || lower === 'rae systems' || lower === 'rac') return 'RAC';
        if (lower === 'weiler' || lower === 'weller') return 'Weller';

        return cleanBrand;
    }

    function getModalBrandLogo(brandName) {
        var cleanBrand = normalizeModalBrandName(brandName);
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

    function normalizeModalText(value) {
        return String(value || '').replace(/\s+/g, ' ').trim();
    }

    function decodeHtmlEntities(value) {
        var txt = document.createElement('textarea');
        txt.innerHTML = String(value || '');
        return txt.value;
    }

    function normalizeModalKey(value) {
        return normalizeModalText(value).toLowerCase().replace(/[^a-z0-9]+/g, '');
    }

    function firstNonEmptyText(values) {
        if (!Array.isArray(values)) return '';
        for (var i = 0; i < values.length; i++) {
            var candidate = values[i];
            if (candidate === null || candidate === undefined) continue;
            var asString = String(candidate);
            if (normalizeModalText(asString) !== '') {
                return asString;
            }
        }
        return '';
    }

    function getProductDescriptionValue(product) {
        if (!product || typeof product !== 'object') return '';
        return firstNonEmptyText([
            product.description,
            product.product_description,
            product.overview,
            product.product_overview
        ]);
    }

    function getProductNameValue(product) {
        if (!product || typeof product !== 'object') return '';
        return firstNonEmptyText([
            product.product_name,
            product.name,
            product.model
        ]);
    }

    function getCardDescriptionValue(card) {
        if (!card || typeof card.getAttribute !== 'function') return '';

        var fromData = firstNonEmptyText([
            card.getAttribute('data-description'),
            card.getAttribute('data-product-description'),
            card.getAttribute('data-overview')
        ]);
        if (normalizeModalText(fromData) !== '') {
            return fromData;
        }

        var descNode = card.querySelector('.product-description');
        if (descNode) {
            var fromNode = String(descNode.textContent || '');
            if (normalizeModalText(fromNode) !== '') {
                return fromNode;
            }
        }

        return '';
    }

    function formatDescriptionHtml(rawText) {
        var normalized = String(rawText || '')
            .replace(/\r\n?/g, '\n')
            .replace(/\u00A0/g, ' ')
            .replace(/[\u200B-\u200D\uFEFF]/g, '');

        var decoded = decodeHtmlEntities(normalized).trim();

        function sanitizeDescriptionHtml(rawHtml) {
            var parser = new DOMParser();
            var doc = parser.parseFromString('<div>' + rawHtml + '</div>', 'text/html');
            var sourceRoot = doc.body.firstElementChild;
            if (!sourceRoot) return '';

            var outDoc = document.implementation.createHTMLDocument('');
            var outRoot = outDoc.createElement('div');
            var allowedTags = {
                p: true,
                br: true,
                div: true,
                span: true,
                ul: true,
                ol: true,
                li: true,
                strong: true,
                b: true,
                em: true,
                i: true,
                u: true,
                img: true,
                table: true,
                thead: true,
                tbody: true,
                tfoot: true,
                tr: true,
                th: true,
                td: true,
            };

            function isSafeImageSrc(src) {
                var value = String(src || '').trim();
                if (value === '') return false;
                if (/^https?:\/\//i.test(value)) return true;
                if (value.indexOf('/') === 0) return true;
                if (value.indexOf('./') === 0 || value.indexOf('../') === 0) return true;
                if (value.indexOf('assets/') === 0 || value.indexOf('andison/assets/') === 0) return true;
                return false;
            }

            function sanitizeNode(node, parent) {
                if (!node) return;

                if (node.nodeType === Node.TEXT_NODE) {
                    parent.appendChild(outDoc.createTextNode(String(node.nodeValue || '')));
                    return;
                }

                if (node.nodeType !== Node.ELEMENT_NODE) {
                    return;
                }

                var tag = String(node.tagName || '').toLowerCase();
                if (tag === 'button' || tag === 'input' || tag === 'textarea' || tag === 'select' || tag === 'option') {
                    return;
                }

                if ((node.classList && (node.classList.contains('desc-cell-select-toggle') || node.classList.contains('desc-cell-image-delete'))) ||
                    node.getAttribute('data-desc-cell-select') || node.getAttribute('data-desc-image-delete')) {
                    return;
                }

                if (!allowedTags[tag]) {
                    var children = Array.prototype.slice.call(node.childNodes || []);
                    children.forEach(function(child) { sanitizeNode(child, parent); });
                    return;
                }

                var normalizedTag = tag;
                if (tag === 'b') normalizedTag = 'strong';
                if (tag === 'i') normalizedTag = 'em';

                var safeEl = outDoc.createElement(normalizedTag);

                if (normalizedTag === 'img') {
                    var src = String(node.getAttribute('src') || '').trim();
                    if (!isSafeImageSrc(src)) {
                        return;
                    }
                    safeEl.setAttribute('src', src);
                    safeEl.setAttribute('alt', String(node.getAttribute('alt') || '').trim());
                    safeEl.setAttribute('loading', 'lazy');
                    safeEl.setAttribute('decoding', 'async');
                    safeEl.className = 'prod-desc-image';
                    parent.appendChild(safeEl);
                    return;
                }

                if (normalizedTag === 'table') {
                    safeEl.className = String(node.getAttribute('class') || 'prod-desc-table').trim() || 'prod-desc-table';
                    safeEl.setAttribute('style', String(node.getAttribute('style') || ''));
                }

                if (normalizedTag === 'th' || normalizedTag === 'td' || normalizedTag === 'tr' || normalizedTag === 'thead' || normalizedTag === 'tbody' || normalizedTag === 'tfoot') {
                    var rowspan = String(node.getAttribute('rowspan') || '').trim();
                    var colspan = String(node.getAttribute('colspan') || '').trim();
                    if (rowspan !== '') safeEl.setAttribute('rowspan', rowspan);
                    if (colspan !== '') safeEl.setAttribute('colspan', colspan);
                    var tagClass = String(node.getAttribute('class') || '').trim();
                    if (tagClass !== '') safeEl.className = tagClass;
                }

                if (normalizedTag === 'div' || normalizedTag === 'span') {
                    var className = String(node.getAttribute('class') || '').trim();
                    if (className !== '') safeEl.className = className;
                }

                if (normalizedTag === 'img') {
                    var imgClass = String(node.getAttribute('class') || '').trim();
                    if (imgClass !== '') safeEl.className = imgClass;
                }

                var safeChildren = Array.prototype.slice.call(node.childNodes || []);
                safeChildren.forEach(function(child) { sanitizeNode(child, safeEl); });
                parent.appendChild(safeEl);
            }

            Array.prototype.slice.call(sourceRoot.childNodes || []).forEach(function(node) {
                sanitizeNode(node, outRoot);
            });

            var tableNodes = outRoot.querySelectorAll('table');
            tableNodes.forEach(function(table) {
                if (!table.className) table.className = 'prod-desc-table';
                table.querySelectorAll('td, th').forEach(function(cell) {
                    var hasEditor = cell.querySelector('.desc-cell-editor');
                    if (hasEditor) {
                        hasEditor.removeAttribute('contenteditable');
                    }
                    cell.removeAttribute('contenteditable');
                });
            });

            return outRoot.innerHTML.trim().replace(/[☐☑□]/g, '');
        }

        if (/<\s*(table|thead|tbody|tfoot|tr|th|td|img|p|ul|ol|li|br|strong|em|b|i|u|div|span)\b/i.test(decoded)) {
            var sanitizedHtml = sanitizeDescriptionHtml(decoded);
            if (sanitizedHtml !== '') {
                return sanitizedHtml;
            }
        }

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

        var displayText = normalized.replace(/^\n+|\n+$/g, '');
        if (displayText === '') return '';

        var lines = displayText.split('\n');
        var rows = [];

        function esc(v) {
            return escapeHtml(String(v || ''));
        }

        for (var i = 0; i < lines.length; i++) {
            var raw = lines[i].replace(/\t/g, '    ').replace(/\s+$/g, '');
            var trimmed = raw.trim();

            if (trimmed === '') {
                rows.push('<div class="prod-specs-spacer" aria-hidden="true"></div>');
                continue;
            }

            if (/^specifications?:?$/i.test(trimmed)) {
                continue;
            }

            var numMatch = trimmed.match(/^(\d+\.)\s*(.+)$/);
            if (numMatch) {
                rows.push(
                    '<div class="prod-specs-numline"><span class="spec-num">' + esc(numMatch[1]) + '</span> ' +
                    '<span class="spec-num-value">' + esc(numMatch[2].replace(/\s+/g, ' ').trim()) + '</span></div>'
                );
                continue;
            }

            var sepIdx = trimmed.indexOf(':');
            if (sepIdx > 0) {
                var label = trimmed.slice(0, sepIdx).replace(/\s+/g, ' ').trim();
                var value = trimmed.slice(sepIdx + 1).replace(/\s+/g, ' ').trim();

                if (label !== '' || value !== '') {
                    rows.push(
                        '<div class="prod-specs-row">' +
                            '<span class="spec-label">' + esc(label) + '</span>' +
                            '<span class="spec-sep">:</span>' +
                            '<span class="spec-value">' + esc(value) + '</span>' +
                        '</div>'
                    );
                    continue;
                }
            }

            rows.push('<div class="prod-specs-line">' + esc(trimmed.replace(/\s+/g, ' ')) + '</div>');
        }

        if (rows.length === 0) {
            return '<p class="prod-specs-paragraph">' + preserveInlineSpacing(displayText) + '</p>';
        }

        return '<div class="prod-specs-rows">' + rows.join('') + '</div>';
    }

    function normalizeSpecsArray(specsValue) {
        if (typeof specsValue === 'string') {
            var source = specsValue.trim();
            if (source !== '') {
                try {
                    var parsed = JSON.parse(source);
                    if (Array.isArray(parsed)) {
                        return parsed.map(function(item) {
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

                    if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                        // Structured matrix payloads are rendered from `specifications` text path.
                        if (parsed.format === 'andison_specs_v1' || parsed.format === 'andison_specs_v2' || parsed.format === 'andison_specs_v3') {
                            return [];
                        }

                        return Object.entries(parsed).map(function(entry) {
                            return {
                                label: String(entry[0] || '').trim(),
                                value: String(entry[1] || '').trim(),
                            };
                        }).filter(function(item) {
                            return item.label !== '' || item.value !== '';
                        });
                    }
                } catch (e) {
                    // Plain string specs are handled by text rendering path.
                }
            }
            return [];
        }

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
            tables: [],
            tableHtml: '',
            specImage: '',
        };

        var source = String(rawText || '').trim();
        if (!source) return payload;

        try {
            var parsed = JSON.parse(source);
            if (parsed && typeof parsed === 'object' && !Array.isArray(parsed)) {
                payload.specImage = String(parsed.specImage || parsed.spec_image || '').trim();
                var hasTables = Array.isArray(parsed.tables);
                var looksLikeV3 = parsed.format === 'andison_specs_v3' || hasTables;
                if (looksLikeV3 && hasTables) {
                    payload.text = String(parsed.text || '').trim();

                    payload.tables = parsed.tables.map(function(entry) {
                        if (!(entry && typeof entry === 'object' && !Array.isArray(entry))) return null;

                        var entryTableHtml = String(entry.tableHtml || '').trim();

                        var matrixRaw = entry.tableMatrix && typeof entry.tableMatrix === 'object' ? entry.tableMatrix : null;
                        if (!matrixRaw && entryTableHtml === '') return null;
                        if (!matrixRaw) {
                            return {
                                matrix: null,
                                table: [],
                                tableHtml: entryTableHtml,
                            };
                        }

                        var headers = Array.isArray(matrixRaw.headers)
                            ? matrixRaw.headers.map(function(h){ return String(h || '').trim(); })
                            : [];

                        var rows = Array.isArray(matrixRaw.rows) ? matrixRaw.rows : [];
                        var rowWidth = rows.reduce(function(maxLen, row) {
                            return Math.max(maxLen, Array.isArray(row) ? row.length : 0);
                        }, 0);
                        var colCount = Math.max(headers.length, rowWidth);
                        if (colCount === 0) return null;
                        while (headers.length < colCount) headers.push('');

                        var normalizedRows = rows.map(function(row) {
                            var out = Array.isArray(row) ? row.slice(0, colCount) : [];
                            while (out.length < colCount) out.push('');
                            return out.map(function(cell) { return String(cell || '').trim(); });
                        });

                        var mode = matrixRaw.mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';
                        var leadColumns = parseInt(matrixRaw.leadColumns, 10);
                        if (!isFinite(leadColumns) || leadColumns < 0) leadColumns = 0;
                        var groups = [];
                        var merges = [];
                        var rowMerges = [];

                        if (mode === 'grouped-pairs') {
                            if (leadColumns >= headers.length) {
                                leadColumns = Math.max(0, headers.length - 1);
                            }
                            var dataCols = Math.max(1, headers.length - leadColumns);
                            var rawGroups = Array.isArray(matrixRaw.groups) ? matrixRaw.groups : [];
                            groups = rawGroups.map(function(g) {
                                if (g && typeof g === 'object' && !Array.isArray(g)) {
                                    var spanObj = parseInt(g.span, 10);
                                    return {
                                        title: String(g.title || g.label || g.name || '').trim(),
                                        span: (isFinite(spanObj) && spanObj > 0) ? spanObj : 1,
                                        rowSpan: !!g.rowSpan,
                                    };
                                }
                                return { title: String(g || '').trim(), span: 2, rowSpan: false };
                            });

                            if (groups.length === 0) {
                                groups = [{ title: 'Free Air', span: dataCols, rowSpan: false }];
                            }
                        } else {
                            merges = (Array.isArray(matrixRaw.merges) ? matrixRaw.merges : []).map(function(m) {
                                var row = parseInt(m && m.row, 10);
                                var col = parseInt(m && m.col, 10);
                                var span = parseInt(m && m.span, 10);
                                if (!isFinite(row) || row < 0) return null;
                                if (!isFinite(col) || col < 1) return null;
                                if (!isFinite(span) || span < 2) return null;
                                if (col >= headers.length) return null;
                                var maxSpan = headers.length - col;
                                if (maxSpan < 2) return null;
                                if (span > maxSpan) span = maxSpan;
                                return { row: row, col: col, span: span };
                            }).filter(function(m) { return !!m; });
                        }

                        rowMerges = (Array.isArray(matrixRaw.rowMerges) ? matrixRaw.rowMerges : []).map(function(m) {
                            var row = parseInt(m && m.row, 10);
                            var col = parseInt(m && m.col, 10);
                            var rowSpan = parseInt(m && m.rowSpan, 10);
                            if (!isFinite(row) || row < 0) return null;
                            if (!isFinite(col) || col < 0 || col >= headers.length) return null;
                            if (!isFinite(rowSpan) || rowSpan < 2) return null;
                            return { row: row, col: col, rowSpan: rowSpan };
                        }).filter(function(m) { return !!m; });

                        return {
                            matrix: {
                                mode: mode,
                                leadColumns: leadColumns,
                                headers: headers,
                                rows: normalizedRows,
                                groups: groups,
                                merges: merges,
                                rowMerges: rowMerges,
                            },
                            table: [],
                            tableHtml: entryTableHtml,
                        };
                    }).filter(function(item) { return !!item; });

                    return payload;
                }

                var hasMatrix = parsed.tableMatrix && typeof parsed.tableMatrix === 'object';
                var looksLikeV2 = parsed.format === 'andison_specs_v2' || hasMatrix;

                if (looksLikeV2) {
                    payload.text = String(parsed.text || '').trim();
                    var matrixRaw = parsed.tableMatrix || {};
                    var headers = Array.isArray(matrixRaw.headers)
                        ? matrixRaw.headers.map(function(h){ return String(h || '').trim(); })
                        : [];

                    var rows = Array.isArray(matrixRaw.rows) ? matrixRaw.rows : [];
                    var rowWidth = rows.reduce(function(maxLen, row) {
                        return Math.max(maxLen, Array.isArray(row) ? row.length : 0);
                    }, 0);
                    var colCount = Math.max(headers.length, rowWidth);

                    if (colCount > 0) {
                        while (headers.length < colCount) headers.push('');
                        var normalizedRows = rows.map(function(row) {
                            var out = Array.isArray(row) ? row.slice(0, colCount) : [];
                            while (out.length < colCount) out.push('');
                            return out.map(function(cell) { return String(cell || '').trim(); });
                        });

                        var mode = matrixRaw.mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';
                        var leadColumns = parseInt(matrixRaw.leadColumns, 10);
                        if (!isFinite(leadColumns) || leadColumns < 0) leadColumns = 0;
                        var groups = [];
                        var merges = [];
                        var rowMerges = [];
                        if (mode === 'grouped-pairs') {
                            if (leadColumns >= headers.length) {
                                leadColumns = Math.max(0, headers.length - 1);
                            }
                            var dataCols = Math.max(1, headers.length - leadColumns);
                            var rawGroups = Array.isArray(matrixRaw.groups) ? matrixRaw.groups : [];
                            groups = rawGroups.map(function(g) {
                                if (g && typeof g === 'object' && !Array.isArray(g)) {
                                    var spanObj = parseInt(g.span, 10);
                                    return {
                                        title: String(g.title || g.label || g.name || '').trim(),
                                        span: (isFinite(spanObj) && spanObj > 0) ? spanObj : 1,
                                        rowSpan: !!g.rowSpan,
                                    };
                                }
                                return { title: String(g || '').trim(), span: 2, rowSpan: false };
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
                                return { title: title, span: g.span, rowSpan: g.span === 1 ? !!g.rowSpan : false };
                            });
                        } else {
                            merges = (Array.isArray(matrixRaw.merges) ? matrixRaw.merges : []).map(function(m) {
                                var row = parseInt(m && m.row, 10);
                                var col = parseInt(m && m.col, 10);
                                var span = parseInt(m && m.span, 10);
                                if (!isFinite(row) || row < 0) return null;
                                if (!isFinite(col) || col < 1) return null;
                                if (!isFinite(span) || span < 2) return null;
                                if (col >= headers.length) return null;
                                var maxSpan = headers.length - col;
                                if (maxSpan < 2) return null;
                                if (span > maxSpan) span = maxSpan;
                                return { row: row, col: col, span: span };
                            }).filter(function(m) { return !!m; });
                        }

                        rowMerges = (Array.isArray(matrixRaw.rowMerges) ? matrixRaw.rowMerges : []).map(function(m) {
                            var row = parseInt(m && m.row, 10);
                            var col = parseInt(m && m.col, 10);
                            var rowSpan = parseInt(m && m.rowSpan, 10);
                            if (!isFinite(row) || row < 0) return null;
                            if (!isFinite(col) || col < 0 || col >= headers.length) return null;
                            if (!isFinite(rowSpan) || rowSpan < 2) return null;
                            return { row: row, col: col, rowSpan: rowSpan };
                        }).filter(function(m) { return !!m; });

                        payload.matrix = {
                            mode: mode,
                            leadColumns: leadColumns,
                            headers: headers,
                            rows: normalizedRows,
                            groups: groups,
                            merges: merges,
                            rowMerges: rowMerges,
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

    function applyBodyBasedAutoTableSizing(table) {
        if (!table) return;

        table.style.width = '100%';
        table.style.minWidth = '0';
        table.style.maxWidth = '100%';
        table.style.tableLayout = 'fixed';

        // Remove existing colgroup and keep a fluid full-width table.
        var existingColgroups = table.querySelectorAll('colgroup');
        existingColgroups.forEach(function(cg) {
            if (cg && cg.parentNode === table) {
                table.removeChild(cg);
            }
        });
        return;
    }

    function renderSpecMatrixTable(matrix, table) {
        if (!matrix || !Array.isArray(matrix.headers) || matrix.headers.length === 0 || !table) return false;

        table.innerHTML = '';
        table.style.border = '1px solid #e8ecf4';

        var headers = matrix.headers.map(function(h) { return String(h || '').trim(); });
        var rows = Array.isArray(matrix.rows) ? matrix.rows : [];
        var mode = matrix.mode === 'grouped-pairs' ? 'grouped-pairs' : 'standard';

        var thead = document.createElement('thead');
        var tbody = document.createElement('tbody');

        if (mode === 'grouped-pairs' && headers.length >= 2) {
            var leadColumns = parseInt(matrix.leadColumns, 10);
            if (!isFinite(leadColumns) || leadColumns < 0) leadColumns = 0;
            if (leadColumns >= headers.length) leadColumns = Math.max(0, headers.length - 1);

            var dataColCount = Math.max(1, headers.length - leadColumns);
            var rawGroups = Array.isArray(matrix.groups) ? matrix.groups : [];
            var groups = rawGroups.map(function(g) {
                if (g && typeof g === 'object' && !Array.isArray(g)) {
                    var spanObj = parseInt(g.span, 10);
                    return {
                        title: String(g.title || g.label || g.name || '').trim(),
                        span: (isFinite(spanObj) && spanObj > 0) ? spanObj : 1,
                        rowSpan: !!g.rowSpan,
                    };
                }
                return { title: String(g || '').trim(), span: 2, rowSpan: false };
            });

            if (groups.length === 0) {
                groups = [{ title: 'Free Air', span: dataColCount, rowSpan: false }];
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

            groups = groups.map(function(g, idx) {
                var title = String(g.title || '').trim();
                if (title === '') title = idx === 0 ? 'Free Air' : ('Group ' + (idx + 1));
                return { title: title, span: g.span, rowSpan: g.span === 1 ? !!g.rowSpan : false };
            });

            var topTr = document.createElement('tr');

            for (var lc = 0; lc < leadColumns; lc++) {
                var leadTh = document.createElement('th');
                leadTh.rowSpan = 2;
                leadTh.textContent = headers[lc] || ('Column ' + (lc + 1));
                leadTh.style.padding = '16px 14px';
                leadTh.style.textAlign = 'center';
                leadTh.style.borderBottom = '1px solid rgba(162, 191, 235, 0.45)';
                leadTh.style.borderRight = '1px solid rgba(162, 191, 235, 0.45)';
                leadTh.style.background = 'linear-gradient(180deg,#2a5b9c 0%,#173865 100%)';
                leadTh.style.color = '#fff';
                leadTh.style.fontSize = '14px';
                leadTh.style.fontWeight = '900';
                topTr.appendChild(leadTh);
            }

            for (var g = 0; g < groups.length; g++) {
                var gTh = document.createElement('th');
                var span = parseInt(groups[g].span, 10);
                if (!isFinite(span) || span < 1) span = 1;
                if (span === 1 && groups[g].rowSpan) {
                    gTh.rowSpan = 2;
                } else {
                    gTh.colSpan = span;
                }
                gTh.textContent = groups[g].title || (g === 0 ? 'Free Air' : ('Group ' + (g + 1)));
                gTh.style.padding = '14px 12px';
                gTh.style.textAlign = 'center';
                gTh.style.borderBottom = '1px solid rgba(162, 191, 235, 0.45)';
                gTh.style.borderRight = '1px solid rgba(162, 191, 235, 0.45)';
                gTh.style.background = 'linear-gradient(180deg,#2a5b9c 0%,#173865 100%)';
                gTh.style.color = '#fff';
                gTh.style.fontSize = '14px';
                gTh.style.fontWeight = '900';
                topTr.appendChild(gTh);
            }

            thead.appendChild(topTr);

            var subTr = document.createElement('tr');
            var subHeaderCount = 0;
            var c = leadColumns;
            for (var gi2 = 0; gi2 < groups.length; gi2++) {
                var g2 = groups[gi2];
                var g2span = parseInt(g2.span, 10);
                if (!isFinite(g2span) || g2span < 1) g2span = 1;

                if (g2span === 1 && g2.rowSpan) {
                    c += 1;
                    continue;
                }

                for (var gs = 0; gs < g2span; gs++) {
                    var subTh = document.createElement('th');
                    subTh.textContent = headers[c] || ('Column ' + (c + 1));
                    subTh.style.padding = '12px 10px';
                    subTh.style.textAlign = 'center';
                    subTh.style.borderBottom = '1px solid rgba(162, 191, 235, 0.45)';
                    subTh.style.borderRight = '1px solid rgba(162, 191, 235, 0.45)';
                    subTh.style.background = 'linear-gradient(180deg,#234f8b 0%,#163154 100%)';
                    subTh.style.color = '#f3f4f6';
                    subTh.style.fontSize = '13px';
                    subTh.style.fontWeight = '800';
                    subTr.appendChild(subTh);
                    subHeaderCount += 1;
                    c += 1;
                }
            }
            if (subHeaderCount > 0) {
                thead.appendChild(subTr);
            }
        } else {
            var headerTr = document.createElement('tr');
            headerTr.style.fontWeight = '700';
            headerTr.style.background = 'linear-gradient(180deg,#2f5f9d 0%,#1f4a82 55%,#183a67 100%)';

            for (var h = 0; h < headers.length; h++) {
                var th = document.createElement('th');
                th.textContent = headers[h] || ('Column ' + (h + 1));
                th.style.padding = '16px 16px';
                th.style.textAlign = 'center';
                th.style.borderBottom = '1px solid rgba(203,220,242,0.55)';
                if (h < headers.length - 1) {
                    th.style.borderRight = '1px solid rgba(203,220,242,0.45)';
                }
                th.style.color = '#ffffff';
                th.style.fontSize = '14px';
                headerTr.appendChild(th);
            }
            thead.appendChild(headerTr);
        }

        var renderedRows = 0;
        var rowMergeStarts = {};
        var rowMergeCovered = {};
        if (Array.isArray(matrix.rowMerges)) {
            matrix.rowMerges.forEach(function(m) {
                var row = parseInt(m && m.row, 10);
                var col = parseInt(m && m.col, 10);
                var rowSpan = parseInt(m && m.rowSpan, 10);
                if (!isFinite(row) || !isFinite(col) || !isFinite(rowSpan)) return;
                if (row < 0 || col < 0 || rowSpan < 2) return;
                if (col >= headers.length) return;
                var maxSpan = rows.length - row;
                if (maxSpan < 2) return;
                if (rowSpan > maxSpan) rowSpan = maxSpan;

                rowMergeStarts[row + ':' + col] = rowSpan;
                for (var rr = row + 1; rr < row + rowSpan; rr++) {
                    rowMergeCovered[rr + ':' + col] = true;
                }
            });
        }

        for (var r = 0; r < rows.length; r++) {
            var row = Array.isArray(rows[r]) ? rows[r] : [];
            var safeRow = row.slice(0, headers.length);
            while (safeRow.length < headers.length) safeRow.push('');

            var explicitStarts = {};
            var explicitCovered = {};
            if (mode === 'standard' && Array.isArray(matrix.merges)) {
                matrix.merges.forEach(function(m) {
                    if (!m || m.row !== r) return;
                    var start = parseInt(m.col, 10);
                    var span = parseInt(m.span, 10);
                    if (!isFinite(start) || !isFinite(span) || span < 2) return;
                    if (start < 1 || start >= headers.length) return;
                    var maxSpan = headers.length - start;
                    if (maxSpan < 2) return;
                    if (span > maxSpan) span = maxSpan;
                    explicitStarts[start] = span;
                    for (var cc = start + 1; cc < start + span; cc++) {
                        explicitCovered[cc] = true;
                    }
                });
            }

            var rowHasAnyData = safeRow.some(function(cell) { return String(cell || '').trim() !== ''; });
            if (!rowHasAnyData) continue;

            var tr = document.createElement('tr');
            if (renderedRows % 2 === 0) tr.style.backgroundColor = '#f8fafb';

            for (var col = 0; col < headers.length; ) {
                if (rowMergeCovered[r + ':' + col]) {
                    col++;
                    continue;
                }

                if (mode === 'standard' && explicitCovered[col]) {
                    col++;
                    continue;
                }

                var cellValue = String(safeRow[col] || '');
                var span = 1;
                var hasExplicitMerge = mode === 'standard' && !!explicitStarts[col];

                if (hasExplicitMerge) {
                    span = explicitStarts[col];
                }

                // Keep standard matrix cells 1:1 with saved column positions.
                // Only apply colspan when an explicit merge exists in matrix.merges.

                var td = document.createElement('td');
                td.setAttribute('data-col-index', String(col));
                td.textContent = cellValue;

                var rowSpan = parseInt(rowMergeStarts[r + ':' + col], 10);
                if (isFinite(rowSpan) && rowSpan > 1) {
                    td.rowSpan = rowSpan;
                    td.style.verticalAlign = 'middle';
                    td.style.textAlign = 'left';
                }

                if (span > 1) {
                    td.colSpan = span;
                    td.style.textAlign = 'center';
                }
                td.style.padding = mode === 'grouped-pairs' ? '14px 14px' : '16px 16px';
                td.style.borderBottom = '1px solid #e8ecf4';
                var endCol = col + span - 1;
                if (endCol < headers.length - 1) {
                    td.style.borderRight = '1px solid #e8ecf4';
                }
                td.style.fontSize = '15px';
                td.style.whiteSpace = 'pre-line';
                if (mode === 'grouped-pairs' && !td.style.textAlign) {
                    td.style.textAlign = 'left';
                }

                // When col 0 is row-merged, the first visible cell in later rows can be col 1+.
                // Override generic td:first-child styling so non-col0 cells keep normal body colors.
                if (mode === 'grouped-pairs' && col !== 0) {
                    td.style.fontWeight = '500';
                    td.style.color = '#3a4559';
                    td.style.backgroundColor = (renderedRows % 2 === 0) ? '#f8fafb' : '#ffffff';
                }

                if (col === 0) {
                    td.style.fontWeight = '700';
                    td.style.color = '#2d3748';
                    td.style.backgroundColor = mode === 'grouped-pairs' ? 'rgba(17,24,39,0.04)' : 'rgba(43,17,219,0.04)';
                }
                tr.appendChild(td);
                col += span;
            }
            tbody.appendChild(tr);
            renderedRows++;
        }

        table.appendChild(thead);
        table.appendChild(tbody);
        applyBodyBasedAutoTableSizing(table);

        return headers.length > 0;
    }

    function renderSpecsTable(specsArr, table, matrix) {
        if (!table) return false;

        function getDisplayLabel(label) {
            var text = String(label || '').trim();
            return /^column\s+\d+$/i.test(text) ? '' : text;
        }

        if (matrix && renderSpecMatrixTable(matrix, table)) {
            return true;
        }

        table.innerHTML = '';
        table.style.border = '1px solid #e8ecf4';
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
            headerTr.style.background = 'linear-gradient(180deg,#2f5f9d 0%,#1f4a82 55%,#183a67 100%)';

            for (var h = 0; h < specsArr.length; h++) {
                var hSpec = specsArr[h];
                if (!hSpec.value) continue;

                var th = document.createElement('th');
                th.textContent = getDisplayLabel(hSpec.label);
                th.style.padding = '16px 16px';
                th.style.textAlign = 'center';
                th.style.borderBottom = '1px solid rgba(203,220,242,0.55)';
                th.style.borderRight = '1px solid rgba(203,220,242,0.45)';
                th.style.color = '#ffffff';
                th.style.fontSize = '14px';
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
                    if (!colSpec.value) continue;

                    var values = colSpec.value.split('|').map(function(v) { return v.trim(); });
                    var td = document.createElement('td');
                    td.textContent = values[rowIdx] || '';
                    td.style.padding = '14px 16px';
                    td.style.borderBottom = '1px solid #e8ecf4';
                    td.style.borderRight = '1px solid #e8ecf4';
                    td.style.fontSize = '15px';
                    td.style.whiteSpace = 'pre-line';
                    td.style.textAlign = 'left';
                    td.style.verticalAlign = 'middle';
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
                td1.textContent = getDisplayLabel(s.label);
                td2.textContent = s.value || '';
                td1.style.fontWeight = '700';
                td1.style.color = '#2d3748';
                td1.style.backgroundColor = 'rgba(43,17,219,0.04)';
                td1.style.borderBottom = '1px solid #e8ecf4';
                td1.style.borderRight = '1px solid #e8ecf4';
                td1.style.textAlign = 'left';
                td1.style.verticalAlign = 'middle';
                td2.style.whiteSpace = 'pre-line';
                td2.style.borderBottom = '1px solid #e8ecf4';
                td2.style.textAlign = 'left';
                td2.style.verticalAlign = 'middle';
                td1.style.padding = '14px 16px';
                td2.style.padding = '14px 16px';
                td1.style.fontSize = '15px';
                td2.style.fontSize = '15px';
                tr.appendChild(td1);
                tr.appendChild(td2);
                tbody.appendChild(tr);
            });
        }

        table.appendChild(tbody);
        return true;
    }

    function copyTableToClipboard(table) {
        if (!table) return;

        // Copy both HTML and plain text so pasting into spreadsheets/docs keeps table format when supported.
        var htmlContent = '<table border="1" cellspacing="0" cellpadding="6" style="border-collapse:collapse;">' + table.innerHTML + '</table>';
        var plainText = table.innerText || table.textContent || '';

        function fallbackCopyText(text) {
            var temp = document.createElement('textarea');
            temp.value = text;
            temp.setAttribute('readonly', '');
            temp.style.position = 'absolute';
            temp.style.left = '-9999px';
            document.body.appendChild(temp);
            temp.select();
            try {
                document.execCommand('copy');
                customAlert('Table copied.');
            } catch (err) {
                customAlert('Failed to copy table. Please try Ctrl+C.');
            }
            document.body.removeChild(temp);
        }

        if (navigator.clipboard && window.ClipboardItem) {
            var item = new ClipboardItem({
                'text/html': new Blob([htmlContent], { type: 'text/html' }),
                'text/plain': new Blob([plainText], { type: 'text/plain' }),
            });
            navigator.clipboard.write([item]).then(function() {
                customAlert('Table copied with formatting.');
            }).catch(function() {
                fallbackCopyText(plainText);
            });
            return;
        }

        fallbackCopyText(plainText);
    }

    function renderSpecifications(specsValue, rawSpecsText, specImageOverride) {
        var specsSection = document.getElementById('prodSpecsSection');
        var specsTextEl = document.getElementById('prodDetailSpecsText');
        var tableWrap = document.getElementById('prodSpecsTableWrap');
        var overviewMediaWrap = document.getElementById('prodOverviewMediaWrap');
        var overviewMediaImg = document.getElementById('prodOverviewMediaImg');

        if (!specsSection || !tableWrap) return false;

        var specsArr = normalizeSpecsArray(specsValue);
        var payload = parseSpecificationPayload(rawSpecsText);
        var hasText = payload.text !== '';
        var specImageRaw = firstNonEmptyText([specImageOverride || '', payload.specImage || '']);
        var specImageResolved = resolveModalPath(specImageRaw || '');

        if (overviewMediaWrap && overviewMediaImg) {
            if (specImageResolved) {
                overviewMediaImg.src = specImageResolved;
                overviewMediaImg.alt = 'Overview specifications image';
                overviewMediaWrap.style.display = 'block';
                overviewMediaImg.onerror = function() {
                    overviewMediaWrap.style.display = 'none';
                };
            } else {
                overviewMediaImg.src = '';
                overviewMediaWrap.style.display = 'none';
            }
        }

        if (specsTextEl) {
            if (hasText) {
                specsTextEl.innerHTML = formatSpecificationsHtml(payload.text);
                specsTextEl.style.display = 'block';
            } else {
                specsTextEl.innerHTML = '';
                specsTextEl.style.display = 'none';
            }
        }

        tableWrap.innerHTML = '';

        function appendRenderedTable(rows, matrix, tableHtml) {
            var table = document.createElement('table');
            table.className = 'prod-detail-specs-table';
            table.style.width = '100%';
            table.style.minWidth = '0';
            table.style.maxWidth = '100%';
            table.style.marginLeft = 'auto';
            table.style.marginRight = 'auto';
            table.style.tableLayout = 'fixed';
            table.style.borderCollapse = 'collapse';
            table.style.fontSize = '16px';

            var htmlSource = String(tableHtml || '').trim();
            if (htmlSource !== '') {
                var parser = new DOMParser();
                var doc = parser.parseFromString(htmlSource, 'text/html');
                var srcTable = doc.querySelector('table');
                if (srcTable) {
                    table.innerHTML = srcTable.innerHTML;
                    applyBodyBasedAutoTableSizing(table);
                    tableWrap.appendChild(table);
                    return true;
                }
            }

            if (!renderSpecsTable(rows || [], table, matrix || null)) {
                return false;
            }

            applyBodyBasedAutoTableSizing(table);
            tableWrap.appendChild(table);
            return true;
        }

        var renderedTables = 0;
        if (Array.isArray(payload.tables) && payload.tables.length > 0) {
            payload.tables.forEach(function(entry) {
                if (!entry) return;
                var tableRows = Array.isArray(entry.table) ? entry.table : [];
                var tableMatrix = entry.matrix && typeof entry.matrix === 'object' ? entry.matrix : null;
                var tableHtml = String(entry.tableHtml || '').trim();
                if (appendRenderedTable(tableRows, tableMatrix, tableHtml)) {
                    renderedTables += 1;
                }
            });
        }

        if (renderedTables === 0) {
            if (payload.matrix) {
                if (appendRenderedTable([], payload.matrix, payload.tableHtml || '')) {
                    renderedTables = 1;
                }
            } else {
                // Prefer the structured payload from `specifications` (admin save target).
                // Fall back to legacy `specs` only when payload has no table data.
                var tableRows = payload.table.length > 0 ? payload.table : specsArr;
                if (appendRenderedTable(tableRows, null, payload.tableHtml || '')) {
                    renderedTables = 1;
                }
            }
        }

        var hasTable = renderedTables > 0;

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

    function isZoomOpen() {
        return !!(zoomOverlay && zoomOverlay.style.display === 'flex');
    }

    function openImageZoom() {
        var mainImg = document.getElementById('prodMainImg');
        if (!zoomOverlay || !zoomImg || !mainImg) return;

        var src = String(mainImg.getAttribute('src') || '').trim();
        if (!src || mainImg.style.display === 'none') return;

        resumeSliderAfterZoom = !!modalSliderTimer && productImages.length > 1;
        stopModalSlider();

        zoomImg.src = src;
        zoomImg.alt = mainImg.alt || (document.getElementById('prodDetailName') ? document.getElementById('prodDetailName').textContent : 'Product image');
        zoomOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeImageZoom() {
        if (!zoomOverlay || !zoomImg || !isZoomOpen()) return;

        zoomOverlay.style.display = 'none';
        zoomImg.src = '';

        if (overlay && overlay.style.display === 'flex') {
            document.body.style.overflow = 'hidden';
            if (resumeSliderAfterZoom && productImages.length > 1) {
                startModalSlider();
            }
        } else {
            document.body.style.overflow = '';
        }

        resumeSliderAfterZoom = false;
    }

    var _jsonPath = resolveModalPath((typeof MODAL_JSON_PATH !== 'undefined') ? MODAL_JSON_PATH : 'Andison/data/brands_info_api.php');

    /* Load product details from JSON data */
    function loadProductDetails(brand, model, fallbackName, fallbackSpecs, fallbackSpecsText, fallbackDescription, options) {
        var opts = options && typeof options === 'object' ? options : {};
        var allowSpecsOverride = opts.allowSpecsOverride !== false;
        fetch(_jsonPath)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                var product = null;
                var targetModel = normalizeModalText(model).toLowerCase();
                var targetName = normalizeModalText(fallbackName).toLowerCase();
                var targetBrand = normalizeModalText(brand).toLowerCase();
                var targetModelKey = normalizeModalKey(model);
                var targetNameKey = normalizeModalKey(fallbackName);

                function scoreProductRecord(p) {
                    if (!p || typeof p !== 'object') return -999;

                    var itemModel = normalizeModalText((p && p.model) || '').toLowerCase();
                    var itemName = normalizeModalText((p && p.name) || '').toLowerCase();
                    var itemProductName = normalizeModalText((p && p.product_name) || '').toLowerCase();
                    var itemBrand = normalizeModalText((p && p.brand) || '').toLowerCase();
                    var itemModelKey = normalizeModalKey((p && p.model) || '');
                    var itemNameKey = normalizeModalKey((p && p.name) || '');
                    var itemProductNameKey = normalizeModalKey((p && p.product_name) || '');

                    var score = 0;

                    if (targetModel !== '' && (itemModel === targetModel || itemName === targetModel || itemProductName === targetModel)) {
                        score = Math.max(score, 120);
                    }

                    if (targetName !== '' && (itemModel === targetName || itemName === targetName || itemProductName === targetName)) {
                        score = Math.max(score, 110);
                    }

                    if (targetModelKey !== '' && (itemModelKey === targetModelKey || itemNameKey === targetModelKey || itemProductNameKey === targetModelKey)) {
                        score = Math.max(score, 100);
                    }

                    if (targetNameKey !== '' && (itemModelKey === targetNameKey || itemNameKey === targetNameKey || itemProductNameKey === targetNameKey)) {
                        score = Math.max(score, 95);
                    }

                    if (score <= 0) {
                        return -999;
                    }

                    if (targetBrand !== '') {
                        var targetBrandKey = normalizeModalKey(targetBrand);
                        var itemBrandKey = normalizeModalKey(itemBrand);

                        if (itemBrand === targetBrand) {
                            score += 40;
                        } else if (targetBrandKey !== '' && itemBrandKey !== '' && (itemBrandKey.indexOf(targetBrandKey) !== -1 || targetBrandKey.indexOf(itemBrandKey) !== -1)) {
                            score += 20;
                        } else {
                            score -= 10;
                        }
                    }

                    if (normalizeModalText(getProductDescriptionValue(p)) !== '') {
                        score += 8;
                    }

                    return score;
                }

                function findBestProduct(products) {
                    if (!Array.isArray(products) || products.length === 0) {
                        return null;
                    }

                    var best = null;
                    var bestScore = -999;
                    for (var i = 0; i < products.length; i++) {
                        var candidate = products[i];
                        var candidateScore = scoreProductRecord(candidate);
                        if (candidateScore > bestScore) {
                            best = candidate;
                            bestScore = candidateScore;
                        }
                    }

                    return bestScore <= -999 ? null : best;
                }

                
                // Check if data is an array (category JSON format) or object (brands_info format)
                if (Array.isArray(data)) {
                    // Category JSON format: [ { model, brand, specs, images, ... } ]
                    product = findBestProduct(data);
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
                        product = findBestProduct(brandData.products);
                        console.log('Searching in brands_info format, found:', !!product);
                    }

                    // Fallback: if brand is missing/mismatched, scan all brands by model/name.
                    if (!product) {
                        var allProducts = [];
                        for (var bKey in data) {
                            if (!Object.prototype.hasOwnProperty.call(data, bKey)) continue;
                            var bData = data[bKey];
                            if (!bData || !Array.isArray(bData.products)) continue;

                            allProducts = allProducts.concat(bData.products);
                        }
                        product = findBestProduct(allProducts);
                        console.log('Fallback model scan in brands_info format, found:', !!product);
                    }
                }
                
                if (!product) {
                    console.log('Product not found - brand:', brand, 'model:', model);
                    return;
                }
                
                console.log('Found product - loading specs and images');
                
                // Load description
                var mergedDescription = firstNonEmptyText([
                    getProductDescriptionValue(product),
                    fallbackDescription,
                    fallbackName,
                    model,
                    (product && product.type) ? String(product.type) : ''
                ]);
                if (normalizeModalText(mergedDescription) !== '') {
                    document.getElementById('prodDetailDesc').innerHTML = formatDescriptionHtml(mergedDescription);
                    document.getElementById('prodDescSection').style.display = 'block';
                } else {
                    document.getElementById('prodDescSection').style.display = 'none';
                }
                
                // Update specifications (plain text + optional table)
                var specsSource = (product && product.specs !== undefined) ? product.specs : null;
                var specsToRender = (Array.isArray(specsSource) || (specsSource && typeof specsSource === 'object'))
                    ? specsSource
                    : (fallbackSpecs || []);

                var specsTextToRender = '';
                var specImageToRender = '';
                if (product && typeof product.specifications === 'string' && product.specifications.trim() !== '') {
                    specsTextToRender = product.specifications;
                } else if (typeof specsSource === 'string' && specsSource.trim() !== '') {
                    specsTextToRender = specsSource;
                } else {
                    specsTextToRender = fallbackSpecsText || '';
                }
                specImageToRender = firstNonEmptyText([
                    (product && (product.spec_image || product.specImage || product.specification_image)) ? String(product.spec_image || product.specImage || product.specification_image) : '',
                    ''
                ]);
                if (allowSpecsOverride) {
                    renderSpecifications(specsToRender, specsTextToRender, specImageToRender);
                }
                
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
            closeImageZoom();
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
        if (isZoomOpen() && zoomImg) {
            zoomImg.src = productImages[idx];
        }
        
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
    function loadRelatedProducts(currentBrand, currentModel, contentMetric) {
        var grid = document.getElementById('relatedProductsGrid');
        var wrap = document.getElementById('relatedProductsWrap');
        var MAX_RELATED_PRODUCTS = 4;

        loadRelatedProducts._activeRequestId = (loadRelatedProducts._activeRequestId || 0) + 1;
        var requestId = loadRelatedProducts._activeRequestId;
        grid.innerHTML = '';

        function normalizeRelatedBrand(value) {
            var v = String(value || '').trim().toLowerCase();
            if (v === 'bw technologies' || v === 'bw') return 'bw';
            if (v === 'dryrod. ii' || v === 'dryrod ii' || v === 'phoenix dry rod' || v === 'phoenix dryrod') return 'dryrod. ii';
            return v;
        }

        function applyRelatedGridLayout(items) {
            var list = Array.isArray(items) ? items : [];
            var itemCount = list.length;
            var count = parseInt(itemCount, 10) || 0;
            if (count < 1) {
                wrap.style.display = 'none';
                return;
            }

            var lengths = list.map(function(p) {
                return String((p && p.model) || '').trim().length;
            });
            var maxLen = lengths.reduce(function(a, b) { return Math.max(a, b); }, 0);
            var avgLen = lengths.length ? (lengths.reduce(function(a, b) { return a + b; }, 0) / lengths.length) : 0;
            var hasLongTitles = maxLen >= 28 || avgLen >= 22;

            var cols = 2;
            if (count === 1) cols = 1;
            else if (count === 3) cols = hasLongTitles ? 2 : 3;
            else if (count >= 5) cols = 2;

            if (window.innerWidth <= 768) {
                cols = Math.min(2, count);
                if (cols < 1) cols = 1;
            }

            grid.style.gridTemplateColumns = 'repeat(' + cols + ', minmax(0, 1fr))';
            grid.style.maxHeight = 'none';
            grid.style.overflowY = 'visible';
            grid.style.overflowX = 'visible';

            if (count === 1) {
                grid.style.maxWidth = '260px';
                grid.style.margin = '0 auto';
            } else {
                grid.style.maxWidth = '100%';
                grid.style.margin = '0';
            }
        }
        
        fetch(_jsonPath)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (requestId !== loadRelatedProducts._activeRequestId) {
                    return;
                }

                var allProducts = [];
                
                // Check if data is an array (category JSON format) or object (brands_info format)
                if (Array.isArray(data)) {
                    // Category JSON format: [ { model, brand, ... } ]
                    // Collect products from same brand only
                    var currentBrandKey = normalizeRelatedBrand(currentBrand);
                    for (var i = 0; i < data.length; i++) {
                        var product = data[i];
                        if (product.model && product.model !== currentModel) {
                            // Match by canonicalized brand key to handle aliases (e.g. BW vs BW Technologies).
                            if (product.brand && normalizeRelatedBrand(product.brand) === currentBrandKey) {
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
                    if (!brandData) {
                        var targetBrandKey = normalizeRelatedBrand(currentBrand);
                        for (var k2 in data) {
                            if (normalizeRelatedBrand(k2) === targetBrandKey) {
                                brandData = data[k2];
                                break;
                            }
                        }
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

                // Remove duplicate model entries before randomizing the shortlist.
                var dedupedProducts = [];
                var seenRelatedKeys = {};
                for (var dedupeIdx = 0; dedupeIdx < allProducts.length; dedupeIdx++) {
                    var candidate = allProducts[dedupeIdx];
                    var modelKey = String((candidate && candidate.model) || '').trim().toLowerCase();
                    if (!modelKey) {
                        continue;
                    }
                    var brandKey = normalizeRelatedBrand((candidate && (candidate._brand || candidate.brand)) || currentBrand);
                    var relatedKey = brandKey + '::' + modelKey;
                    if (seenRelatedKeys[relatedKey]) {
                        continue;
                    }
                    seenRelatedKeys[relatedKey] = true;
                    dedupedProducts.push(candidate);
                }

                if (dedupedProducts.length === 0) {
                    wrap.style.display = 'none';
                    return;
                }
                
                // Keep related products compact: show up to 4 items.
                var maxItems = Math.min(dedupedProducts.length, MAX_RELATED_PRODUCTS);
                var relatedProducts = dedupedProducts.sort(function() { return Math.random() - 0.5; }).slice(0, maxItems);

                console.log('loadRelatedProducts: displaying', relatedProducts.length, 'items out of', dedupedProducts.length, 'deduped products');
                
                if (relatedProducts.length === 0) {
                    wrap.style.display = 'none';
                    return;
                }

                // Clear again inside callback to avoid stale async responses appending old items.
                grid.innerHTML = '';
                
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
                
                // Create items - dynamic count based on available related products.
                for (var itemIdx = 0; itemIdx < relatedProducts.length && itemIdx < MAX_RELATED_PRODUCTS; itemIdx++) {
                    var product = relatedProducts[itemIdx];
                    (function(product) {
                        var item = document.createElement('div');
                        item.className = 'related-product-item';
                        item.style.cursor = 'pointer';
                        item.setAttribute('tabindex', '0');
                        item.setAttribute('role', 'button');
                        item.setAttribute('aria-label', 'Open product ' + String(product.model || '').trim());
                        
                        var img = document.createElement('img');
                        img.style.maxWidth = '100%';
                        img.style.maxHeight = '100%';
                        img.style.objectFit = 'contain';

                        var media = document.createElement('div');
                        media.className = 'related-product-media';
                        
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
                        var titleLen = String(product.model || '').trim().length;
                        if (titleLen >= 36) {
                            name.style.webkitLineClamp = '3';
                        } else {
                            name.style.webkitLineClamp = '2';
                        }
                        name.textContent = product.model;
                        
                        media.appendChild(img);
                        item.appendChild(media);
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
                                    syntheticCard.setAttribute('data-description', getProductDescriptionValue(product));
                                    syntheticCard.setAttribute('data-product-name', product.product_name || product.name || '');
                                    syntheticCard.setAttribute('data-images', JSON.stringify(product.images || []));
                                    syntheticCard.setAttribute('data-specs', JSON.stringify(product.specs || []));
                                    syntheticCard.setAttribute('data-specifications', String(product.specifications || ''));
                                    syntheticCard.setAttribute('data-spec-image', String(product.spec_image || product.specImage || product.specification_image || ''));
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
                
                console.log('Final grid item count:', grid.children.length);

                applyRelatedGridLayout(relatedProducts);
                
                wrap.style.display = 'block';
            })
            .catch(function(err){
                if (requestId !== loadRelatedProducts._activeRequestId) {
                    return;
                }
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

        closeImageZoom();

        // SHOW MODAL IMMEDIATELY - don't wait for data loading
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';

        var modelNode = card.querySelector('.brand-product-name, .product-model');
        var modelText = modelNode ? normalizeModalText(modelNode.textContent || '') : '';
        var nameNode = card.querySelector('.brand-product-type, h4');
        var nameText = nameNode ? normalizeModalText(nameNode.textContent || '') : '';

        var model    = firstNonEmptyText([
            card.getAttribute('data-model'),
            card.getAttribute('data-product-model'),
            modelText,
            nameText
        ]);

        var type     = firstNonEmptyText([
            card.getAttribute('data-type'),
            card.querySelector('.add-to-inquiry') ? card.querySelector('.add-to-inquiry').getAttribute('data-type') : ''
        ]);
        var imgSrc   = card.getAttribute('data-image')  || '';
        var brand    = firstNonEmptyText([
            card.getAttribute('data-brand'),
            card.querySelector('.add-to-inquiry') ? card.querySelector('.add-to-inquiry').getAttribute('data-brand') : '',
            card.getAttribute('data-badge'),
            card.querySelector('.product-badge, .brand-badge, .badge') ? card.querySelector('.product-badge, .brand-badge, .badge').textContent : '',
            (typeof BRAND_NAME !== 'undefined' ? BRAND_NAME : '')
        ]);
        brand = normalizeModalBrandName(brand);
        var specsRaw = card.getAttribute('data-specs')  || '[]';
        var imagesRaw = card.getAttribute('data-images') || '[]';
        var specs    = [];
        var images   = [];
        try { specs = JSON.parse(specsRaw); } catch(e){}
        try { images = JSON.parse(imagesRaw); } catch(e){}

        // Extra fields from Supabase products
        var description    = getCardDescriptionValue(card);
        var specsText      = card.getAttribute('data-specifications') || '';
        var specImage      = card.getAttribute('data-spec-image') || '';
        if ((!specsText || !String(specsText).trim()) && typeof specs === 'string') {
            specsText = specs;
            specs = [];
        }
        var price          = card.getAttribute('data-price')          || '';
        var productName    = firstNonEmptyText([
            card.getAttribute('data-product-name'),
            card.getAttribute('data-name'),
            nameText,
            modelText
        ]);

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
        var immediateDescription = firstNonEmptyText([description, productName, type]);
        if (normalizeModalText(immediateDescription) !== '') {
            if (descEl)      descEl.innerHTML = formatDescriptionHtml(immediateDescription);
            if (descSection) descSection.style.display = 'block';
            if (noDetailsEl) noDetailsEl.style.display = 'none';
        } else if (descSection) {
            descSection.style.display = 'none';
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

        // Load additional data from JSON (description/specs) only when needed.
        // Prevents fuzzy JSON matches from overriding the currently opened card's specs/spec-image.
        var hasCardSpecsText = String(specsText || '').trim() !== '';
        var hasCardSpecsArray = Array.isArray(specs) && specs.length > 0;
        var shouldOverrideSpecsFromJson = !(hasCardSpecsText || hasCardSpecsArray);
        loadProductDetails(brand, model, productName, specs, specsText, description, {
            allowSpecsOverride: shouldOverrideSpecsFromJson,
        });

        /* Specifications (plain text + optional table) */
        var hasSpecs = renderSpecifications(specs, specsText, specImage);
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

        // Load related products with count based on visible content density.
        var contentMetric = 0;
        contentMetric += normalizeModalText(immediateDescription).length;
        contentMetric += normalizeModalText(specsText).length;
        if (hasSpecs) contentMetric += 220;
        loadRelatedProducts(brand, model, contentMetric);

        // Fit modal from just below header to bottom of viewport.
        applyModalViewportLayout();

        var contentScroll = document.getElementById('prodDetailContentScroll');
        if (contentScroll) {
            contentScroll.scrollTop = 0;
        }

        overlay.style.display    = 'flex';
        document.body.style.overflow = 'hidden';
    };

    /* ── Close ── */
    window.closeProductModal = function() {
        closeImageZoom();
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
    var mainImage = document.getElementById('prodMainImg');
    if (mainImage) {
        mainImage.addEventListener('click', openImageZoom);
    }
    if (zoomCloseBtn) {
        zoomCloseBtn.addEventListener('click', closeImageZoom);
        zoomCloseBtn.addEventListener('mouseenter', function(){ this.style.background = 'rgba(255,255,255,0.25)'; });
        zoomCloseBtn.addEventListener('mouseleave', function(){ this.style.background = 'rgba(255,255,255,0.14)'; });
    }
    if (zoomOverlay) {
        zoomOverlay.addEventListener('click', function(e){ if (e.target === zoomOverlay) closeImageZoom(); });
    }
    overlay.addEventListener('click', function(e){ if (e.target === overlay) closeProductModal(); });
    window.addEventListener('resize', function() {
        if (overlay && overlay.style.display === 'flex') {
            applyModalViewportLayout();
        }
    });
    document.addEventListener('keydown', function(e){
        if (e.key !== 'Escape') return;
        if (isZoomOpen()) {
            closeImageZoom();
            return;
        }
        closeProductModal();
    });

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

/* Category listing cards: use the centered, full-card style from the reference design */
.category-content .product-grid.grid-view {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
    gap: 20px !important;
}

.category-content .product-grid.grid-view .product-card {
    flex-direction: column !important;
    align-items: stretch !important;
    justify-content: flex-start !important;
    gap: 0 !important;
    padding: 0 0 16px !important;
    border-radius: 12px !important;
    border: 1px solid #d1d5db !important;
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08) !important;
}

.category-content .product-grid.grid-view .product-image {
    width: 100% !important;
    height: 220px !important;
    border-bottom: none !important;
    border-radius: 12px 12px 0 0 !important;
    background: #fff !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 12px 12px 6px !important;
}

.category-content .product-grid.grid-view .product-image > img,
.category-content .product-grid.grid-view .product-image > .card-img-slider {
    width: 100% !important;
    max-width: 96% !important;
    height: 168px !important;
    flex: 0 0 168px;
}

.category-content .product-grid.grid-view .product-image > img {
    object-fit: contain !important;
    padding: 0 !important;
}

.category-content .product-grid.grid-view .product-image .card-img-slider {
    position: relative;
    overflow: hidden;
}

.category-content .product-grid.grid-view .product-image .card-img-slider img {
    width: 100% !important;
    height: 100% !important;
    max-width: 100% !important;
    max-height: 100% !important;
    object-fit: contain !important;
    padding: 0 !important;
}

.category-content .product-grid.grid-view .product-badge {
    position: static !important;
    top: auto !important;
    right: auto !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    background: #2b11db !important;
    color: #fff !important;
    padding: 3px 10px !important;
    border-radius: 6px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0 !important;
    text-transform: none !important;
    box-shadow: none !important;
}

.category-content .product-grid.grid-view .product-info,
.category-content .product-grid.grid-view .product-card > div:last-child {
    width: 100%;
    padding: 8px 14px 0 !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 5px;
}

.category-content .product-grid.grid-view .product-model {
    padding: 0 !important;
    margin: 0 !important;
    color: #2b11db !important;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.3;
}

.category-content .product-grid.grid-view .product-card h4 {
    padding: 0 !important;
    margin: 0 !important;
    color: #2b11db !important;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.3;
}

/* When model exists above the title, make the second line supportive (not another highlight) */
.category-content .product-grid.grid-view .product-model + h4 {
    color: #888 !important;
    font-size: 12px;
    font-weight: 500;
}

.category-content .product-grid.grid-view .product-card .product-description {
    display: none !important;
}

.category-content .product-grid.grid-view .add-to-inquiry {
    width: 100% !important;
    margin-top: 4px;
    border-radius: 6px !important;
    padding: 9px 12px !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px;
}

@media (max-width: 768px) {
    .category-content .product-grid.grid-view {
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)) !important;
    }

    .category-content .product-grid.grid-view .product-image {
        height: 198px !important;
    }
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
                img.style.padding = '0';
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

