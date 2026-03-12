<?php

declare(strict_types=1);

require_once __DIR__ . '/supabase.php';

function andison_get_categories(): array
{
    $cats    = andison_sb_select('categories', 'order=name');
    $subs    = andison_sb_select('subcategories', 'order=name&limit=500');
    $subsubs = andison_sb_select('sub_subcategories', 'order=name&limit=500');

    if (empty($cats)) {
        return [];
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

