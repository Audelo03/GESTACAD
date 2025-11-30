Get-ChildItem -Path "c:\xampp\htdocs\GESTACAD" -Include *.php -Recurse | ForEach-Object {
    $content = Get-Content $_.FullName -Raw
    $newContent = $content -replace '/GORA/', '/GESTACAD/'
    if ($content -ne $newContent) {
        Set-Content -Path $_.FullName -Value $newContent -NoNewline
        Write-Host "Updated: $($_.FullName)"
    }
}
