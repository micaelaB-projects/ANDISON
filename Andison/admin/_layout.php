<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';

function andison_admin_header(string $title, string $active = 'dashboard'): void
{
    $flash = andison_get_flash();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo htmlspecialchars($title); ?> - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root{--accent:#2B11DB;--mint:#00D7B3;--bg:#f5f7fb;--card:#fff;--text:#111827;--muted:#6b7280;--border:#e5e7eb;--danger:#ef4444;--success:#10b981}
        /* Dark theme variables */
        .dark-theme { --accent:#1f2340; --mint:#06d6a0; --bg:#0b1220; --card:#0f1724; --text:#e6eef6; --muted:#94a3b8; --border:#1f2937 }
        .dark-theme body{background:linear-gradient(135deg,#071126 0%,#0b1220 100%);color:var(--text)}
        .dark-theme .sidebar{background:linear-gradient(135deg,var(--accent) 0%, #0f1940 100%);color:var(--text)}
        .dark-theme .card{background:var(--card);box-shadow:0 14px 40px rgba(2,6,23,0.6)}
        /* Dark-mode tweaks for sidebar options and layout */
        .dark-theme .options-header{color:rgba(255,255,255,0.85)}
        .dark-theme .options-bottom{border-top-color:rgba(255,255,255,0.08)}
        .dark-theme .options-bottom .nav.option{color:rgba(255,255,255,0.90);background:transparent}
        .dark-theme .options-bottom .nav.option::before{background:rgba(255,255,255,0.06)}
        .dark-theme .options-bottom .nav.option i{color:rgba(255,255,255,0.95)}
        .dark-theme .options-bottom .nav.option span{color:rgba(255,255,255,0.90);font-weight:700}
        .dark-theme .options-bottom .nav.option:hover{color:rgba(255,255,255,0.98)}
        .dark-theme .options-bottom .nav.option:hover::before{opacity:1}
        .dark-theme .nav a{background:rgba(255,255,255,0.02);color:rgba(255,255,255,0.9)}
        .dark-theme .nav a.active{background:rgba(0,215,179,0.14);outline:1px solid rgba(0,215,179,0.20);color:var(--text)}
        .dark-theme .sidebar .bottom{padding-bottom:18px}
        *{box-sizing:border-box}
        body{margin:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:linear-gradient(135deg,#f5f7fb 0%,#eff2ff 100%);color:var(--text)}
        a{color:inherit}
        .shell{min-height:100vh;display:flex}
        .sidebar{width:260px;background:linear-gradient(135deg,var(--accent) 0%, #1a0a8f 100%);color:#fff;padding:18px 14px;position:fixed;top:0;left:0;height:100vh;transition:transform 0.3s ease;z-index:10000}
        .sidebar.collapsed{transform:translateX(-260px)}
        .brand{display:flex;align-items:center;gap:10px;padding:10px 10px 16px}
        .brand .mark{width:48px;height:48px;border-radius:10px;background:rgba(255,255,255,0.16);display:grid;place-items:center;font-weight:900;font-size:20px}
        .brand .name{font-weight:900;letter-spacing:0.4px;line-height:1.1;font-size:16px}
        .brand .sub{font-size:12px;opacity:0.85}
        .nav{display:flex;flex-direction:column;gap:8px;margin-top:10px}
        .nav a{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:12px;text-decoration:none;font-weight:500;letter-spacing:0.2px;background:rgba(255,255,255,0.08);transition:all 0.2s ease}
        .nav a.active{background:rgba(0,215,179,0.22);outline:1px solid rgba(0,215,179,0.30)}
        .nav a:hover{background:rgba(255,255,255,0.18);transform:translateX(4px)}
        .nav.option{background:transparent;border-radius:10px;padding:8px 12px;margin:6px 8px;color:rgba(255,255,255,0.95);display:flex;align-items:center;gap:8px;text-decoration:none}
        .nav.option i{font-size:16px}
        .nav.option:hover{background:rgba(255,255,255,0.06);transform:translateX(4px)}
        .sidebar .bottom{position:absolute;left:14px;right:14px;bottom:14px;display:flex;flex-direction:column;align-items:center;gap:18px;padding-bottom:6px}
        .options-bottom{width:100%;display:flex;flex-direction:column;align-items:center;border-top:1px solid rgba(255,255,255,0.12);padding-top:16px}
        .options-header{font-size:11px;font-weight:900;color:rgba(255,255,255,0.85);letter-spacing:1.2px;text-transform:uppercase;padding:0 8px;margin-bottom:16px;text-align:center;letter-spacing:0.9px}
        .options-bottom .nav.option{display:flex;flex-direction:column;align-items:center;justify-content:center;padding:12px 16px;margin:6px 0;color:rgba(255,255,255,0.90);gap:10px;text-decoration:none;border-radius:12px;transition:all 0.2s ease;position:relative}
        .options-bottom .nav.option::before{content:'';position:absolute;inset:0;background:rgba(255,255,255,0.04);border-radius:12px;opacity:0;transition:opacity 0.2s ease;z-index:-1}
        .options-bottom .nav.option:hover{color:rgba(255,255,255,0.98);transform:translateY(-2px)}
        .options-bottom .nav.option:hover::before{opacity:1}
        .options-bottom .nav.option i{font-size:28px;line-height:1;display:flex;align-items:center;justify-content:center}
        .options-bottom .nav.option span{display:block;font-size:12px;font-weight:700;letter-spacing:0.3px}
        .pill{display:none}
        .close-btn{position:absolute;top:14px;right:14px;width:32px;height:32px;background:rgba(255,255,255,0.15);border:none;border-radius:8px;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:18px;transition:all 0.2s ease}
        .close-btn:hover{background:rgba(255,255,255,0.25);transform:scale(1.1)}
        .hamburger{display:flex;position:fixed;top:16px;left:16px;z-index:100001;width:36px;height:36px;background:var(--accent);border:none;border-radius:10px;cursor:pointer;flex-direction:column;align-items:center;justify-content:center;gap:4px;box-shadow:0 4px 12px rgba(0,0,0,0.15);transition:opacity 0.3s ease, visibility 0.3s ease}
        .hamburger.menu-open{opacity:0;visibility:hidden}
        .hamburger span{width:18px;height:2.5px;background:#fff;border-radius:2px;transition:all 0.3s ease}
        .hamburger.active span:nth-child(1){transform:translateY(6.5px) rotate(45deg)}
        .hamburger.active span:nth-child(2){opacity:0}
        .hamburger.active span:nth-child(3){transform:translateY(-6.5px) rotate(-45deg)}
        .main{flex:1;padding:28px 28px 50px;margin-left:0;padding-left:74px;transition:all 0.3s ease}
        body.menu-open .main{margin-left:260px;padding-left:28px}
        .topbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:24px;padding-bottom:16px;border-bottom:2px solid rgba(43,17,219,0.08)}
        .topbar h1{margin:0;font-size:28px;letter-spacing:-0.5px;font-weight:800;background:linear-gradient(135deg,var(--accent) 0%,#1a0a8f 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .hint{color:var(--muted);font-size:13px;max-width:820px}
        .grid{display:grid;grid-template-columns:repeat(12,1fr);gap:18px}
        .card{grid-column:span 12;background:var(--card);border:1px solid rgba(43,17,219,0.08);border-radius:20px;box-shadow:0 12px 48px rgba(43,17,219,0.09);padding:24px;transition:all 0.3s ease}
        .card:hover{box-shadow:0 16px 56px rgba(43,17,219,0.12);transform:translateY(-2px)}
        .card h2{margin:0 0 18px;font-size:20px;font-weight:800;color:var(--accent);display:flex;align-items:center;gap:10px}
        .card h2 i{font-size:24px}
        .row{display:flex;gap:12px;flex-wrap:wrap}
        .field{display:flex;flex-direction:column;gap:6px;min-width:240px;flex:1}
        label{font-size:12px;font-weight:900;letter-spacing:0.6px;text-transform:uppercase;color:#374151}
        input[type="text"], input[type="url"], input[type="password"], select, textarea{width:100%;padding:14px 16px;border-radius:14px;border:2px solid var(--border);background:#fff;font-size:14px;transition:all 0.2s ease}
        textarea{min-height:100px;resize:vertical;line-height:1.6}
        input:focus, select:focus, textarea:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 4px rgba(43,17,219,0.12);transform:translateY(-1px)}
        input:hover:not(:focus), select:hover:not(:focus), textarea:hover:not(:focus){border-color:rgba(43,17,219,0.3)}
        .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:12px 20px;border-radius:14px;border:2px solid;font-weight:900;cursor:pointer;text-decoration:none;font-size:14px;transition:all 0.2s ease}
        .btn-primary{background:var(--mint);border-color:var(--mint);color:#0b1b16}
        .btn-outline{background:#fff;border-color:rgba(43,17,219,0.20);color:var(--accent)}
        .btn-danger{background:#fff;border-color:rgba(239,68,68,0.35);color:var(--danger)}
        .btn:hover{transform:translateY(-2px);box-shadow:0 12px 24px rgba(0,0,0,0.12)}
        .btn:active{transform:translateY(0)}
        .btn-primary:hover{box-shadow:0 12px 24px rgba(0,215,179,0.3)}
        .table{width:100%;border-collapse:separate;border-spacing:0 12px}
        .table th{font-size:12px;text-transform:uppercase;letter-spacing:0.5px;color:var(--muted);text-align:left;padding:0 12px;font-weight:800}
        .table td{background:#fff;border:1px solid rgba(43,17,219,0.08);padding:14px 12px;transition:all 0.2s ease}
        .table tr:hover td{background:#f9fafb}
        .table tr td:first-child{border-top-left-radius:14px;border-bottom-left-radius:14px}
        .table tr td:last-child{border-top-right-radius:14px;border-bottom-right-radius:14px}
        .badge{display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:999px;font-size:12px;font-weight:900;background:rgba(43,17,219,0.10);color:var(--accent)}
        .flash{margin:0 0 18px;padding:14px 18px;border-radius:16px;border:1px solid;font-size:14px}
        .flash.success{background:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.25);color:#065f46}
        .flash.error{background:rgba(239,68,68,0.06);border-color:rgba(239,68,68,0.25);color:#7f1d1d}

        .toast{
            position:fixed;
            right:18px;
            bottom:18px;
            z-index:9999;
            min-width:280px;
            max-width:520px;
            padding:12px 14px;
            border-radius:14px;
            border:1px solid rgba(17,24,39,0.12);
            background:rgba(255,255,255,0.96);
            box-shadow:0 18px 50px rgba(0,0,0,0.18);
            display:flex;
            align-items:flex-start;
            gap:10px;
            opacity:0;
            transform:translateY(10px);
            pointer-events:none;
            transition:opacity 220ms ease, transform 220ms ease;
        }
        .toast.show{opacity:1;transform:translateY(0)}
        .toast .icon{font-size:18px;line-height:1.2;margin-top:1px}
        .toast .title{font-weight:900;margin:0 0 2px}
        .toast .msg{margin:0;color:#374151;font-size:13px;line-height:1.4}
        .toast.success{border-color:rgba(16,185,129,0.25)}
        .toast.success .icon{color:var(--success)}
        .toast.error{border-color:rgba(239,68,68,0.25)}
        .toast.error .icon{color:var(--danger)}
        .two-col{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        @media (max-width: 980px){
            .main{margin-left:0!important;padding:64px 16px 16px!important}
            .two-col{grid-template-columns:1fr}
            body.menu-open .main{margin-left:0!important}
        }

        /* Confirmation Modal */
        .confirm-modal{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.6);z-index:99999;align-items:center;justify-content:center}
        .confirm-modal.show{display:flex}
        .confirm-box{background:#fff;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.3);max-width:480px;width:90%;padding:28px}
        .confirm-icon{font-size:48px;text-align:center;margin-bottom:16px;color:var(--accent)}
        .confirm-title{font-size:22px;font-weight:bold;margin:0 0 12px;text-align:center;color:var(--text)}
        .confirm-message{font-size:16px;margin:0 0 24px;text-align:center;color:var(--muted);line-height:1.5}
        .confirm-buttons{display:flex;gap:12px;justify-content:center}
        .confirm-buttons button{padding:12px 24px;border-radius:10px;border:2px solid;font-weight:bold;cursor:pointer;font-size:15px}
        .confirm-btn-yes{background:var(--mint);border-color:var(--mint);color:#0b1b16}
        .confirm-btn-no{background:#fff;border-color:rgba(43,17,219,0.3);color:var(--accent)}
        .confirm-btn-yes:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,215,179,0.3)}
        .confirm-btn-no:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(43,17,219,0.2)}
    </style>
</head>
<body>
<button class="hamburger" id="hamburger" aria-label="Toggle menu">
    <span></span>
    <span></span>
    <span></span>
</button>
<div class="shell">
    <aside class="sidebar" id="sidebar">
        <button class="close-btn" id="closeBtn" aria-label="Close menu">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="brand">
            <div class="mark">A</div>
            <div>
                <div class="name">ANDISON</div>
                <div class="sub">Admin Dashboard</div>
            </div>
        </div>
        <nav class="nav">
            <a href="index.php" class="<?php echo $active === 'dashboard' ? 'active' : ''; ?>"><i class="bi bi-grid"></i> Dashboard</a>
            <a href="analytics.php" class="<?php echo $active === 'analytics' ? 'active' : ''; ?>"><i class="bi bi-bar-chart-line"></i> Analytics</a>
            <a href="products.php" class="<?php echo $active === 'products' ? 'active' : ''; ?>"><i class="bi bi-box-seam"></i> Products</a>
            <a href="categories.php" class="<?php echo $active === 'categories' ? 'active' : ''; ?>"><i class="bi bi-tag"></i> Categories</a>
            <a href="featured.php" class="<?php echo $active === 'featured' ? 'active' : ''; ?>"><i class="bi bi-star"></i> Homepage Featured</a>
            <a href="slider.php" class="<?php echo $active === 'slider' ? 'active' : ''; ?>"><i class="bi bi-images"></i> Homepage Slider</a>
            <a href="youtube.php" class="<?php echo $active === 'youtube' ? 'active' : ''; ?>"><i class="bi bi-youtube"></i> YouTube Links</a>
        </nav>

        <div class="bottom">
            <div class="options-bottom" style="width:100%;">
                <div class="options-header">Options</div>
                <a class="nav option <?php echo $active === 'profile' ? 'active' : ''; ?>" href="profile.php"><i class="bi bi-person"></i><span>Account</span></a>
            </div>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <h1><?php echo htmlspecialchars($title); ?></h1>
        </div>

        <?php if ($flash): ?>
            <div class="flash <?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars((string)$flash['message']); ?>
            </div>

            <div class="toast <?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>" id="toast" role="status" aria-live="polite"
                 data-type="<?php echo htmlspecialchars((string)$flash['type']); ?>"
                 data-message="<?php echo htmlspecialchars((string)$flash['message'], ENT_QUOTES); ?>">
                <div class="icon">
                    <?php echo $flash['type'] === 'success' ? '<i class="bi bi-check-circle"></i>' : '<i class="bi bi-exclamation-triangle"></i>'; ?>
                </div>
                <div>
                    <div class="title"><?php echo $flash['type'] === 'success' ? 'Saved' : 'Action Needed'; ?></div>
                    <p class="msg"></p>
                </div>
            </div>
            <script>
                (function(){
                    var t = document.getElementById('toast');
                    if(!t) return;
                    var msg = t.getAttribute('data-message') || '';
                    var p = t.querySelector('.msg');
                    if(p) p.textContent = msg;

                    // Show toast
                    requestAnimationFrame(function(){ t.classList.add('show'); });

                    // Auto-hide after a moment
                    setTimeout(function(){ t.classList.remove('show'); }, 2600);
                    setTimeout(function(){ if(t && t.parentNode){ t.parentNode.removeChild(t); } }, 3100);
                })();
            </script>
        <?php endif; ?>
<?php
}

function andison_admin_footer(): void
{
    ?>
    </main>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="confirm-modal">
    <div class="confirm-box">
        <div class="confirm-icon">
            <i class="bi bi-question-circle"></i>
        </div>
        <h3 class="confirm-title">Confirm Action</h3>
        <p class="confirm-message" id="confirmMessage">Are you sure you want to proceed?</p>
        <div class="confirm-buttons">
            <button type="button" class="confirm-btn-yes" id="confirmYes">Yes, Proceed</button>
            <button type="button" class="confirm-btn-no" id="confirmNo">Cancel</button>
        </div>
    </div>
</div>

<script>
(function(){
    var modal = document.getElementById('confirmModal');
    var messageEl = document.getElementById('confirmMessage');
    var yesBtn = document.getElementById('confirmYes');
    var noBtn = document.getElementById('confirmNo');
    var resolveCallback = null;

    window.customConfirm = function(message) {
        return new Promise(function(resolve){
            resolveCallback = resolve;
            messageEl.textContent = message;
            modal.classList.add('show');
        });
    };

    yesBtn.addEventListener('click', function(){
        modal.classList.remove('show');
        if (resolveCallback) resolveCallback(true);
    });

    noBtn.addEventListener('click', function(){
        modal.classList.remove('show');
        if (resolveCallback) resolveCallback(false);
    });

    modal.addEventListener('click', function(e){
        if (e.target === modal) {
            modal.classList.remove('show');
            if (resolveCallback) resolveCallback(false);
        }
    });
    
    // Hamburger menu toggle
    var hamburger = document.getElementById('hamburger');
    var sidebar = document.getElementById('sidebar');
    var closeBtn = document.getElementById('closeBtn');
    
    function closeSidebar() {
        sidebar.classList.add('collapsed');
        hamburger.classList.remove('active');
        hamburger.classList.remove('menu-open');
        document.body.classList.remove('menu-open');
        sessionStorage.setItem('sidebarOpen', 'false');
    }
    
    function openSidebar() {
        sidebar.classList.remove('collapsed');
        hamburger.classList.add('active');
        hamburger.classList.add('menu-open');
        document.body.classList.add('menu-open');
        sessionStorage.setItem('sidebarOpen', 'true');
    }
    
    if (hamburger && sidebar) {
        // Check saved state
        var savedState = sessionStorage.getItem('sidebarOpen');
        if (savedState === 'true') {
            openSidebar();
        } else {
            sidebar.classList.add('collapsed');
        }
        
        // Toggle menu
        hamburger.addEventListener('click', function(e){
            e.stopPropagation();
            var isCollapsed = sidebar.classList.contains('collapsed');
            
            if (isCollapsed) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });
        
        // Close button
        if (closeBtn) {
            closeBtn.addEventListener('click', function(e){
                e.stopPropagation();
                closeSidebar();
            });
        }
        
        // Don't close when clicking inside sidebar
        sidebar.addEventListener('click', function(e){
            e.stopPropagation();
        });
    }
    
    // Theme toggle (Options -> Toggle Theme)
    var themeToggle = document.getElementById('toggleTheme');
    (function(){
        var saved = localStorage.getItem('admin_theme');
        if (saved === 'dark') {
            document.documentElement.classList.add('dark-theme');
        }
    })();

    if (themeToggle) {
        themeToggle.addEventListener('click', function(e){
            e.preventDefault();
            var isDark = document.documentElement.classList.toggle('dark-theme');
            localStorage.setItem('admin_theme', isDark ? 'dark' : 'light');
        });
    }

    // Logout button removed from sidebar; no handler required.
})();
</script>

</body>
</html>
<?php
}



