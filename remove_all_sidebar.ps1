$file = 'c:\xampp\htdocs\ANDISON\services.php'
[System.IO.File]::ReadAllLines($file) | Where-Object {
    $_ -notmatch '\.sidebar' -and `
    $_ -notmatch '\.mini-sidebar' -and `
    $_ -notmatch '\.overlay-backdrop' -and `
    $_ -notmatch '\.sub-toggle' -and `
    $_ -notmatch '\.nested-toggle' -and `
    $_ -notmatch '\.mini-popover' -and `
    $_ -notmatch '\.mobile-sidebar-fab' -and `
    $_ -notmatch '#sidebar' -and `
    $_ -notmatch '#miniSidebar' -and `
    $_ -notmatch '#overlayBackdrop' -and `
    $_ -notmatch '#miniPopover' -and `
    $_ -notmatch '#mobileSidebarFab' -and `
    $_ -notmatch '#expandSidebar' -and `
    $_ -notmatch '#closeSidebar' -and `
    $_ -notmatch 'Sidebar|sidebar|overlay.*sidebar'
} | Set-Content $file

$finalCount = @(Get-Content $file).Count
$sidebarCount = @(Select-String -Path $file -Pattern 'sidebar').Count

Write-Host "Cleanup complete!"
Write-Host "Final file: $finalCount lines"
Write-Host "Remaining sidebar refs: $sidebarCount"
