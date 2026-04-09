<?php
declare(strict_types=1);

require_once __DIR__ . '/Andison/includes/supabase.php';

// Get all brands from Supabase
$brands = andison_sb_select('brands?order=name');

if (!is_array($brands)) {
    die("Error: Failed to fetch brands\n");
}

// Group by lowercase name to find duplicates
$groups = [];
foreach ($brands as $brand) {
    $name = $brand['name'] ?? '';
    $lower = strtolower($name);
    if (!isset($groups[$lower])) {
        $groups[$lower] = [];
    }
    $groups[$lower][] = $brand;
}

// Find duplicates and decide which to keep
$toDelete = [];
foreach ($groups as $lower => $brandList) {
    if (count($brandList) > 1) {
        echo "Found duplicate: " . $lower . "\n";
        
        // Sort by format priority: prefer ALL_CAPS or Title Case over lowercase
        usort($brandList, function($a, $b) {
            $aName = $a['name'] ?? '';
            $bName = $b['name'] ?? '';
            
            // Priority: 1. ALL_CAPS, 2. Title Case (first letter cap), 3. lowercase
            $aScore = (ctype_upper(str_replace(' ', '', $aName))) ? 1 : ((ctype_upper($aName[0] ?? '')) ? 2 : 3);
            $bScore = (ctype_upper(str_replace(' ', '', $bName))) ? 1 : ((ctype_upper($bName[0] ?? '')) ? 2 : 3);
            
            return $aScore - $bScore;
        });
        
        // Keep first (best formatted), delete the rest
        for ($i = 1; $i < count($brandList); $i++) {
            $brandToDelete = $brandList[$i];
            $id = $brandToDelete['id'] ?? null;
            if ($id) {
                echo "  DELETE: '" . ($brandToDelete['name'] ?? '') . "' (id: " . $id . ")\n";
                echo "  KEEP: '" . ($brandList[0]['name'] ?? '') . "'\n";
                $toDelete[] = $id;
            }
        }
    }
}

if (empty($toDelete)) {
    echo "\nNo duplicates to remove\n";
    exit(0);
}

echo "\n\nDeleting " . count($toDelete) . " duplicate brand entries...\n";

foreach ($toDelete as $id) {
    $filter = 'id=eq.' . (string)$id;
    echo "  Attempting delete with filter: " . $filter . "\n";
    $ok = andison_sb_delete('brands', $filter);
    if ($ok) {
        echo "  ✓ Deleted id " . $id . "\n";
    } else {
        echo "  ✗ Failed to delete id " . $id . "\n";
    }
}

// Clear cache
$cacheFile = __DIR__ . '/Andison/data/_cache/brands_full.cache';
if (file_exists($cacheFile)) {
    unlink($cacheFile);
    echo "\nCache cleared\n";
}

echo "\nDone!\n";
?>
