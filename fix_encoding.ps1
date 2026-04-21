cd "C:\Users\andre\Desktop\lexi"
$utf8 = [System.Text.Encoding]::UTF8
$w1252 = [System.Text.Encoding]::GetEncoding(1252, [System.Text.EncoderFallback]::ReplacementFallback, [System.Text.DecoderFallback]::ReplacementFallback)
$utf8nb = New-Object System.Text.UTF8Encoding $false

function Detect-Level($path) {
    $t = [System.IO.File]::ReadAllText($path, $utf8)
    if ($t -match "ÃƒÂ") { return 3 }
    if ($t -match "Ã¡|Ã³|Ã©|Ã­|Ãº|Ã±") { return 2 }
    if ($t -match "[áéíóúñüÁÉÍÓÚÑ]") { return 0 }
    return 1
}

function Fix-File($path, $levels) {
    $t = [System.IO.File]::ReadAllText($path, $utf8)
    for ($i = 0; $i -lt $levels; $i++) {
        $b = $w1252.GetBytes($t)
        $t = $utf8.GetString($b)
    }
    [System.IO.File]::WriteAllText($path, $t, $utf8nb)
}

$files = @("index.html","perfil.html","carrito.html","ejercicios.html","contacto.html","producto.html","progreso.html","info.html","biblioteca.html")

foreach ($f in $files) {
    $path = "C:\Users\andre\Desktop\lexi\$f"
    $level = Detect-Level $path
    Write-Host "$f -> level $level"
    if ($level -gt 0) {
        Fix-File $path $level
        Write-Host "  Fixed with $level pass(es)"
    } else {
        Write-Host "  Already OK"
    }
}

Write-Host ""
Write-Host "Verification:"
$t = [System.IO.File]::ReadAllText("C:\Users\andre\Desktop\lexi\index.html", $utf8)
$i = $t.IndexOf("30 idiomas")
if ($i -ge 0) { Write-Host "index.html: '$($t.Substring($i-6,10))'" }
$t2 = [System.IO.File]::ReadAllText("C:\Users\andre\Desktop\lexi\biblioteca.html", $utf8)
$i2 = $t2.IndexOf("cat")
while ($i2 -ge 0 -and $i2 -lt $t2.Length - 8) {
    $sub = $t2.Substring($i2, 8)
    if ($sub -match "cat.log") { Write-Host "biblioteca.html catalogo: '$sub'"; break }
    $i2 = $t2.IndexOf("cat", $i2+1)
}
Write-Host "Done"
