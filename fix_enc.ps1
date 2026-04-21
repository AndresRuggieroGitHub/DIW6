param()
$folder="C:\Users\andre\Desktop\lexi"
$files=@("index.html","perfil.html","carrito.html","ejercicios.html","contacto.html","producto.html","progreso.html","info.html","biblioteca.html")
$latin1=[System.Text.Encoding]::GetEncoding("iso-8859-1")
$utf8=[System.Text.Encoding]::UTF8
foreach($fname in $files){
$path=Join-Path $folder $fname
if(!(Test-Path $path)){Write-Host "MISSING: $fname";continue}
$raw=[System.IO.File]::ReadAllBytes($path)
$content=$utf8.GetString($raw)
$passes=0;$test=$content
while($test -match "Ã" -and $passes -lt 4){try{$b=$latin1.GetBytes($test);$test=$utf8.GetString($b);$passes++}catch{break}}
if($passes -gt 0){$fixed=$content;for($i=0;$i -lt $passes;$i++){$b=$latin1.GetBytes($fixed);$fixed=$utf8.GetString($b)};[System.IO.File]::WriteAllText($path,$fixed,$utf8);Write-Host "Fixed $fname with $passes pass(es)"}else{Write-Host "OK (no fix needed): $fname"}
}
Write-Host "DONE"
$c=[System.IO.File]::ReadAllText((Join-Path $folder "index.html"),$utf8)
$idx=$c.IndexOf("30 idiomas")
if($idx -ge 0){Write-Host "Verification: $(($c.Substring([Math]::Max(0,$idx-6),16)))"}
