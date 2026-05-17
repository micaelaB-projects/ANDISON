<style>
/* Shared modern footer for legacy pages */

/* Global nav dropdown readability fix for all pages.
   Exclude logo-only brand links so image tiles keep their custom styling. */
nav .nav-dropdown ul a:not(.andison-nav-brand-link) {
    color: #374151 !important;
    -webkit-text-fill-color: #374151 !important;
    font-weight: 600 !important;
    opacity: 1 !important;
    text-shadow: none !important;
}

nav .nav-dropdown ul a:not(.andison-nav-brand-link):hover,
nav .nav-dropdown ul a:not(.andison-nav-brand-link):focus-visible,
nav .nav-dropdown ul li.active > a:not(.andison-nav-brand-link),
nav .nav-dropdown ul a[aria-current="page"]:not(.andison-nav-brand-link) {
    background: #e2e7f5 !important;
    color: #1f2fa9 !important;
    -webkit-text-fill-color: #1f2fa9 !important;
    outline: none !important;
    opacity: 1 !important;
    text-shadow: none !important;
}

/* Global override: make Brands hover logos larger and cards tighter across pages. */
nav li:nth-child(3)
.nav-dropdown {
    min-width: 700px !important;
    max-width: 700px !important;
    padding: 18px 20px !important;
}

nav li:nth-child(3) .nav-dropdown ul {
    display: grid !important;
    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
    gap: 2px 4px !important;
    margin-top: 10px !important;
}

