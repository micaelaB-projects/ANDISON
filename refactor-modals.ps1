# Refactor all category pages to use unified product_modal.php
# Removes duplicate modal code and ensures unified modal is used

$categoryDirs = @(
    "arc-welding-machine", "batteries", "drilling-and-lifting", "gas-detectors",
    "portable-ventilators", "power-tools", "protection", "welding-accessories", "welding-consumables"
)

$updated = 0
$errors = 0

foreach ($dir in $categoryDirs) {
    if (-not (Test-Path $dir)) { continue }
    
    Get-ChildItem -Path $dir -Filter "*.php" | Where-Object {
        $_.Name -match "^[a-z].*\.php$" -or $_.Name -like "*-*.php"
    } | ForEach-Object {
        $filePath = $_.FullName
        
        try {
            # Read file
            $content = Get-Content -Path $filePath -Raw -Encoding UTF8
            
            # Check if already has product_modal include
            if ($content -notmatch 'product_modal\.php') {
                Write-Host "⚠ Skipping $($_.Name) - no product_modal include found"
                return
            }
            
            # Remove openProductModal function definition (lines starting with "function openProductModal")
            $content = $content -replace '(?s)\s*function\s+openProductModal\s*\([^)]*\)\s*\{(?:[^{}]|(?:\{[^{}]*\}))*?\}\s*function\s+closeModal\s*\(\)\s*\{[^}]*?\}', ''
            
            # Remove orphaned modal event listeners (everything between </script> and next <script> that looks like modal code)
            $content = $content -replace '(?s)</script>\s*\n\s*// Add click listeners to all product cards\s*\n\s*document\.addEventListener\(''DOMContentLoaded''(?:[^<]|<(?!script>))*?(?=\s*</script>\s*\n\s*<script>var CATEGORY_NAME)', '</script>'
            
            # Ensure proper spacing before CATEGORY_NAME line
            $content = $content -replace '(?s)</script>\s*\n\s+// Modal inquiry button[\s\S]*?(?=<script>var CATEGORY_NAME)', "`n</script>`n`n<script>"
            
            # Write back
            Set-Content -Path $filePath -Value $content -Encoding UTF8
            Write-Host "✓ Updated $($_.Name)"
            $updated++
        }
        catch {
            Write-Host "✗ Error updating $($_.Name): $_"
            $errors++
        }
    }
}

Write-Host "`n=== Summary ===" 
Write-Host "Updated: $updated files"
Write-Host "Errors: $errors files"
