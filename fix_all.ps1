# UTF-8 BOM para PowerShell 5.1
$enc = [System.Text.UTF8Encoding]::new($false)
$fffd = [char]0xFFFD

# ─── biblioteca.html ───────────────────────────────────────────────────────────
$f = 'C:\Users\andre\Desktop\lexi\biblioteca.html'
$c = [System.IO.File]::ReadAllText($f, $enc)

# UI - U+FFFD
$c = $c.Replace("Pr${fffd}ctica",        "Práctica")
$c = $c.Replace("${fffd}ndice de listas","Índice de listas")
$c = $c.Replace("Nueva colecci${fffd}n", "Nueva colección")
$c = $c.Replace("A${fffd}adir vocabulario","Añadir vocabulario")
$c = $c.Replace("cat${fffd}logo",        "catálogo")
$c = $c.Replace("l${fffd}nea",           "línea")
$c = $c.Replace("Mostrar m${fffd}s",     "Mostrar más")
$c = $c.Replace("Guardar en colecci${fffd}n","Guardar en colección")
$c = $c.Replace("Nueva colecci${fffd}n", "Nueva colección")
$c = $c.Replace("Nombre de la colecci${fffd}n","Nombre de la colección")
# em dash (solo aparece como FFFD solo entre espacios)
$c = $c.Replace(" ${fffd} o ",           " — o ")
$c = $c.Replace(" ${fffd} guarda",       " — guarda")

# Word data - U+FFFD
$c = $c.Replace("xin ch${fffd}o",        "xin chào")
$c = $c.Replace("j${fffd} napot",        "jó napot")
$c = $c.Replace("v${fffd}z",             "víz")
$c = $c.Replace("dobr${fffd} den",       "dobrý den") # cs y sk - se reemplaza las 2 instancias
$c = $c.Replace("o cora${fffd}${fffd}o", "o coração")
$c = $c.Replace("v${fffd}ret",           "været")
$c = $c.Replace("reuni${fffd}n",         "reunión")
# la reunión neerlandesa también
$c = $c.Replace("la reuni${fffd}n",      "la reunión")

