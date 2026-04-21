$file = 'C:\Users\andre\Desktop\lexi\biblioteca.html'
$text = [System.IO.File]::ReadAllText($file, [System.Text.Encoding]::UTF8)
$R = [char]0xFFFD

# UI TEXT FIXES
$text = $text -replace "Pr${R}ctica", 'Práctica'
$text = $text -replace "${R}ndice de listas", 'Índice de listas'
$text = $text -replace "Nueva colecci${R}n", 'Nueva colección'
$text = $text -replace "A${R}adir vocabulario", 'Añadir vocabulario'
$text = $text -replace "cat${R}logo", 'catálogo'
$text = $text -replace "l${R}nea $R o escribe solo una", 'línea — o escribe solo una'
$text = $text -replace "l${R}nea\.", 'línea.'
$text = $text -replace "l${R}nea", 'línea'
$text = $text -replace "Mostrar m${R}s", 'Mostrar más'
$text = $text -replace "Guardar en colecci${R}n", 'Guardar en colección'
$text = $text -replace "Nombre de la colecci${R}n", 'Nombre de la colección'
$text = $text -replace "guardar en colecci${R}n", 'guardar en colección'

# Norwegian
$text = $text -replace "v${R}ret", 'været'

# Portuguese
$text = $text -replace "o cora${R}${R}o", 'o coração'

# Turkish
$text = $text -replace "reuni${R}n", 'reunión'

# Czech
$text = $text -replace "dobr${R} den", 'dobrý den'

# Hungarian
$text = $text -replace "j${R} napot", 'jó napot'
$text = $text -replace "v${R}z", 'víz'

# Vietnamese
$text = $text -replace "xin ch${R}o", 'xin chào'

# HTML comments
$text = $text -replace "<!-- Portugu${R}s -->", '<!-- Português -->'
$text = $text -replace "<!-- Ce${R}tina -->", '<!-- Čeština -->'
$text = $text -replace "<!-- Rom${R}na -->", '<!-- Română -->'

$R2 = [char]0xFFFD
$countAfter = ($text.ToCharArray() | Where-Object {$_ -eq $R2}).Count
[System.IO.File]::WriteAllText($file, $text, [System.Text.Encoding]::UTF8)
Write-Host "Done. Remaining U+FFFD chars: $countAfter"
