$files = Get-ChildItem -Path . -Recurse -Filter *.php | Where-Object { $_.FullName -notmatch "vendor|node_modules" }
$newCss = "`n        .nav-dropdown::before { content: ''; position: absolute; top: -16px; left: 0; width: 100%; height: 16px; background: transparent; }"
foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw
    if ($content -match '\.nav-dropdown\s*\{' -and $content -notmatch '\.nav-dropdown::before') {
        $content = $content -replace '(\.nav-dropdown\s*\{)', ($newCss + "`n`$1")
        $content | Set-Content -NoNewline $file.FullName
        Write-Host "Updated $($file.Name)"
    }
}