# Slovak "dobrý deň" - la segunda instancia dobr? den ya fue reemplazada a dobrý den, 
# pero la sk necesita deň no den; usamos el context del data-word-id
$c = $c.Replace('data-word-id="w-dobry-sk" data-word-label="dobrý den"', 'data-word-id="w-dobry-sk" data-word-label="dobrý deň"')
$c = $c.Replace('<h2 class="card-word">dobrý den</h2>
        <p class="card-translation">buenos días</p>
      </div>
    </article>
    <article class="result-card" data-word-card data-language="hu"', '<h2 class="card-word">dobrý deň</h2>
        <p class="card-translation">buenos días</p>
      </div>
    </article>
    <article class="result-card" data-word-card data-language="hu"')

# Comentarios con FFFD
$c = $c.Replace("<!-- Ce${fffd}tina -->", "<!-- Čeština -->")
$c = $c.Replace("<!-- Rom${fffd}na -->",  "<!-- Română -->")

# Word data - ASCII ? (Greek)
$c = $c.Replace('data-word-label="Ge?a s??" data-translation="hola"', 'data-word-label="Γεια σου" data-translation="hola"')
$c = $c.Replace('<h2 class="card-word">Ge?a s??</h2>', '<h2 class="card-word">Γεια σου</h2>')
$c = $c.Replace('data-word-label="?e??" data-translation="agua"', 'data-word-label="νερό" data-translation="agua"')
$c = $c.Replace('<h2 class="card-word">?e??</h2>', '<h2 class="card-word">νερό</h2>')
$c = $c.Replace('data-word-label="T??assa" data-translation="el mar"', 'data-word-label="Θάλασσα" data-translation="el mar"')
$c = $c.Replace('<h2 class="card-word">T??assa</h2>', '<h2 class="card-word">Θάλασσα</h2>')

# Word data - wrong content (Chinese)
$c = $c.Replace('data-word-id="w-nihao" data-word-label="행복" data-translation="hola"', 'data-word-id="w-nihao" data-word-label="你好" data-translation="hola"')
$c = $c.Replace('data-word-id="w-chi" data-word-label="행복" data-translation="comer"', 'data-word-id="w-chi" data-word-label="吃" data-translation="comer"')
$c = $c.Replace('data-word-id="w-gongzuo" data-word-label="행복" data-translation="trabajo"', 'data-word-id="w-gongzuo" data-word-label="工作" data-translation="trabajo"')

# Fix h2 for Chinese (context: language="zh")
$c = $c.Replace('data-language="zh" data-cefr="A1" data-topic="social" data-word-id="w-nihao"', 'data-language="zh" data-cefr="A1" data-topic="social" data-word-id="w-nihao"')
# Replace h2 content using context around data-word-id
$c = $c -replace '(data-word-id="w-nihao"[^>]*>[\s\S]*?<h2 class="card-word">)행복(</h2>)', '${1}你好${2}'
$c = $c -replace '(data-word-id="w-chi"[^>]*>[\s\S]*?<h2 class="card-word">)행복(</h2>)', '${1}吃${2}'
$c = $c -replace '(data-word-id="w-gongzuo"[^>]*>[\s\S]*?<h2 class="card-word">)행복(</h2>)', '${1}工作${2}'

# Japanese - wrong content
$c = $c.Replace('data-word-id="w-arigato" data-word-label="안녕하세요" data-translation="gracias"', 'data-word-id="w-arigato" data-word-label="ありがとう" data-translation="gracias"')
$c = $c -replace '(data-word-id="w-arigato"[^>]*>[\s\S]*?<h2 class="card-word">)안녕하세요(</h2>)', '${1}ありがとう${2}'
$c = $c.Replace('data-word-id="w-taberu" data-word-label="???" data-translation="comer"', 'data-word-id="w-taberu" data-word-label="食べる" data-translation="comer"')
$c = $c -replace '(data-word-id="w-taberu"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?(</h2>)', '${1}食べる${2}'
$c = $c.Replace('data-word-id="w-sakura" data-word-label="밥" data-translation="flor de cerezo"', 'data-word-id="w-sakura" data-word-label="桜" data-translation="flor de cerezo"')
$c = $c -replace '(data-word-id="w-sakura"[^>]*>[\s\S]*?<h2 class="card-word">)밥(</h2>)', '${1}桜${2}'

# Arabic - wrong content
$c = $c.Replace('data-word-id="w-marhaba" data-word-label="안녕하세요" data-translation="hola"', 'data-word-id="w-marhaba" data-word-label="مرحبا" data-translation="hola"')
$c = $c -replace '(data-word-id="w-marhaba"[^>]*>[\s\S]*?<h2 class="card-word">)안녕하세요(</h2>)', '${1}مرحبا${2}'
$c = $c.Replace('data-word-id="w-shukran" data-word-label="????" data-translation="gracias"', 'data-word-id="w-shukran" data-word-label="شكراً" data-translation="gracias"')
$c = $c -replace '(data-word-id="w-shukran"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?\?(</h2>)', '${1}شكراً${2}'
$c = $c.Replace('data-word-id="w-maa" data-word-label="???" data-translation="agua"', 'data-word-id="w-maa" data-word-label="ماء" data-translation="agua"')
$c = $c -replace '(data-word-id="w-maa"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?(</h2>)', '${1}ماء${2}'

# Hebrew - wrong content
$c = $c.Replace('data-word-id="w-he-bayit" data-word-label="привет" data-translation="casa"', 'data-word-id="w-he-bayit" data-word-label="בַּיִת" data-translation="casa"')
$c = $c -replace '(data-word-id="w-he-bayit"[^>]*>[\s\S]*?<h2 class="card-word">)привет(</h2>)', '${1}בַּיִת${2}'
$c = $c.Replace('data-word-id="w-he-lechem" data-word-label="안녕하세요" data-translation="pan"', 'data-word-id="w-he-lechem" data-word-label="לֶחֶם" data-translation="pan"')
$c = $c -replace '(data-word-id="w-he-lechem"[^>]*>[\s\S]*?<h2 class="card-word">)안녕하세요(</h2>)', '${1}לֶחֶם${2}'
$c = $c.Replace('data-word-id="w-he-avoda" data-word-label="????????" data-translation="trabajo"', 'data-word-id="w-he-avoda" data-word-label="עֲבוֹדָה" data-translation="trabajo"')
$c = $c -replace '(data-word-id="w-he-avoda"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?\?\?\?\?\?(</h2>)', '${1}עֲבוֹדָה${2}'
$c = $c.Replace('data-word-id="w-he-tarbut" data-word-label="??????????" data-translation="cultura"', 'data-word-id="w-he-tarbut" data-word-label="תַּרְבּוּת" data-translation="cultura"')
$c = $c -replace '(data-word-id="w-he-tarbut"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?\?\?\?\?\?\?\?(</h2>)', '${1}תַּרְבּוּת${2}'

# Russian - wrong content
$c = $c.Replace('data-word-id="w-kniga" data-word-label="안녕하세요" data-translation="libro"', 'data-word-id="w-kniga" data-word-label="книга" data-translation="libro"')
$c = $c -replace '(data-word-id="w-kniga"[^>]*>[\s\S]*?<h2 class="card-word">)안녕하세요(</h2>)', '${1}книга${2}'
$c = $c.Replace('data-word-id="w-rabota" data-word-label="привет" data-translation="trabajo"', 'data-word-id="w-rabota" data-word-label="работа" data-translation="trabajo"')
$c = $c -replace '(data-word-id="w-rabota"[^>]*>[\s\S]*?<h2 class="card-word">)привет(</h2>)', '${1}работа${2}'

# Ukrainian - wrong content + ASCII ?
$c = $c.Replace('data-word-id="w-dobroho" data-word-label="??????? ?????" data-translation="buenos días"', 'data-word-id="w-dobroho" data-word-label="Доброго ранку" data-translation="buenos días"')
$c = $c -replace '(data-word-id="w-dobroho"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?\?\?\?\? \?\?\?\?\?(</h2>)', '${1}Доброго ранку${2}'
$c = $c.Replace('data-word-id="w-dim" data-word-label="???" data-translation="casa"', 'data-word-id="w-dim" data-word-label="дім" data-translation="casa"')
$c = $c -replace '(data-word-id="w-dim"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?(</h2>)', '${1}дім${2}'
$c = $c.Replace('data-word-id="w-liubyty" data-word-label="привет" data-translation="amar"', 'data-word-id="w-liubyty" data-word-label="любити" data-translation="amar"')
$c = $c -replace '(data-word-id="w-liubyty"[^>]*>[\s\S]*?<h2 class="card-word">)привет(</h2>)', '${1}любити${2}'

# Vietnamese - ASCII ?
$c = $c.Replace('data-word-id="w-camon" data-word-label="c?m on"', 'data-word-id="w-camon" data-word-label="cảm ơn"')
$c = $c.Replace('<h2 class="card-word">c?m on</h2>', '<h2 class="card-word">cảm ơn</h2>')
$c = $c.Replace('data-word-id="w-nuoc" data-word-label="nu?c"', 'data-word-id="w-nuoc" data-word-label="nước"')
$c = $c.Replace('<h2 class="card-word">nu?c</h2>', '<h2 class="card-word">nước</h2>')

# Hindi - wrong content + ASCII ?
$c = $c.Replace('data-word-id="w-hi-ghar" data-word-label="행복" data-translation="casa"', 'data-word-id="w-hi-ghar" data-word-label="घर" data-translation="casa"')
$c = $c -replace '(data-word-id="w-hi-ghar"[^>]*>[\s\S]*?<h2 class="card-word">)행복(</h2>)', '${1}घर${2}'
$c = $c.Replace('data-word-id="w-hi-khana" data-word-label="????" data-translation="comida"', 'data-word-id="w-hi-khana" data-word-label="खाना" data-translation="comida"')
$c = $c -replace '(data-word-id="w-hi-khana"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?\?(</h2>)', '${1}खाना${2}'
$c = $c.Replace('data-word-id="w-hi-vidyalaya" data-word-label="????????" data-translation="escuela"', 'data-word-id="w-hi-vidyalaya" data-word-label="विद्यालय" data-translation="escuela"')
$c = $c -replace '(data-word-id="w-hi-vidyalaya"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?\?\?\?\?\?(</h2>)', '${1}विद्यालय${2}'
$c = $c.Replace('data-word-id="w-hi-vyapar" data-word-label="???????" data-translation="negocio / comercio"', 'data-word-id="w-hi-vyapar" data-word-label="व्यापार" data-translation="negocio / comercio"')
$c = $c -replace '(data-word-id="w-hi-vyapar"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?\?\?\?\?(</h2>)', '${1}व्यापार${2}'

# Thai - wrong content + ASCII ?
$c = $c.Replace('data-word-id="w-th-aharn" data-word-label="안녕하세요" data-translation="comida"', 'data-word-id="w-th-aharn" data-word-label="อาหาร" data-translation="comida"')
$c = $c -replace '(data-word-id="w-th-aharn"[^>]*>[\s\S]*?<h2 class="card-word">)안녕하세요(</h2>)', '${1}อาหาร${2}'
$c = $c.Replace('data-word-id="w-th-ban" data-word-label="????" data-translation="casa"', 'data-word-id="w-th-ban" data-word-label="บ้าน" data-translation="casa"')
$c = $c -replace '(data-word-id="w-th-ban"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?\?(</h2>)', '${1}บ้าน${2}'
$c = $c.Replace('data-word-id="w-th-thongthiao" data-word-label="??????????" data-translation="viajar / turismo"', 'data-word-id="w-th-thongthiao" data-word-label="ท่องเที่ยว" data-translation="viajar / turismo"')
$c = $c -replace '(data-word-id="w-th-thongthiao"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?\?\?\?\?\?\?\?(</h2>)', '${1}ท่องเที่ยว${2}'
$c = $c.Replace('data-word-id="w-th-watthanatham" data-word-label="????????" data-translation="cultura"', 'data-word-id="w-th-watthanatham" data-word-label="วัฒนธรรม" data-translation="cultura"')
$c = $c -replace '(data-word-id="w-th-watthanatham"[^>]*>[\s\S]*?<h2 class="card-word">)\?\?\?\?\?\?\?\?(</h2>)', '${1}วัฒนธรรม${2}'

# Bulgarian - ASCII ?
$c = $c.Replace('data-word-label="???????" data-translation="hola"', 'data-word-label="Здравей" data-translation="hola"')
$c = $c -replace '(data-language="bg"[\s\S]*?data-translation="hola"[\s\S]*?<h2 class="card-word">)\?\?\?\?\?\?\?(</h2>)', '${1}Здравей${2}'
$c = $c.Replace('data-word-label="????" data-translation="agua"', 'data-word-label="вода" data-translation="agua"')
$c = $c -replace '(data-language="bg"[\s\S]*?data-translation="agua"[\s\S]*?<h2 class="card-word">)\?\?\?\?(</h2>)', '${1}вода${2}'
$c = $c.Replace('data-word-label="???????" data-translation="belleza"', 'data-word-label="красота" data-translation="belleza"')
$c = $c -replace '(data-language="bg"[\s\S]*?data-translation="belleza"[\s\S]*?<h2 class="card-word">)\?\?\?\?\?\?\?(</h2>)', '${1}красота${2}'

[System.IO.File]::WriteAllText($f, $c, $enc)
Write-Host "Fixed: biblioteca.html"

# ─── progreso.html ─────────────────────────────────────────────────────────────
$f = 'C:\Users\andre\Desktop\lexi\progreso.html'
$c = [System.IO.File]::ReadAllText($f, $enc)

$fire   = [char]0xD83D + [char]0xDD25   # 🔥
$party  = [char]0xD83C + [char]0xDF89   # 🎉

$c = $c.Replace('<!-- Cabecera de p?gina -->',      '<!-- Cabecera de página -->')
$c = $c.Replace("<span class=""progress-streak-fire"" id=""streakFire"">??</span>", "<span class=""progress-streak-fire"" id=""streakFire"">$fire</span>")
$c = $c.Replace("streak > 0 ? '??' : '??'",         "streak > 0 ? '$fire' : '$fire'")
$c = $c.Replace("'?Nivel m?ximo alcanzado! ??'",     "'¡Nivel máximo alcanzado! $party'")
$c = $c.Replace('Practicar ?',                       'Practicar →')

[System.IO.File]::WriteAllText($f, $c, $enc)
Write-Host "Fixed: progreso.html"

# ─── perfil.html ───────────────────────────────────────────────────────────────
$f = 'C:\Users\andre\Desktop\lexi\perfil.html'
$c = [System.IO.File]::ReadAllText($f, $enc)

$c = $c.Replace('<!-- Sesi?n -->',                  '<!-- Sesión -->')
$c = $c.Replace('aparici?n en lexiLibrary',         'aparición en lexiLibrary')
$c = $c.Replace('// A?adir activo al final',        '// Añadir activo al final')
$c = $c.Replace("'Sesi?n cerrada (demo ? en producci?n redirigir?as al login).'", "'Sesión cerrada (demo — en producción redirigirías al login).'")
$c = $c.Replace("'?Borrar todas las estad?sticas de pr?ctica? Esto no se puede deshacer.'", "'¿Borrar todas las estadísticas de práctica? Esto no se puede deshacer.'")
$c = $c.Replace("alert('Estad?sticas reiniciadas.')", "alert('Estadísticas reiniciadas.')")
$c = $c.Replace('Perder?s todas las palabras guardadas.',  'Perderás todas las palabras guardadas.')
$c = $c.Replace('Esta acci?n es irreversible.',     'Esta acción es irreversible.')

[System.IO.File]::WriteAllText($f, $c, $enc)
Write-Host "Fixed: perfil.html"

# ─── ejercicios.html ───────────────────────────────────────────────────────────
$f = 'C:\Users\andre\Desktop\lexi\ejercicios.html'
$c = [System.IO.File]::ReadAllText($f, $enc)

$c = $c.Replace('// Registrar sesi?n completada en estad?sticas', '// Registrar sesión completada en estadísticas')

[System.IO.File]::WriteAllText($f, $c, $enc)
Write-Host "Fixed: ejercicios.html"

Write-Host "DONE - todos los archivos corregidos."
