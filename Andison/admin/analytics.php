<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
andison_require_admin();
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/analytics.php';
 
$analytics   = andison_get_analytics();
$chartData   = andison_get_daily_chart(30, $analytics);

$brandViews  = $analytics['brands'] ?? [];
arsort($brandViews);

$catViews    = $analytics['categories'] ?? [];
arsort($catViews);

$pageViews   = $analytics['pages'] ?? [];
arsort($pageViews);

$totalBrandViews = array_sum($brandViews);
$totalCatViews   = array_sum($catViews);

andison_admin_header('Analytics', 'analytics');
?>

<div class="grid">

    <!-- ── Stat summary ────────────────────────────────────────── -->
    <section class="card" style="grid-column:span 12;">
        <h2 style="font-size:20px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
            <i class="bi bi-bar-chart-line"></i> Site Overview
            <span style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:600;color:#10b981;background:rgba(16,185,129,0.10);padding:3px 10px;border-radius:999px;">
                <span style="width:7px;height:7px;border-radius:50%;background:#10b981;display:inline-block;animation:livePulse 1.4s ease-in-out infinite;"></span>
                LIVE
            </span>
            <span id="an-timestamp" style="font-size:11px;font-weight:400;color:#9ca3af;margin-left:auto;"></span>
        </h2>
        <style>
            @keyframes livePulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:0.4;transform:scale(0.7)}}
            .an-num{transition:color 0.4s ease}
            .an-num.flash{color:#00D7B3!important}
        </style>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;">
            <div style="padding:18px;background:linear-gradient(135deg,#2B11DB,#1a0a8f);border-radius:12px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div id="an-total-pv" class="an-num" style="font-size:30px;font-weight:800;"><?php echo number_format($analytics['total_pageviews']); ?></div>
                    <i class="bi bi-eye" style="font-size:26px;opacity:0.55;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;margin-top:6px;">Total Page Views</div>
                <div style="font-size:11px;opacity:0.65;margin-top:2px;">All time</div>
            </div>
            <div style="padding:18px;background:linear-gradient(135deg,#f59e0b,#92400e);border-radius:12px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div id="an-today" class="an-num" style="font-size:30px;font-weight:800;"><?php echo number_format($analytics['today_pageviews']); ?></div>
                    <i class="bi bi-calendar-day" style="font-size:26px;opacity:0.55;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;margin-top:6px;">Views Today</div>
                <div id="an-today-u" style="font-size:11px;opacity:0.65;margin-top:2px;"><?php echo number_format($analytics['today_unique']); ?> unique</div>
            </div>
            <div style="padding:18px;background:linear-gradient(135deg,#06b6d4,#0e7490);border-radius:12px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div id="an-week" class="an-num" style="font-size:30px;font-weight:800;"><?php echo number_format($analytics['week_pageviews']); ?></div>
                    <i class="bi bi-calendar-week" style="font-size:26px;opacity:0.55;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;margin-top:6px;">This Week</div>
                <div id="an-week-u" style="font-size:11px;opacity:0.65;margin-top:2px;"><?php echo number_format($analytics['week_unique']); ?> unique</div>
            </div>
            <div style="padding:18px;background:linear-gradient(135deg,#8b5cf6,#4c1d95);border-radius:12px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div id="an-month" class="an-num" style="font-size:30px;font-weight:800;"><?php echo number_format($analytics['month_pageviews']); ?></div>
                    <i class="bi bi-calendar-month" style="font-size:26px;opacity:0.55;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;margin-top:6px;">This Month</div>
                <div id="an-month-u" style="font-size:11px;opacity:0.65;margin-top:2px;"><?php echo number_format($analytics['month_unique']); ?> unique</div>
            </div>
            <div style="padding:18px;background:linear-gradient(135deg,#ec4899,#9d174d);border-radius:12px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div id="an-brands-total" class="an-num" style="font-size:30px;font-weight:800;"><?php echo number_format($totalBrandViews); ?></div>
                    <i class="bi bi-building" style="font-size:26px;opacity:0.55;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;margin-top:6px;">Brand Page Views</div>
                <div style="font-size:11px;opacity:0.65;margin-top:2px;"><?php echo count($brandViews); ?> brand<?php echo count($brandViews) !== 1 ? 's' : ''; ?> visited</div>
            </div>
            <div style="padding:18px;background:linear-gradient(135deg,#10b981,#065f46);border-radius:12px;color:white;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div id="an-cats-total" class="an-num" style="font-size:30px;font-weight:800;"><?php echo number_format($totalCatViews); ?></div>
                    <i class="bi bi-tag" style="font-size:26px;opacity:0.55;"></i>
                </div>
                <div style="font-size:13px;opacity:0.9;margin-top:6px;">Category Views</div>
                <div style="font-size:11px;opacity:0.65;margin-top:2px;"><?php echo count($catViews); ?> categor<?php echo count($catViews) !== 1 ? 'ies' : 'y'; ?> visited</div>
            </div>
        </div>
    </section>

    <!-- ── 30-Day Chart ──────────────────────────────────────────── -->
    <section class="card" style="grid-column:span 12;">
        <h2 style="font-size:18px;margin-bottom:16px;"><i class="bi bi-graph-up-arrow"></i> Page Views — Last 30 Days</h2>
        <?php
            $maxV = max(array_column($chartData, 'views') ?: [1]);
            $maxV = max($maxV, 1);
        ?>
        <div id="an-chart30" style="display:flex;align-items:flex-end;gap:4px;height:120px;">
            <?php foreach ($chartData as $i => $bar): ?>
                <?php $bh = max((int) round(($bar['views'] / $maxV) * 110), 4); ?>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;">
                    <span id="an-c30-val-<?php echo $i; ?>" style="font-size:9px;color:#6b7280;font-weight:700;"><?php echo $bar['views']; ?></span>
                    <div id="an-c30-bar-<?php echo $i; ?>"
                         title="<?php echo htmlspecialchars($bar['date']); ?>: <?php echo $bar['views']; ?> views"
                         style="width:100%;height:<?php echo $bh; ?>px;background:linear-gradient(180deg,var(--accent),rgba(43,17,219,0.45));border-radius:5px 5px 0 0;transition:height 0.5s ease;"
                         onmouseover="this.style.opacity='0.75'" onmouseout="this.style.opacity='1'"></div>
                    <?php if ($i % 5 === 0 || $i === count($chartData)-1): ?>
                        <span style="font-size:9px;color:#9ca3af;white-space:nowrap;"><?php echo htmlspecialchars($bar['date']); ?></span>
                    <?php else: ?>
                        <span style="font-size:9px;color:transparent;">-</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- ── Brand Analytics ───────────────────────────────────────── -->
    <section class="card" style="grid-column:span 12 !important;display:flex;flex-direction:column;">
        <h2 style="font-size:18px;margin-bottom:16px;flex-shrink:0;"><i class="bi bi-building"></i> Brand Views</h2>
        <div id="an-brands-list" style="flex:1;overflow-y:auto;padding-right:6px;max-height:400px;">
            <?php if (empty($brandViews)): ?>
                <div style="color:#9ca3af;font-size:13px;padding:16px 0;">No brand visits recorded yet.</div>
            <?php else: ?>
                <?php
                    $maxBV  = max(array_values($brandViews) ?: [1]);
                    $bRank  = 1;
                    foreach ($brandViews as $bname => $bcount):
                        $bpct = (int) round(($bcount / $maxBV) * 100);
                ?>
                <div style="margin-bottom:14px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-size:11px;font-weight:800;color:#9ca3af;min-width:16px;">#<?php echo $bRank++; ?></span>
                            <span style="font-size:13px;font-weight:600;color:#374151;"><?php echo htmlspecialchars($bname); ?></span>
                        </div>
                        <span style="font-size:12px;font-weight:700;background:rgba(43,17,219,0.10);color:var(--accent);padding:2px 10px;border-radius:999px;"><?php echo number_format($bcount); ?></span>
                    </div>
                    <div style="background:#e5e7eb;border-radius:999px;height:7px;">
                        <div style="width:<?php echo $bpct; ?>%;height:7px;background:linear-gradient(90deg,#2B11DB,rgba(43,17,219,0.5));border-radius:999px;transition:width 0.6s ease;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── Category Analytics ────────────────────────────────────── -->
    <section class="card" style="grid-column:span 12 !important;display:flex;flex-direction:column;">
        <h2 style="font-size:18px;margin-bottom:16px;flex-shrink:0;"><i class="bi bi-tag"></i> Category Views</h2>
        <div id="an-categories-list" style="flex:1;overflow-y:auto;padding-right:6px;max-height:400px;">
            <?php if (empty($catViews)): ?>
                <div style="color:#9ca3af;font-size:13px;padding:16px 0;">No category visits recorded yet.</div>
            <?php else: ?>
                <?php
                    $maxCV  = max(array_values($catViews) ?: [1]);
                    $cRank  = 1;
                    foreach ($catViews as $cname => $ccount):
                        $cpct = (int) round(($ccount / $maxCV) * 100);
                ?>
                <div style="margin-bottom:14px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-size:11px;font-weight:800;color:#9ca3af;min-width:16px;">#<?php echo $cRank++; ?></span>
                            <span style="font-size:13px;font-weight:600;color:#374151;"><?php echo htmlspecialchars($cname); ?></span>
                        </div>
                        <span style="font-size:12px;font-weight:700;background:rgba(16,185,129,0.12);color:#10b981;padding:2px 10px;border-radius:999px;"><?php echo number_format($ccount); ?></span>
                    </div>
                    <div style="background:#e5e7eb;border-radius:999px;height:7px;">
                        <div style="width:<?php echo $cpct; ?>%;height:7px;background:linear-gradient(90deg,#10b981,rgba(16,185,129,0.4));border-radius:999px;transition:width 0.6s ease;"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

</div>

<script>
(function(){
    var POLL = 10000;

    function fmt(n){ return Number(n).toLocaleString(); }

    function setVal(id, v, flash){
        var el = document.getElementById(id);
        if (!el) return;
        if (el.textContent.replace(/,/g,'') !== String(v)){
            el.textContent = fmt(v);
            if (flash){ el.classList.add('flash'); setTimeout(function(){ el.classList.remove('flash'); }, 600); }
        }
    }

    function rebuildRankList(wrapperId, data, accentColor, bgColor){
        var wrap = document.getElementById(wrapperId);
        if (!wrap) return;
        var keys = Object.keys(data||{});
        if (!keys.length){ wrap.innerHTML = '<div style="color:#9ca3af;font-size:13px;padding:16px 0;">No visits recorded yet.</div>'; return; }
        keys.sort(function(a,b){ return data[b]-data[a]; });
        var maxV = data[keys[0]]||1;
        var html = '';
        keys.forEach(function(k, i){
            var pct = Math.round((data[k]/maxV)*100);
            html += '<div style="margin-bottom:14px;">'
                  + '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">'
                  + '<div style="display:flex;align-items:center;gap:8px;">'
                  + '<span style="font-size:11px;font-weight:800;color:#9ca3af;min-width:16px;">#'+(i+1)+'</span>'
                  + '<span style="font-size:13px;font-weight:600;color:#374151;">'+k+'</span>'
                  + '</div>'
                  + '<span style="font-size:12px;font-weight:700;background:'+bgColor+';color:'+accentColor+';padding:2px 10px;border-radius:999px;">'+fmt(data[k])+'</span>'
                  + '</div>'
                  + '<div style="background:#e5e7eb;border-radius:999px;height:7px;">'
                  + '<div style="width:'+pct+'%;height:7px;background:linear-gradient(90deg,'+accentColor+',rgba(43,17,219,0.4));border-radius:999px;transition:width 0.6s ease;"></div>'
                  + '</div></div>';
        });
        wrap.innerHTML = html;
    }

    function rebuildChart30(chart){
        if (!chart||!chart.length) return;
        var maxV = Math.max.apply(null, chart.map(function(b){ return b.views; }));
        maxV = Math.max(maxV,1);
        for (var i=0;i<chart.length;i++){
            var bar = document.getElementById('an-c30-bar-'+i);
            var val = document.getElementById('an-c30-val-'+i);
            if (bar){ var h=Math.max(Math.round((chart[i].views/maxV)*110),4); bar.style.height=h+'px'; bar.title=chart[i].date+': '+chart[i].views+' views'; }
            if (val) val.textContent = chart[i].views;
        }
    }

    function tick(){
        fetch('analytics-api.php',{cache:'no-store'})
            .then(function(r){ return r.json(); })
            .then(function(d){
                setVal('an-total-pv', d.total_pageviews, true);
                setVal('an-today',    d.today_pageviews, true);
                setVal('an-week',     d.week_pageviews,  true);
                setVal('an-month',    d.month_pageviews, true);

                var bTotal = Object.values(d.brands||{}).reduce(function(s,v){return s+v;},0);
                var cTotal = Object.values(d.categories||{}).reduce(function(s,v){return s+v;},0);
                setVal('an-brands-total', bTotal, true);
                setVal('an-cats-total',   cTotal, true);

                var tu = document.getElementById('an-today-u');  if(tu) tu.textContent = fmt(d.today_unique)+' unique';
                var wu = document.getElementById('an-week-u');   if(wu) wu.textContent = fmt(d.week_unique)+' unique';
                var mu = document.getElementById('an-month-u');  if(mu) mu.textContent = fmt(d.month_unique)+' unique';

                rebuildRankList('an-brands-list',     d.brands||{},     '#2B11DB', 'rgba(43,17,219,0.10)');
                rebuildRankList('an-categories-list', d.categories||{}, '#10b981', 'rgba(16,185,129,0.12)');
                rebuildChart30(d.chart30 || d.chart);

                var ts = document.getElementById('an-timestamp');
                if (ts){ var n=new Date(); ts.textContent='Updated '+String(n.getHours()).padStart(2,'0')+':'+String(n.getMinutes()).padStart(2,'0')+':'+String(n.getSeconds()).padStart(2,'0'); }
            })
            .catch(function(){});
    }

    setInterval(tick, POLL);

    // Show initial timestamp
    var ts = document.getElementById('an-timestamp');
    if (ts){ var n=new Date(); ts.textContent='Updated '+String(n.getHours()).padStart(2,'0')+':'+String(n.getMinutes()).padStart(2,'0')+':'+String(n.getSeconds()).padStart(2,'0'); }
})();
</script>

<style>
@media (max-width: 1200px) {
    .card[style*="grid-column:span 6"] { grid-column: span 12 !important; }
}
</style>

<?php
andison_admin_footer();
