<?php

declare(strict_types=1);

function andison_get_categories(): array
{
    $jsonFile = __DIR__ . '/../data/categories_info.json';
    
    if (!file_exists($jsonFile)) {
        return [];
    }
    
    $content = file_get_contents($jsonFile);
    if ($content === false) {
        return [];
    }
    
    $categories = json_decode($content, true);
    
    if (!is_array($categories)) {
        return [];
    }
    
    return $categories;
}

function andison_save_categories(array $categories): bool
{
    $jsonFile = __DIR__ . '/../data/categories_info.json';
    
    // Ensure directory exists
    $dir = dirname($jsonFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Write JSON with lock
    $handle = fopen($jsonFile, 'c');
    if ($handle === false) {
        return false;
    }
    
    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return false;
    }
    
    rewind($handle);
    ftruncate($handle, 0);
    
    $json = json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $written = fwrite($handle, $json);
    
    flock($handle, LOCK_UN);
    fclose($handle);
    
    return $written !== false && $written > 0;
}
