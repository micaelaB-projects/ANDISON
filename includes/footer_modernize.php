<style>
/* Shared modern footer for legacy pages */
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
    grid-template-columns: minmax(240px, 1.25fr) minmax(220px, 1fr) minmax(220px, 1fr) minmax(200px, 1fr) minmax(150px, 0.8fr);
    gap: 56px;
    align-items: start;
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

@media (max-width: 1180px) {
    footer.footer-modernized .footer-main-grid {
        grid-template-columns: repeat(3, minmax(180px, 1fr));
        gap: 24px 28px;
    }

    footer.footer-modernized .footer-col-title { font-size: 15px; }
    footer.footer-modernized .footer-nav-links a { font-size: 15px; }
    footer.footer-modernized .footer-copyright { font-size: 15px; }
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
        grid-template-columns: repeat(2, minmax(0, 1fr));
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
        width: 148px;
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
}
</style>

<?php
require_once __DIR__ . '/../Andison/includes/footer_settings.php';
$andisonFooterSettings = andison_get_footer_settings();
?>

<script>
(function(){
    var footerSettings = <?php echo json_encode($andisonFooterSettings, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;

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
        return '<li class="footer-contact-item"><i class="' + iconClass + '"></i><span>Email: <a href="mailto:' + escHtml(safeEmail) + '" style="color:inherit;text-decoration:none;">' + escHtml(safeEmail) + '</a></span></li>';
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

    function modernizeLegacyFooter() {
        var footer = document.querySelector('footer');
        if (!footer) return;
        if (footer.classList.contains('footer-modernized')) return;

        var footerContent = footer.querySelector('.footer-content');
        var legacyLinks = footer.querySelector('.footer-links');
        var legacyCopyright = footer.querySelector('.footer-copyright');

        if (!footerContent || !legacyLinks || !legacyCopyright || footer.querySelector('.footer-main-grid')) {
            return;
        }

        footer.classList.add('footer-modernized');

        var copyrightText = String(footerSettings.copyright || '').trim();
        if (!copyrightText) {
            copyrightText = (legacyCopyright.textContent || '').trim();
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
            footerSocialLink(footerSettings.linkedin_url || '', 'bi bi-linkedin', 'LinkedIn')
        ].join('');

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
                        + footerContactItem('bi bi-telephone-fill', (footerSettings.manila_phone_1 || '') ? ('Phone: ' + String(footerSettings.manila_phone_1 || '').trim()) : '')
                        + footerContactItem('bi bi-telephone-fill', footerSettings.manila_phone_2 || '')
                        + footerContactEmailItem('bi bi-envelope-fill', footerSettings.contact_email || '')
                    + '</ul>'
                + '</div>'

                + '<div class="footer-col">'
                    + '<h4 class="footer-col-title">' + escHtml(footerSettings.calabarzon_title || 'Calabarzon') + '</h4>'
                    + '<ul class="footer-contact-list">'
                        + footerContactItem('bi bi-geo-alt-fill', footerSettings.calabarzon_address || '')
                        + footerContactItem('bi bi-telephone-fill', (footerSettings.calabarzon_phone || '') ? ('Phone: ' + String(footerSettings.calabarzon_phone || '').trim()) : '')
                        + footerContactEmailItem('bi bi-envelope-fill', footerSettings.contact_email || '')
                    + '</ul>'
                + '</div>'

                + '<div class="footer-col">'
                    + '<h4 class="footer-col-title">' + escHtml(footerSettings.navigation_title || 'Navigation') + '</h4>'
                    + '<nav class="footer-nav-links" aria-label="Footer navigation">'
                        + footerNavLinksHtml
                    + '</nav>'
                    + '</div>'

                + (footerSocialLinksHtml
                    ? '<div class="footer-col"><div class="footer-socials"><h5 class="footer-socials-title">Socials</h5><div class="footer-social-links">' + footerSocialLinksHtml + '</div></div></div>'
                    : '')
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
