<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

function andison_get_categories(): array
{
    $cats    = andison_sb_select('categories', 'order=name');
    $subs    = andison_sb_select('subcategories', 'order=name&limit=500');
    $subsubs = andison_sb_select('sub_subcategories', 'order=name&limit=500');

    if (empty($cats)) {
        // Fallback to local JSON
        $jsonFile = __DIR__ . '/../data/categories_info.json';
        if (!file_exists($jsonFile)) return [];
        $content = file_get_contents($jsonFile);
        if ($content === false) return [];
        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : [];
    }

    // Index sub-subcategories by subcategory_id
    $subsubIndex = [];
    foreach ($subsubs as $ss) {
        $subId = $ss['subcategory_id'] ?? '';
        if ($subId !== '') {
            $subsubIndex[$subId][] = ['id' => $ss['id'], 'name' => $ss['name']];
        }
    }

    // Index subcategories by category_id
    $subIndex = [];
    foreach ($subs as $sub) {
        $catId = $sub['category_id'] ?? '';
        if ($catId !== '') {
            $subIndex[$catId][] = [
                'id'            => $sub['id'],
                'name'          => $sub['name'],
                'subcategories' => $subsubIndex[$sub['id']] ?? [],
            ];
        }
    }

    $result = [];
    foreach ($cats as $cat) {
        $result[] = [
            'id'            => $cat['id'],
            'name'          => $cat['name'],
            'description'   => $cat['description'] ?? '',
            'icon'          => $cat['icon'] ?? '',
            'subcategories' => $subIndex[$cat['id']] ?? [],
        ];
    }

    return $result;
}
function andison_save_categories(array $categories): bool
{
    // Backup to local JSON
    $jsonFile = __DIR__ . '/../data/categories_info.json';
    $dir = dirname($jsonFile);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $handle = fopen($jsonFile, 'c');
    if ($handle !== false) {
        if (flock($handle, LOCK_EX)) {
            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            flock($handle, LOCK_UN);
        }
        fclose($handle);
    }

    // Save to Supabase
    $catRows = $subRows = $subsubRows = [];
    foreach ($categories as $cat) {
        $catRows[] = [
            'id'          => $cat['id'],
            'name'        => $cat['name'],
            'description' => $cat['description'] ?? '',
            'icon'        => $cat['icon'] ?? '',
        ];
        foreach ($cat['subcategories'] ?? [] as $sub) {
            $subRows[] = [
                'id'          => $sub['id'],
                'category_id' => $cat['id'],
                'name'        => $sub['name'],
            ];
            foreach ($sub['subcategories'] ?? [] as $ss) {
                $subsubRows[] = [
                    'id'             => $ss['id'],
                    'subcategory_id' => $sub['id'],
                    'category_id'    => $cat['id'],
                    'name'           => $ss['name'],
                ];
            }
        }
    }

    $ok1 = andison_sb_upsert('categories', $catRows);
    $ok2 = andison_sb_upsert('subcategories', $subRows);
    $ok3 = empty($subsubRows) || andison_sb_upsert('sub_subcategories', $subsubRows);

    return $ok1 && $ok2 && $ok3;
}