nav li:nth-child(3) .nav-dropdown ul li {
    min-height: 74px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

nav li:nth-child(3) .nav-dropdown ul a {
    min-height: 68px !important;
    padding: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

nav li:nth-child(3) .nav-dropdown ul a img {
    width: 124px !important;
    height: 64px !important;
    max-width: none !important;
    max-height: none !important;
    object-fit: contain !important;
}

@media (max-width: 768px) {
    nav li:nth-child(3)
.nav-dropdown {
        min-width: min(94vw, 620px) !important;
        max-width: min(94vw, 620px) !important;
    }

    nav li:nth-child(3) .nav-dropdown ul {
        gap: 6px 8px !important;
    }

    nav li:nth-child(3) .nav-dropdown ul a img {
        width: 92px !important;
        height: 48px !important;
    }
}

footer.footer-modernized {
    background: linear-gradient(135deg, #2209c9 0%, #2b11db 52%, #1b0893 100%) !important;
    color: #eef1ff !important;
    padding: 56px 0 0 !important;
    border-top: 1px solid rgba(255, 255, 255, 0.14) !important;
    position: relative;
    width: 100% !important;
    left: 0 !important;
    right: 0 !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
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
    gap: 22px;
    position: relative;
    z-index: 1;
}

footer.footer-modernized .footer-main-grid {
    display: grid;
    grid-template-columns: minmax(250px, 1.35fr) minmax(210px, 1.05fr) minmax(210px, 1.05fr) minmax(180px, 0.95fr) minmax(120px, 0.72fr) !important;
    gap: 34px !important;
    align-items: start;
    grid-auto-flow: row;
}

footer.footer-modernized .footer-brand-col,
footer.footer-modernized .footer-col {
    min-width: 0;
}

footer.footer-modernized .footer-brand-logo {
    display: inline-block;
    margin-bottom: 12px;
}

footer.footer-modernized .footer-brand-logo img {
    width: 228px;
    max-width: 100%;
    height: auto;
    display: block;
    filter: brightness(0) invert(1);
}

footer.footer-modernized .footer-brand-blurb {
    margin: 0;
    font-size: 15px;
    line-height: 1.58;
    color: rgba(239, 243, 255, 0.9);
    max-width: 330px;
}

footer.footer-modernized .footer-col-title {
    margin: 4px 0 14px;
    color: #ffffff;
    font-size: 15px;
    line-height: 1.05;
    letter-spacing: 0.5px;
    font-weight: 800;
    text-transform: uppercase;
    text-align: center;
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
    font-size: 15px;
    line-height: 1.5;
}

footer.footer-modernized .footer-contact-item i {
    color: #dde4ff;
    font-size: 15px;
    margin-top: 4px;
    flex-shrink: 0;
}

footer.footer-modernized .footer-nav-links {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    background: transparent !important;
    border: 0 !important;
    box-shadow: none !important;
    padding: 0 !important;
}

footer.footer-modernized .footer-nav-links a {
    color: rgba(255, 255, 255, 0.96);
    text-decoration: none;
    font-size: 15px;
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

footer.footer-modernized .footer-socials {
    margin-top: 0;
}

footer.footer-modernized .footer-social-col {
    justify-self: center;
    align-self: start;
}

footer.footer-modernized .footer-qr-col {
    justify-self: start;
    align-self: start;
}

footer.footer-modernized .footer-main-grid > .footer-col:last-child {
    grid-column: auto !important;
}

footer.footer-modernized .footer-socials-title {
    margin: 0 0 10px;
    color: #ffffff;
    font-size: 13px;
    letter-spacing: 0.7px;
    font-weight: 800;
    text-transform: uppercase;
}

footer.footer-modernized .footer-social-links {
    display: flex;
    gap: 10px;
    align-items: center;
    justify-content: flex-start;
}

footer.footer-modernized .footer-social-link {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f3f5ff;
    border: 1px solid #d7defd;
    color: #2b11db;
    text-decoration: none;
    font-size: 16px;
    box-shadow: 0 6px 14px rgba(8, 16, 52, 0.2);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

footer.footer-modernized .footer-social-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 18px rgba(8, 16, 52, 0.28);
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
    font-size: 15px;
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

/* Normalize Featured Brands dropdown logo sizing site-wide. */
nav li:nth-child(3)
.nav-dropdown {
    min-width: 1080px !important;
    max-width: 1080px !important;
    padding: 8px 10px !important;
}

nav li:nth-child(3) .nav-dropdown ul {
    grid-template-columns: repeat(5, 208px) !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 0 2px !important;
}

nav li:nth-child(3) .nav-dropdown ul li {
    width: 208px !important;
    height: 86px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

nav li:nth-child(3) .nav-dropdown ul a {
    min-height: 86px !important;
    width: 208px !important;
    height: 86px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 2px !important;
    line-height: 1 !important;
    background: transparent !important;
    border: 1px solid transparent !important;
    border-radius: 12px !important;
    box-shadow: none !important;
    transform: none !important;
    overflow: hidden !important;
    transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease !important;
}

nav li:nth-child(3) .nav-dropdown ul a:hover {
    background: #ffffff !important;
    border-color: #00D7B3 !important;
    box-shadow: 0 8px 24px rgba(0, 215, 179, 0.22) !important;
    transform: translateY(-4px) !important;
}

nav li:nth-child(3) .nav-dropdown ul a img {
    width: 208px !important;
    height: 86px !important;
    max-width: 208px !important;
    max-height: 86px !important;
    object-fit: contain !important;
    object-position: center center !important;
    display: block !important;
    transform: translate(var(--brand-shift-x, 0px), var(--brand-shift-y, 0px)) scale(var(--brand-auto-scale, 1.22)) !important;
    transform-origin: center center !important;
    filter: grayscale(25%) !important;
    transition: filter 0.2s ease, transform 0.2s ease !important;
}

nav li:nth-child(3) .nav-dropdown ul a:hover img {
    filter: grayscale(0%) !important;
}

@media (max-width: 1180px) {
    footer.footer-modernized .footer-main-grid {
        grid-template-columns: repeat(2, minmax(220px, 1fr)) !important;
        gap: 16px 20px;
    }

    nav li:nth-child(3)
.nav-dropdown {
        min-width: 860px !important;
        max-width: 860px !important;
        padding: 7px 9px !important;
    }

    nav li:nth-child(3) .nav-dropdown ul {
        grid-template-columns: repeat(5, 157px) !important;
        justify-content: center !important;
        gap: 0 2px !important;
    }

    nav li:nth-child(3) .nav-dropdown ul li,
    nav li:nth-child(3) .nav-dropdown ul a {
        width: 157px !important;
        height: 65px !important;
        min-height: 65px !important;
    }

    nav li:nth-child(3) .nav-dropdown ul a img {
        width: 157px !important;
        height: 65px !important;
        max-width: 157px !important;
        max-height: 65px !important;
        transform: scale(1.08) !important;
        transform-origin: center center !important;
    }

    nav li:nth-child(3) .nav-dropdown ul a img[alt="Kobelco"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="Metrode"] {
        transform: translateX(-10px) scale(1.2) !important;
    }

    nav li:nth-child(3) .nav-dropdown ul a img[alt="MAGNAFLUX"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="Tempilstik"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="SK And GAL GAGE"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="RAC"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="Spilfyter"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="Technotex"] {
        transform: scale(1.14) !important;
    }

    nav li:nth-child(3) .nav-dropdown ul a img[alt="Dalo"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="DALO"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="DryRod. II"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="BW"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="BW Technologies"] {
        transform: scale(0.95) !important;
    }

    footer.footer-modernized .footer-col-title { font-size: 12px; }
    footer.footer-modernized .footer-nav-links a { font-size: 12px; }
    footer.footer-modernized .footer-copyright { font-size: 12px; }

    footer.footer-modernized .footer-social-col {
        grid-column: auto;
        justify-self: start;
    }

    footer.footer-modernized .footer-qr-col {
        grid-column: auto;
        justify-self: start;
    }
}

@media (max-width: 768px) {
    footer.footer-modernized {
        padding-top: 24px !important;
    }

    footer.footer-modernized .footer-content {
        gap: 8px;
        padding: 0 10px 8px;
    }

    footer.footer-modernized .footer-main-grid {
        grid-template-columns: 1fr !important;
        gap: 8px;
        align-items: stretch;
    }

    footer.footer-modernized .footer-brand-col {
        grid-column: 1 / -1;
        padding: 10px 11px 9px;
        border-radius: 0;
        background: transparent;
        border: 0;
        backdrop-filter: none;
        box-shadow: none;
        position: relative;
        overflow: visible;
    }

    footer.footer-modernized .footer-brand-col::before,
    footer.footer-modernized .footer-col::before {
        content: none;
        position: absolute;
        left: 10px;
        right: 10px;
        top: 0;
        height: 1px;
        background: linear-gradient(90deg, rgba(255,255,255,0), rgba(255,255,255,0.35), rgba(255,255,255,0));
        pointer-events: none;
    }

    footer.footer-modernized .footer-brand-logo {
        margin-bottom: 6px;
    }

    footer.footer-modernized .footer-brand-logo img {
        width: min(170px, 62vw);
        max-height: 56px;
        object-fit: contain;
        object-position: left center;
    }

    footer.footer-modernized .footer-brand-blurb {
        display: none;
    }

    footer.footer-modernized .footer-col {
        padding: 10px 9px 9px;
        border-radius: 0;
        background: transparent;
        border: 0;
        backdrop-filter: none;
        box-shadow: none;
        min-height: 100%;
        position: relative;
        overflow: visible;
    }

    footer.footer-modernized .footer-col:last-child {
        grid-column: 1 / -1;
    }

    footer.footer-modernized .footer-col-title {
        font-size: 11px;
        margin: 0 0 7px;
        text-align: center;
    }
    footer.footer-modernized .footer-nav-links {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 6px;
    }
    footer.footer-modernized .footer-nav-links a {
        font-size: 11px;
        line-height: 1.1;
        padding: 6px 2px;
        border-radius: 0;
        background: transparent;
        text-align: left;
        width: 100%;
        min-height: auto;
        display: flex;
        align-items: center;
        border: 0;
        transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }
    footer.footer-modernized .footer-nav-links a:hover {
        background: transparent;
        border-color: transparent;
        transform: none;
    }
    footer.footer-modernized .footer-nav-links a:nth-child(1) { order: 1; }
    footer.footer-modernized .footer-nav-links a:nth-child(2) { order: 3; }
    footer.footer-modernized .footer-nav-links a:nth-child(3) { order: 5; }
    footer.footer-modernized .footer-nav-links a:nth-child(4) { order: 2; }
    footer.footer-modernized .footer-nav-links a:nth-child(5) { order: 4; }
    footer.footer-modernized .footer-nav-links a:nth-child(6) { order: 6; }
    footer.footer-modernized .footer-contact-list { gap: 5px; }
    footer.footer-modernized .footer-contact-item { font-size: 11px; gap: 7px; line-height: 1.3; color: rgba(248, 250, 255, 0.92); }
    footer.footer-modernized .footer-contact-item span { overflow-wrap: anywhere; word-break: break-word; }
    footer.footer-modernized .footer-contact-item i { font-size: 12px; margin-top: 2px; color: rgba(230,236,255,0.95); }
    footer.footer-modernized .footer-socials { margin-top: 10px; }
    footer.footer-modernized .footer-socials-title { font-size: 11px; margin-bottom: 7px; }
    footer.footer-modernized .footer-social-link { width: 30px; height: 30px; font-size: 14px; }

    footer.footer-modernized .footer-bottom {
        flex-direction: column;
        align-items: center;
        padding-right: 0;
        padding-left: 0;
        padding-bottom: 10px;
        padding-top: 7px;
        gap: 6px;
        border-top-color: rgba(255, 255, 255, 0.24);
    }

    footer.footer-modernized .footer-copyright { font-size: 11px; line-height: 1.35; }

    footer.footer-modernized .footer-scroll-top {
        width: 38px;
        height: 38px;
        right: 12px;
        bottom: 12px;
    }

    nav li:nth-child(3) .nav-dropdown ul {
        grid-template-columns: repeat(5, 79px) !important;
        justify-content: center !important;
        gap: 0 1px !important;
    }

    nav li:nth-child(3) .nav-dropdown ul li,
    nav li:nth-child(3) .nav-dropdown ul a {
        width: 79px !important;
        height: 34px !important;
        min-height: 34px !important;
    }

    nav li:nth-child(3) .nav-dropdown ul a img {
        width: 79px !important;
        height: 34px !important;
        max-width: 79px !important;
        max-height: 34px !important;
        transform: scale(1.06) !important;
        transform-origin: center center !important;
    }

    nav li:nth-child(3) .nav-dropdown ul a img[alt="Kobelco"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="Metrode"] {
        transform: translateX(-5px) scale(1.12) !important;
    }

    nav li:nth-child(3) .nav-dropdown ul a img[alt="MAGNAFLUX"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="Tempilstik"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="SK And GAL GAGE"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="RAC"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="Spilfyter"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="Technotex"] {
        transform: scale(1.1) !important;
    }

    nav li:nth-child(3) .nav-dropdown ul a img[alt="Dalo"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="DALO"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="DryRod. II"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="BW"],
    nav li:nth-child(3) .nav-dropdown ul a img[alt="BW Technologies"] {
        transform: scale(0.94) !important;
    }
}
</style>

<?php
require_once __DIR__ . '/../Andison/includes/footer_settings.php';
require_once __DIR__ . '/../Andison/includes/brands_info.php';

if (!function_exists('andison_footer_normalize_brand_key')) {
    function andison_footer_normalize_brand_key(string $brand): string
    {
        $normalized = strtolower(trim($brand));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        if ($normalized === 'dryrod ii' || $normalized === 'dryrod. ii' || $normalized === 'phoenix dryrod' || $normalized === 'phoenix dry rod') {
            return 'dryrod. ii';
        }
        if ($normalized === 'bw technologies' || $normalized === 'bw') {
            return 'bw';
        }
        if ($normalized === 'rae' || $normalized === 'rac' || $normalized === 'rae systems') {
            return 'rae systems';
        }
        if ($normalized === 'robot systems' || $normalized === 'robot system peripherals' || $normalized === 'robot systems peripherals') {
            return 'robot systems peripherals';
        }
        if ($normalized === 'hard worker' || $normalized === 'hard workers' || $normalized === 'hardworker') {
            return 'hardworker';
        }
        if ($normalized === 'weller' || $normalized === 'weiler') {
            return 'weiler';
        }
        if ($normalized === 'panasonic') {
            return 'panasonic connect';
        }

        return $normalized;
    }
}

$andisonFooterBrandLogoMap = [];
try {
    $andisonFooterBrands = andison_get_brands_info();
    if (is_array($andisonFooterBrands)) {
        foreach ($andisonFooterBrands as $brandName => $brandInfo) {
            if (!is_array($brandInfo)) {
                continue;
            }

            $logo = trim((string)($brandInfo['logo'] ?? ''));
            if ($logo === '') {
                continue;
            }

            $baseKey = andison_footer_normalize_brand_key((string)$brandName);
            if ($baseKey === '') {
                continue;
            }

            $aliases = [$baseKey];
            if ($baseKey === 'dryrod. ii') {
                $aliases = array_merge($aliases, ['dryrod ii', 'phoenix dryrod', 'phoenix dry rod']);
            } elseif ($baseKey === 'bw') {
                $aliases[] = 'bw technologies';
            } elseif ($baseKey === 'rae systems') {
                $aliases = array_merge($aliases, ['rae', 'rac']);
            } elseif ($baseKey === 'robot systems peripherals') {
                $aliases = array_merge($aliases, ['robot systems', 'robot system peripherals']);
            } elseif ($baseKey === 'hardworker') {
                $aliases = array_merge($aliases, ['hard worker', 'hard workers']);
            } elseif ($baseKey === 'weiler') {
                $aliases[] = 'weller';
            } elseif ($baseKey === 'panasonic connect') {
                $aliases[] = 'panasonic';
            }

            foreach (array_unique($aliases) as $alias) {
                if (!isset($andisonFooterBrandLogoMap[$alias])) {
                    $andisonFooterBrandLogoMap[$alias] = $logo;
                }
            }
        }
    }
} catch (Throwable $e) {
    $andisonFooterBrandLogoMap = [];
}

$andisonFooterSettings = andison_get_footer_settings();
?>

<script>
(function(){
    var footerSettings = <?php echo json_encode($andisonFooterSettings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
    var brandLogoMap = <?php echo json_encode($andisonFooterBrandLogoMap, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

    function escHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escHtmlWithBreaks(value) {
        return escHtml(value).replace(/\n/g, '<br>');
    }

    function footerContactItem(iconClass, text) {
        var safeText = String(text || '').trim();
        if (!safeText) {
            return '';
        }
        return '<li class="footer-contact-item"><i class="' + iconClass + '"></i><span>' + escHtml(safeText) + '</span></li>';
    }

    function footerContactEmailItem(iconClass, email) {
        var safeEmail = String(email || '').trim();
        if (!safeEmail) {
            return '';
        }
        return '<li class="footer-contact-item"><i class="' + iconClass + '"></i><span><a href="mailto:' + escHtml(safeEmail) + '" style="color:inherit;text-decoration:none;">' + escHtml(safeEmail) + '</a></span></li>';
    }

    function footerNavLink(href, label) {
        var safeHref = String(href || '').trim();
        var safeLabel = String(label || '').trim();
        if (!safeHref || !safeLabel) {
            return '';
        }
        return '<a href="' + escHtml(safeHref) + '">' + escHtml(safeLabel) + '</a>';
    }

    function footerSocialLink(href, iconClass, label) {
        var safeHref = String(href || '').trim();
        if (!safeHref) {
            return '';
        }
        return '<a class="footer-social-link" href="' + escHtml(safeHref) + '" target="_blank" rel="noopener noreferrer" aria-label="' + escHtml(label) + '" title="' + escHtml(label) + '"><i class="' + iconClass + '"></i></a>';
    }

    function normalizeBrandKey(value) {
        var normalized = String(value || '').trim().toLowerCase().replace(/\s+/g, ' ');
        if (normalized === 'dryrod ii' || normalized === 'dryrod. ii' || normalized === 'phoenix dryrod' || normalized === 'phoenix dry rod') return 'dryrod. ii';
        if (normalized === 'bw technologies' || normalized === 'bw') return 'bw';
        if (normalized === 'rae' || normalized === 'rac' || normalized === 'rae systems') return 'rae systems';
        if (normalized === 'robot systems' || normalized === 'robot system peripherals' || normalized === 'robot systems peripherals') return 'robot systems peripherals';
        if (normalized === 'hard worker' || normalized === 'hard workers' || normalized === 'hardworker') return 'hardworker';
        if (normalized === 'weller' || normalized === 'weiler') return 'weiler';
        if (normalized === 'panasonic') return 'panasonic connect';
        return normalized;
    }

    function extractBrandNameFromLink(link) {
        if (!link) return '';

        try {
            var linkUrl = new URL(link.getAttribute('href') || '', window.location.href);
            var fromQuery = linkUrl.searchParams.get('name');
            if (fromQuery) {
                return decodeURIComponent(fromQuery);
            }
        } catch (err) {}

        var img = link.querySelector('img');
        if (img) {
            var title = String(img.getAttribute('title') || '').trim();
            if (title) return title;
            var alt = String(img.getAttribute('alt') || '').trim();
            if (alt) return alt;
        }

        return String(link.textContent || '').trim();
    }

    function updateBrandsDropdownLogos() {
        if (!brandLogoMap || Object.keys(brandLogoMap).length === 0) {
            return;
        }

        var brandLinks = document.querySelectorAll('.nav-list > li:nth-child(3) .nav-dropdown ul li > a');
        if (!brandLinks.length) {
            return;
        }

        brandLinks.forEach(function(link) {
            var brandName = extractBrandNameFromLink(link);
            var key = normalizeBrandKey(brandName);
            if (!key || !brandLogoMap[key]) {
                return;
            }

            var logoUrl = String(brandLogoMap[key] || '').trim();
            if (!logoUrl) {
                return;
            }

            var img = link.querySelector('img');
            if (!img) {
                img = document.createElement('img');
                link.textContent = '';
                link.appendChild(img);
            }

            img.setAttribute('src', logoUrl);
            if (!img.getAttribute('alt') && brandName) {
                img.setAttribute('alt', brandName);
            }
            if (!img.getAttribute('title') && brandName) {
                img.setAttribute('title', brandName);
            }
        });
    }

    function updateTrustedBrandsCarouselLogos() {
        if (!brandLogoMap || Object.keys(brandLogoMap).length === 0) {
            return;
        }

        var carouselLinks = document.querySelectorAll('.brands-carousel-track .brands-carousel-item');
        if (!carouselLinks.length) {
            return;
        }

        carouselLinks.forEach(function(link) {
            var brandName = extractBrandNameFromLink(link);
            var key = normalizeBrandKey(brandName);
            if (!key || !brandLogoMap[key]) {
                return;
            }

            var logoUrl = String(brandLogoMap[key] || '').trim();
            if (!logoUrl) {
                return;
            }

            var img = link.querySelector('img');
            if (!img) {
                img = document.createElement('img');
                link.textContent = '';
                link.appendChild(img);
            }

            img.setAttribute('src', logoUrl);
            if (!img.getAttribute('alt') && brandName) {
                img.setAttribute('alt', brandName);
            }
            if (!img.getAttribute('title') && brandName) {
                img.setAttribute('title', brandName);
            }
        });
    }

    function fitBrandLogoImage(img) {
        if (!img || !img.naturalWidth || !img.naturalHeight) {
            return;
        }

        var brandLabel = String(img.getAttribute('alt') || img.getAttribute('title') || '').trim();
        var brandKey = normalizeBrandKey(brandLabel);

        var sourceW = img.naturalWidth;
        var sourceH = img.naturalHeight;
        var sampleW = Math.max(1, Math.min(320, sourceW));
        var sampleH = Math.max(1, Math.round(sourceH * (sampleW / sourceW)));

        var canvas = document.createElement('canvas');
        canvas.width = sampleW;
        canvas.height = sampleH;
        var ctx = canvas.getContext('2d', { willReadFrequently: true });
        if (!ctx) {
            return;
        }

        try {
            ctx.clearRect(0, 0, sampleW, sampleH);
            ctx.drawImage(img, 0, 0, sampleW, sampleH);
            var pixels = ctx.getImageData(0, 0, sampleW, sampleH).data;

            var minX = sampleW;
            var minY = sampleH;
            var maxX = -1;
            var maxY = -1;

            for (var y = 0; y < sampleH; y++) {
                for (var x = 0; x < sampleW; x++) {
                    var alpha = pixels[(y * sampleW + x) * 4 + 3];
                    if (alpha > 10) {
                        if (x < minX) minX = x;
                        if (y < minY) minY = y;
                        if (x > maxX) maxX = x;
                        if (y > maxY) maxY = y;
                    }
                }
            }

            if (maxX < minX || maxY < minY) {
                return;
            }

            var boxW = Math.max(1, maxX - minX + 1);
            var boxH = Math.max(1, maxY - minY + 1);
            var fillX = boxW / sampleW;
            var fillY = boxH / sampleH;

            var targetFillX = 0.90;
            var targetFillY = 0.88;
            var scaleX = targetFillX / Math.max(0.01, fillX);
            var scaleY = targetFillY / Math.max(0.01, fillY);
            var autoScale = Math.min(scaleX, scaleY);
            autoScale = Math.max(1.08, Math.min(1.75, autoScale));

            if (brandKey === 'bw') {
                autoScale *= 0.92;
            } else if (brandKey === 'dryrod. ii') {
                autoScale *= 0.94;
            }

            var centerX = minX + boxW / 2;
            var centerY = minY + boxH / 2;
            var xBias = ((sampleW / 2) - centerX) / sampleW;
            var yBias = ((sampleH / 2) - centerY) / sampleH;

            var shiftX = Math.max(-10, Math.min(10, xBias * 208));
            var shiftY = Math.max(-6, Math.min(6, yBias * 86));

            img.style.setProperty('--brand-auto-scale', autoScale.toFixed(3));
            img.style.setProperty('--brand-shift-x', shiftX.toFixed(2) + 'px');
            img.style.setProperty('--brand-shift-y', shiftY.toFixed(2) + 'px');
        } catch (err) {
            // Ignore per-image failures to keep the dropdown usable.
        }
    }

    function autoSizeDropdownBrandLogos() {
        var dropdownImages = document.querySelectorAll('.nav-list > li:nth-child(3) .nav-dropdown ul li > a img');
        if (!dropdownImages.length) {
            return;
        }

        dropdownImages.forEach(function(img) {
            if (img.complete && img.naturalWidth > 0) {
                fitBrandLogoImage(img);
                return;
            }

            img.addEventListener('load', function onLoad() {
                img.removeEventListener('load', onLoad);
                fitBrandLogoImage(img);
            });
        });
    }

    function modernizeLegacyFooter() {
        var footer = document.querySelector('footer');
        if (!footer) return;
        if (footer.getAttribute('data-andison-footer-rendered') === '1') return;

        var footerContent = footer.querySelector('.footer-content');
        if (!footerContent) {
            return;
        }

        var legacyLinks = footer.querySelector('.footer-links');
        var legacyCopyright = footer.querySelector('.footer-copyright');
        var hasStructuredGrid = !!footer.querySelector('.footer-main-grid');

        // Transform both legacy footer blocks and older structured footer variants.
        if (!hasStructuredGrid && (!legacyLinks || !legacyCopyright)) {
            return;
        }

        footer.classList.add('footer-modernized');

        var copyrightText = String(footerSettings.copyright || '').trim();
        if (!copyrightText) {
            copyrightText = legacyCopyright ? String(legacyCopyright.textContent || '').trim() : '';
        }
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

        var footerNavLinksHtml = [
            footerNavLink('/ANDISON/home.php', 'Home'),
            footerNavLink('/ANDISON/aboutus.php', 'About Us'),
            footerNavLink('/ANDISON/brands.php', 'Brands'),
            footerNavLink('/ANDISON/industries.php', 'Industries'),
            footerNavLink('/ANDISON/services.php', 'Services'),
            footerNavLink('/ANDISON/contact.php', 'Contact Us')
        ].join('');

        var footerSocialLinksHtml = [
            footerSocialLink(footerSettings.facebook_url || '', 'bi bi-facebook', 'Facebook'),
            footerSocialLink(footerSettings.linkedin_url || '', 'bi bi-linkedin', 'LinkedIn'),
            footerSocialLink(footerSettings.messenger_url || '', 'bi bi-messenger', 'Messenger'),
            footerSocialLink(footerSettings.viber_url || '', 'bi bi-phone-vibrate', 'Viber')
        ].join('');

        var qrCodeHtml = (footerSettings.qr_code_url || '') 
            ? '<div style="margin-top:20px;text-align:left;padding:16px 8px;background:rgba(255,255,255,0.15);border-radius:8px;border:1px solid rgba(255,255,255,0.25);">'
                + '<div style="font-size:12px;color:#ffffff;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;text-align:center;">QR Code</div>'
                + '<div style="font-size:13px;font-weight:700;color:#ffffff;margin-bottom:4px;line-height:1.3;">Browse our product catalog</div>'
                + '<div style="font-size:11px;color:#ffffff;margin-bottom:12px;font-weight:600;">SCAN HERE</div>'
                + '<div style="position:relative;width:120px;height:120px;margin:0;background:rgba(255,255,255,0.1);border-radius:8px;border:1px solid rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;overflow:hidden;">'
                    + '<img src="' + escHtml(footerSettings.qr_code_url) + '" alt="QR Code" style="width:118px;height:118px;display:block;border-radius:7px;" onerror="this.style.display=\'none\';this.parentElement.innerHTML=\'<div style=&quot;font-size:11px;color:rgba(255,255,255,0.5);padding:4px;&quot;>QR Code</div>\'">'
                + '</div>'
            + '</div>'
            : '';

        footerContent.innerHTML = (
            ''
            + '<div class="footer-main-grid">'
                + '<div class="footer-brand-col">'
                    + '<a href="/ANDISON/home.php" class="footer-brand-logo" aria-label="Andison Industrial Home">'
                        + '<img src="/ANDISON/assets/HOME/image-removebg-preview.png" alt="Andison Industrial">'
                    + '</a>'
                    + '<p class="footer-brand-blurb">' + escHtmlWithBreaks(footerSettings.brand_blurb || '') + '</p>'
                + '</div>'

                + '<div class="footer-col">'
                    + '<h4 class="footer-col-title">' + escHtml(footerSettings.manila_title || 'Manila') + '</h4>'
                    + '<ul class="footer-contact-list">'
                        + footerContactItem('bi bi-geo-alt-fill', footerSettings.manila_address || '')
                        + footerContactItem('bi bi-telephone-fill', (footerSettings.manila_phone_1 || '') ? (String(footerSettings.manila_phone_1 || '').trim()) : '')
                        + footerContactItem('bi bi-telephone-fill', footerSettings.manila_phone_2 || '')
                        + footerContactEmailItem('bi bi-envelope-fill', footerSettings.contact_email || '')
                    + '</ul>'
                + '</div>'

                + '<div class="footer-col">'
                    + '<h4 class="footer-col-title">' + escHtml(footerSettings.calabarzon_title || 'Calabarzon') + '</h4>'
                    + '<ul class="footer-contact-list">'
                        + footerContactItem('bi bi-geo-alt-fill', footerSettings.calabarzon_address || '')
                        + footerContactItem('bi bi-telephone-fill', (footerSettings.calabarzon_phone || '') ? (String(footerSettings.calabarzon_phone || '').trim()) : '')
                        + footerContactEmailItem('bi bi-envelope-fill', footerSettings.contact_email || '')
                    + '</ul>'
                + '</div>'

                + '<div class="footer-col">'
                    + '<h4 class="footer-col-title">' + escHtml(footerSettings.navigation_title || 'Navigation') + '</h4>'
                    + '<nav class="footer-nav-links" aria-label="Footer navigation">'
                        + footerNavLinksHtml
                    + '</nav>'
                    + '</div>'

                + ((footerSocialLinksHtml || footerSettings.social_image_url)
                    ? '<div class="footer-col footer-social-col">'
                        + (footerSocialLinksHtml
                            ? '<div class="footer-socials"><h5 class="footer-socials-title" style="text-align: center;">Socials</h5><div class="footer-social-links">' + footerSocialLinksHtml + '</div></div>'
                            : '')
                        + (footerSettings.social_image_url
                            ? '<div class="footer-social-image" style="margin-top: 25px;text-align: left;"><div style="font-size:13px;font-weight:700;color:#ffffff;margin-bottom:4px;line-height:1.3;white-space:nowrap;">Browse our product catalog</div><div style="font-size:13px;color:#ffffff;margin-bottom:10px;font-weight:700;">SCAN HERE</div><img src="' + escHtml(footerSettings.social_image_url) + '" alt="QR Code" style="max-width: 100%; height: auto; max-height: 140px; object-fit: contain; filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1)); border-radius: 6px;"></div>'
                            : '')
                    + '</div>'
                    : '')

                + (footerSettings.qr_code_url
                    ? '<div class="footer-col footer-qr-col">'
                        + qrCodeHtml
                    + '</div>'
                    : '')
            + '</div>'
            + '<div class="footer-bottom">'
                + '<p class="footer-copyright">' + copyrightText + '</p>'
            + '</div>'
        ).replace(/\/ANDISON(?=\/)/g, footerBase);

        var footerLogoImg = footerContent.querySelector('.footer-brand-logo img');
        if (footerLogoImg) {
            var logoCandidates = [
                (footerBase ? footerBase : '') + '/assets/HOME/image-removebg-preview.png',
                '/ANDISON/assets/HOME/image-removebg-preview.png',
                '/assets/HOME/image-removebg-preview.png',
                'assets/HOME/image-removebg-preview.png',
                '../assets/HOME/image-removebg-preview.png'
            ];
            var logoIndex = 0;

            footerLogoImg.addEventListener('error', function onLogoError() {
                logoIndex += 1;
                while (logoIndex < logoCandidates.length && logoCandidates[logoIndex] === footerLogoImg.getAttribute('src')) {
                    logoIndex += 1;
                }

                if (logoIndex >= logoCandidates.length) {
                    footerLogoImg.removeEventListener('error', onLogoError);
                    return;
                }

                footerLogoImg.setAttribute('src', logoCandidates[logoIndex]);
            });
        }

        footer.setAttribute('data-andison-footer-rendered', '1');

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

    function runSafely(fn) {
        try {
            fn();
        } catch (err) {
            // Keep footer/logo hydration resilient even if one updater fails.
        }
    }

    function hydrateFooterAndBrandUIs() {
        // Footer modernization must run first so layout/socials are always normalized.
        runSafely(modernizeLegacyFooter);
        runSafely(updateBrandsDropdownLogos);
        runSafely(autoSizeDropdownBrandLogos);
        runSafely(updateTrustedBrandsCarouselLogos);
    }

    function scheduleHydrationRetries() {
        // Retry quickly for pages that finish rendering late.
        setTimeout(hydrateFooterAndBrandUIs, 80);
        setTimeout(hydrateFooterAndBrandUIs, 220);

        var retries = 0;
        var maxRetries = 10;
        var retryTimer = setInterval(function() {
            retries += 1;
            hydrateFooterAndBrandUIs();

            var footer = document.querySelector('footer');
            if (!footer || footer.getAttribute('data-andison-footer-rendered') === '1' || retries >= maxRetries) {
                clearInterval(retryTimer);
            }
        }, 350);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function(){
            hydrateFooterAndBrandUIs();
            scheduleHydrationRetries();
        });
        window.addEventListener('load', hydrateFooterAndBrandUIs);
    } else {
        hydrateFooterAndBrandUIs();
        scheduleHydrationRetries();
        window.addEventListener('load', hydrateFooterAndBrandUIs);
    }
})();
</script>

<script>
(function() {
    var loader = document.getElementById('globalPageLoader');
    if (!loader) return;

    // Start with overflow hidden to prevent scrolling while loader is visible on initial load
    document.documentElement.classList.add('gpl-loading');

    // Determine the base path for the logo (same logic as footer)
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
    
    var logoImg = document.getElementById('gplLogoImg');
    if (logoImg) {
        logoImg.src = (footerBase ? footerBase : '') + '/assets/HOME/image-removebg-preview.png';
        // Fallback if image fails to load
        logoImg.addEventListener('error', function() {
            if (!this.src.includes('ANDISON')) {
                this.src = '/ANDISON/assets/HOME/image-removebg-preview.png';
            }
        });
    }

    function hideLoader() {
        if (loader.classList.contains('is-hidden')) return;
        loader.classList.add('is-hidden');
        loader.setAttribute('aria-hidden', 'true');
        document.documentElement.classList.remove('gpl-loading');
    }

    // Hide loader when the page finishes loading
    if (document.readyState === 'complete') {
        setTimeout(hideLoader, 300);
    } else {
        window.addEventListener('load', function() {
            setTimeout(hideLoader, 300);
        });
    }

    // Fallback: forcefully hide after 4.5 seconds in case of stalled assets
    setTimeout(hideLoader, 4500);

    // Show loader on navigation clicks (nav bar, sidebar, and general internal links)
    document.addEventListener('click', function(e) {
        // Find closest anchor tag
        var target = e.target.closest('a');
        if (!target) return;

        var href = target.getAttribute('href');
        if (!href) return;

        // Skip specific links
        if (
            href.startsWith('javascript:') ||
            href.startsWith('#') ||
            target.getAttribute('target') === '_blank' ||
            href.startsWith('tel:') ||
            href.startsWith('mailto:') ||
            e.ctrlKey || e.shiftKey || e.metaKey || e.button !== 0 // open in new tab
        ) {
            return;
        }

        // Only intercept internal links (same origin)
        var isInternal = false;
        try {
            var url = new URL(target.href, window.location.href);
            if (url.origin === window.location.origin) {
                isInternal = true;
            }
        } catch (err) {}

        if (isInternal) {
            // Check if it's just a hash change on the same page
            var currentUrl = new URL(window.location.href);
            var targetUrl = new URL(target.href);
            if (currentUrl.pathname === targetUrl.pathname && currentUrl.search === targetUrl.search && targetUrl.hash) {
                return; // just an anchor jump on same page
            }

            // Show loader
            loader.classList.remove('is-hidden');
            loader.setAttribute('aria-hidden', 'false');
            document.documentElement.classList.add('gpl-loading');
        }
    });

    // Handle browser back/forward buttons (pages can be restored from bfcache)
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            hideLoader();
        }
    });
})();
</script>

