<?php
$page_title = "CO1/MAG Welding Machine";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo $page_title; ?> - Andison Industrial</title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; padding: 28px; color:#222 }
    a { color:#2B11DB; text-decoration:none }
    .container { max-width: 980px; margin: 0 auto }
    h1 { color:#2B11DB }
    .products { margin-top:18px }
    .prod { padding:12px 0; border-bottom:1px solid #eee }
  </style>
</head>
<body>
<!-- GLOBAL LOADER -->
<style>
.global-page-loader { position: fixed; inset: 0; background: linear-gradient(135deg, rgba(43, 17, 219, 0.96) 0%, rgba(30, 48, 140, 0.98) 100%); backdrop-filter: blur(10px); display: flex; align-items: center; justify-content: center; z-index: 999999; opacity: 1; visibility: visible; transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.global-page-loader.is-hidden { opacity: 0; visibility: hidden; pointer-events: none; }
.gpl-box { width: min(320px, 88vw); padding: 32px 24px; border-radius: 24px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.16); box-shadow: 0 24px 54px rgba(0, 0, 0, 0.4); display: flex; flex-direction: column; align-items: center; gap: 20px; transform: translateY(0); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
.global-page-loader.is-hidden .gpl-box { transform: translateY(15px) scale(0.95); }
.gpl-logo { width: 140px; height: 140px; object-fit: contain; filter: drop-shadow(0 6px 16px rgba(0, 0, 0, 0.4)); animation: gplPulse 2s ease-in-out infinite; }
.gpl-ring { width: 54px; height: 54px; border-radius: 50%; border: 4px solid rgba(255, 255, 255, 0.15); border-top-color: #00D7B3; animation: gplSpin 1s linear infinite; }
.gpl-text { margin: 0; color: #ffffff; font-size: 15px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; text-align: center; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
@keyframes gplPulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.06); } }
@keyframes gplSpin { to { transform: rotate(360deg); } }
html.gpl-loading, html.gpl-loading body { overflow: hidden !important; }
</style>
<div id="globalPageLoader" class="global-page-loader" aria-hidden="false">
    <div class="gpl-box" role="status" aria-live="polite" aria-label="Loading page">
        <img class="gpl-logo" id="gplLogoImgTop" src="/ANDISON/assets/HOME/image-removebg-preview.png" alt="ANDISON Logo">
        <div class="gpl-ring" aria-hidden="true"></div>
        <p class="gpl-text">Loading...</p>
    </div>
</div>
<script>
(function() {
    var loader = document.getElementById('globalPageLoader');
    if (!loader) return;
    document.documentElement.classList.add('gpl-loading');
    var gplLogoTop = document.getElementById('gplLogoImgTop');
    if (gplLogoTop) {
        var base = window.location.pathname.split('/').slice(0, window.location.pathname.split('/').findIndex(p => p.toLowerCase() === 'andison') + 1).join('/');
        if (base && base !== '/') gplLogoTop.src = base + '/assets/HOME/image-removebg-preview.png';
    }
    
    function hideLoader() {
        if (loader.classList.contains('is-hidden')) return;
        loader.classList.add('is-hidden');
        loader.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('gpl-loading');
    }
    
    if (document.readyState === 'complete') {
        setTimeout(hideLoader, 150);
    } else {
        window.addEventListener('load', function() {
            setTimeout(hideLoader, 150);
        });
    }
    
    setTimeout(hideLoader, 5000);
    
    document.addEventListener('click', function(e) {
        var target = e.target.closest('a');
        if (!target) return;
        var href = target.getAttribute('href');
        if (!href) return;
        if (href.startsWith('javascript:') || href.startsWith('#') || target.getAttribute('target') === '_blank' || href.startsWith('tel:') || href.startsWith('mailto:') || e.ctrlKey || e.shiftKey || e.metaKey || e.button !== 0) return;
        var isInternal = false;
        try {
            var url = new URL(target.href, window.location.href);
            if (url.origin === window.location.origin) isInternal = true;
        } catch (err) {}
        if (isInternal) {
            var currentUrl = new URL(window.location.href);
            var targetUrl = new URL(target.href);
            if (currentUrl.pathname === targetUrl.pathname && currentUrl.search === targetUrl.search && targetUrl.hash) return;
            loader.classList.remove('is-hidden');
            loader.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('gpl-loading');
        }
    });
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) hideLoader();
    });
})();
</script>
<!-- /GLOBAL LOADER -->
  <div class="container">
    <a href="../home.php">← Back to Home</a>
    <h1><?php echo $page_title; ?></h1>
    <p>Products in this category will be listed here. Populate this file with product listings or include dynamic product fetching.</p>
    <div class="products">
      <div class="prod">No products yet — add product blocks or include a catalog.</div>
    </div>
  </div>
  <script src="../../assets/js/scroll-fade.js"></script>
</body>
</html>






