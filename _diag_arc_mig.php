<?php
declare(strict_types=1);
require_once __DIR__ . '/Andison/includes/supabase.php';

$rows = andison_sb_select('products', 'select=id,brand,model,type,category_id,subcategory_id,sub_subcategory_id&limit=10000');

$targets = [
    'arc-welding-robots' => 'arc-welding-robot',
    'arc-welding-machine' => 'mig-welding-machine',
];

echo "Total rows: " . count($rows) . PHP_EOL;

foreach ($targets as $cat => $sub) {
    $exact = 0;
    $catOnly = 0;
    $nameLike = 0;
    echo PHP_EOL . "=== {$cat} / {$sub} ===" . PHP_EOL;

    foreach ($rows as $r) {
        $c = strtolower(trim((string)($r['category_id'] ?? '')));
        $s = strtolower(trim((string)($r['subcategory_id'] ?? '')));
        $ss = strtolower(trim((string)($r['sub_subcategory_id'] ?? '')));
        $type = strtolower(trim((string)($r['type'] ?? '')));
        $model = strtolower(trim((string)($r['model'] ?? '')));

        if ($c === $cat && ($s === $sub || $ss === $sub)) {
            $exact++;
        } elseif ($c === $cat) {
            $catOnly++;
        }

        if (
            str_contains($s, 'arc welding robot') || str_contains($s, 'mig welding machine') ||
            str_contains($s, 'robot') || str_contains($s, 'mig') ||
            str_contains($type, 'robot') || str_contains($type, 'mig') ||
            str_contains($model, 'robot') || str_contains($model, 'mig')
        ) {
            $nameLike++;
        }
    }

    echo "Exact match rows: {$exact}" . PHP_EOL;
    echo "Category-only rows: {$catOnly}" . PHP_EOL;
    echo "Potential name-style rows (global heuristic): {$nameLike}" . PHP_EOL;
}

// Show sample mismatches inside target categories
foreach ($targets as $cat => $sub) {
    echo PHP_EOL . "--- Sample rows in category {$cat} not matching {$sub} ---" . PHP_EOL;
    $shown = 0;
    foreach ($rows as $r) {
        $c = strtolower(trim((string)($r['category_id'] ?? '')));
        $s = trim((string)($r['subcategory_id'] ?? ''));
        $ss = trim((string)($r['sub_subcategory_id'] ?? ''));
        if ($c !== $cat) {
            continue;
        }
        if (strtolower($s) === $sub || strtolower($ss) === $sub) {
            continue;
        }
        echo (string)($r['id'] ?? '') . ' | ' . (string)($r['brand'] ?? '') . ' | sub=' . $s . ' | subsub=' . $ss . ' | model=' . (string)($r['model'] ?? '') . PHP_EOL;
        $shown++;
        if ($shown >= 15) {
            break;
        }
    }
    if ($shown === 0) {
        echo "(none)" . PHP_EOL;
    }
}
