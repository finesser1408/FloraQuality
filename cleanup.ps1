$keep = @('app','bootstrap','config','database','public','resources','routes','storage','tests','vendor','breeze_backup')
Get-ChildItem -Path '.' -Directory | Where-Object { $keep -notcontains $_.Name } | ForEach-Object {
    Write-Host "Removing: $($_.Name)"
    Remove-Item -Path $_.FullName -Recurse -Force
}
Write-Host "Done cleaning root directories"
