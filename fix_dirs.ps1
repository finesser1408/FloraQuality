# Fix all nested directories that are doubled up
$dirs = @('bootstrap', 'config', 'public', 'resources', 'routes', 'storage', 'tests', 'database', 'vendor')

foreach ($d in $dirs) {
    $nested = Join-Path $d $d
    if (Test-Path $nested) {
        Write-Host "Fixing nested: $nested"
        # Copy nested contents up one level
        Get-ChildItem -Path $nested -Force | ForEach-Object {
            $dest = Join-Path $d $_.Name
            Copy-Item -Path $_.FullName -Destination $dest -Recurse -Force
        }
        # Remove nested directory
        Remove-Item -Path $nested -Recurse -Force
        Write-Host "  Fixed: $d"
    } else {
        Write-Host "OK: $d"
    }
}
Write-Host "Done fixing nested dirs"
