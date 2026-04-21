# Fix all character corruptions in Lexi project
$enc = [System.Text.UTF8Encoding]::new($false)
$folder = 'C:\Users\andre\Desktop\lexi'

function Fix-File($name, $fixes) {
    $path = Join-Path $folder $name
    $c = [System.IO.File]::ReadAllText($path, $enc)
    foreach ($pair in $fixes) { $c = $c.Replace($pair[0], $pair[1]) }
    [System.IO.File]::WriteAllText($path, $c, $enc)
    Write-Host "Fixed: $name"
}

# ────────────────────────────────────────────────────────────────────────────
# COMMON: applied to all files
$common = @(
    @('href="ejercicios.html">Pr?ctica<', 'href="ejercicios.html">Práctica<'),
    @('aria-label="Paginas"',             'aria-label="Páginas"'),
    @('>Paginas<',                        '>Páginas<')
)

# ── index.html ──────────────────────────────────────────────────────────────
Fix-File 'index.html' ($common + @(
    @('en m?s de 30 idiomas.',                          'en más de 30 idiomas.'),
    @('>?Qu? puedes hacer?<',                           '>¿Qué puedes hacer?<'),
    @('organ?zalas por colecciones y rep?salas',        'organízalas por colecciones y repásalas'),
    @('>Practica personalizada<',                       '>Práctica personalizada<'),
    @('>An?lisis de progreso<',                         '>Análisis de progreso<'),
    @('evoluci?n mediante gr?ficas y estad?sticas',     'evolución mediante gráficas y estadísticas'),
    @('>?C?mo funciona?<',                              '>¿Cómo funciona?<'),
    @('el cat?logo y encuentra',                        'el catálogo y encuentra'),
    @('>Gu?rdalas<',                                    '>Guárdalas<'),
    @('Organ?zalas en colecciones personalizadas seg?n','Organízalas en colecciones personalizadas según'),
    @('m?s ejercicios personalizados y pr?cticas',      'más ejercicios personalizados y prácticas'),
    @('Ilustraci?n aprendizaje premium',                'Ilustración aprendizaje premium'),
    @('>?Listo para empezar?<',                         '>¡Listo para empezar!<'),
    @('Explorar cat?logo',                              'Explorar catálogo')
))

# ── ejercicios.html ─────────────────────────────────────────────────────────
# Multilingual SKILL_LABELS
$ko_read   = [string][char]0xC77D + [char]0xAE30          # 읽기
$ko_listen = [string][char]0xB4E3 + [char]0xAE30          # 듣기
$ko_speak  = [string][char]0xB9D0 + [char]0xD558 + [char]0xAE30  # 말하기
$ko_write  = [string][char]0xC4F0 + [char]0xAE30          # 쓰기

$zh_read   = [string][char]0x9605 + [char]0x8BFB          # 阅读
$zh_listen = [string][char]0x542C + [char]0x529B          # 听力
$zh_speak  = [string][char]0x53E3 + [char]0x8BED          # 口语
$zh_write  = [string][char]0x5199 + [char]0x4F5C          # 写作

$ru_read   = [string][char]0x427+[char]0x442+[char]0x435+[char]0x43D+[char]0x438+[char]0x435  # Чтение
$ru_listen = [string][char]0x421+[char]0x43B+[char]0x443+[char]0x448+[char]0x430+[char]0x43D+[char]0x438+[char]0x435  # Слушание
$ru_speak  = [string][char]0x413+[char]0x43E+[char]0x432+[char]0x43E+[char]0x440+[char]0x435+[char]0x43D+[char]0x438+[char]0x435  # Говорение
$ru_write  = [string][char]0x41F+[char]0x438+[char]0x441+[char]0x44C+[char]0x43C+[char]0x43E  # Письмо

$ua_read   = [string][char]0x427+[char]0x438+[char]0x442+[char]0x430+[char]0x43D+[char]0x43D+[char]0x44F  # Читання
$ua_listen = [string][char]0x421+[char]0x43B+[char]0x443+[char]0x445+[char]0x430+[char]0x43D+[char]0x43D+[char]0x44F  # Слухання
$ua_speak  = [string][char]0x413+[char]0x43E+[char]0x432+[char]0x43E+[char]0x440+[char]0x456+[char]0x43D+[char]0x43D+[char]0x44F  # Говоріння
# $ru_write same for Ukrainian

$gr_read   = [string][char]0x391+[char]0x3BD+[char]0x3AC+[char]0x3B3+[char]0x3BD+[char]0x3C9+[char]0x3C3+[char]0x3B7  # Ανάγνωση
$gr_listen = [string][char]0x391+[char]0x3BA+[char]0x3C1+[char]0x3CC+[char]0x3B1+[char]0x3C3+[char]0x3B7  # Ακρόαση
$gr_speak  = [string][char]0x39F+[char]0x3BC+[char]0x3B9+[char]0x3BB+[char]0x3AF+[char]0x3B1  # Ομιλία
$gr_write  = [string][char]0x393+[char]0x3C1+[char]0x3B1+[char]0x3C6+[char]0x3AE  # Γραφή

Fix-File 'ejercicios.html' ($common + @(
    @('Sesi?n de hoy',                 'Sesión de hoy'),
    @('?Qu? quieres practicar?',       '¿Qué quieres practicar?'),
    @('empieza ? cada ejercicio',      'empieza — cada ejercicio'),
    @('<div class="ex-complete-icon">??</div>', '<div class="ex-complete-icon">🎉</div>'),
    @('>?Seccion completada!<',        '>¡Sección completada!<'),
    @("// T?rminos de habilidad",      '// Términos de habilidad'),
    @("'?coute'",                      "'Écoute'"),
    @("'?criture'",                    "'Écriture'"),
    @("'H?ren'",                       "'Hören'"),
    @("'L?sning'",                     "'Læsning'"),
    @("'??',        '??',      '???',      '??',       'Mix'", "'$ko_read', '$ko_listen', '$ko_speak', '$ko_write', 'Mix'"),
    @("'??',        '??',      '??',        '??',       'Mix'", "'$zh_read', '$zh_listen', '$zh_speak', '$zh_write', 'Mix'"),
    @("'??????',      '????????',  '?????????',   '??????',     'Mix'", "'$ru_read', '$ru_listen', '$ru_speak', '$ru_write', 'Mix'"),
    @("'???????',     '????????',  '?????????',   '??????',     'Mix'", "'$ua_read', '$ua_listen', '$ua_speak', '$ua_write', 'Mix'"),
    @("'??????s?',   '????as?',   '?????a',      'G?af?',      'Mix'", "'$gr_read', '$gr_listen', '$gr_speak', '$gr_write', 'Mix'"),
    @('Actualizar badges seg?n',       'Actualizar badges según')
))

# ── progreso.html ───────────────────────────────────────────────────────────
Fix-File 'progreso.html' ($common + @(
    @('d?as de racha',                   'días de racha'),
    @("en:'Ingl?s'",                     "en:'Inglés'"),
    @("fr:'Franc?s'",                    "fr:'Francés'"),
    @("de:'Alem?n'",                     "de:'Alemán'"),
    @("es:'Espa?ol'",                    "es:'Español'"),
    @("dk:'Dan?s'",                      "dk:'Danés'"),
    @("fi:'Fin?s'",                      "fi:'Finés'"),
    @("Est?s empezando. ?Cada palabra cuenta!", "Estás empezando. ¡Cada palabra cuenta!"),
    @('la mayor?a de contextos.',        'la mayoría de contextos.'),
    @('con precisi?n.',                  'con precisión.'),
    @('?Nivel m?ximo!',                  '¡Nivel máximo!'),
    @("name + ' ? ' +",                  "name + ' — ' +"),
    @("'?Llevas ' + streak + ' d?as seguidos! Sigue as?.'", "'¡Llevas ' + streak + ' días seguidos! Sigue así.'"),
    @("'?Buen comienzo! Vuelve ma?ana para mantener la racha.'", "'¡Buen comienzo! Vuelve mañana para mantener la racha.'"),
    @('Sesiones de pr?ctica',            'Sesiones de práctica'),
    @('?ltimas palabras guardadas',      'Últimas palabras guardadas'),
    @('A?n no',                          'Aún no'),
    @("seg?n",                           "según")
))

# ── biblioteca.html ─────────────────────────────────────────────────────────
# Korean word data
$ko_annyeong = [string][char]0xC548+[char]0xB155+[char]0xD558+[char]0xC138+[char]0xC694  # 안녕하세요
$ko_bap      = [string][char]0xBC25  # 밥
$ko_happy    = [string][char]0xD589+[char]0xBCF5  # 행복
$ru_privet   = [string][char]0x43F+[char]0x440+[char]0x438+[char]0x432+[char]0x435+[char]0x442  # привет

Fix-File 'biblioteca.html' ($common + @(
    @('?ndice de listas',                'Índice de listas'),
    @('Nueva colecci?n',                 'Nueva colección'),
    @('Mostrar m?s',                     'Mostrar más'),
    @('A?adir vocabulario',              'Añadir vocabulario'),
    @('el cat?logo',                     'el catálogo'),
    @('Buscar en el cat?logo',           'Buscar en el catálogo'),
    @('por l?nea.',                      'por línea.'),
    @('por l?nea ?',                     'por línea —'),
    @('data-word-label="?????"',         "data-word-label=`"$ko_annyeong`""),
    @('<h2 class="card-word">?????</h2>',"<h2 class=`"card-word`">$ko_annyeong</h2>"),
    @('data-word-label="?"',             "data-word-label=`"$ko_bap`""),
    @('<h2 class="card-word">?</h2>',    "<h2 class=`"card-word`">$ko_bap</h2>"),
    @('data-word-label="??"',            "data-word-label=`"$ko_happy`""),
    @('<h2 class="card-word">??</h2>',   "<h2 class=`"card-word`">$ko_happy</h2>"),
    @('data-word-label="??????"',        "data-word-label=`"$ru_privet`""),
    @('<h2 class="card-word">??????</h2>',"<h2 class=`"card-word`">$ru_privet</h2>"),
    @('o explora el cat?logo',           'o explora el catálogo')
))

# ── carrito.html ────────────────────────────────────────────────────────────
Fix-File 'carrito.html' ($common + @(
    @('eliminaci?n',                     'eliminación'),
    @('?Quieres eliminar este producto', '¿Quieres eliminar este producto')
))

# ── contacto.html ───────────────────────────────────────────────────────────
Fix-File 'contacto.html' ($common + @(
    @('eliminaci?n',                     'eliminación'),
    @('?Quieres eliminar',               '¿Quieres eliminar')
))

# ── info.html ───────────────────────────────────────────────────────────────
Fix-File 'info.html' ($common + @(
    @('C?mo funciona Lexi',              'Cómo funciona Lexi'),
    @('est? pensado',                    'está pensado'),
    @('a qu? ritmo',                     'a qué ritmo'),
    @('gu?rdalas',                       'guárdalas'),
    @('ampl?a l?mites',                  'amplía límites'),
    @('amplia l?mites',                  'amplía límites'),
    @('ampl?a limites',                  'amplía límites'),
    @('amplia limites',                  'amplía límites'),
    @('pr?ctica avanzada',               'práctica avanzada')
))

# ── producto.html ───────────────────────────────────────────────────────────
Fix-File 'producto.html' ($common + @(
    @('sigue siendo ?til',               'sigue siendo útil'),
    @('ampl?a l?mites',                  'amplía límites'),
    @('amplia l?mites',                  'amplía límites'),
    @('ampl?a limites',                  'amplía límites'),
    @('amplia limites',                  'amplía límites'),
    @('pr?ctica avanzada',               'práctica avanzada'),
    @('M?s generaciones',                'Más generaciones'),
    @('Estad?sticas',                    'Estadísticas'),
    @('A?adir al carrito',               'Añadir al carrito'),
    @('a?o',                             'año'),
    @('eliminaci?n',                     'eliminación'),
    @('?Quieres eliminar',               '¿Quieres eliminar')
))

# ── perfil.html ─────────────────────────────────────────────────────────────
Fix-File 'perfil.html' ($common + @(
    @('Ver mi progreso ?',               'Ver mi progreso →'),
    @('Informaci?n de cuenta',           'Información de cuenta'),
    @('Correo electr?nico',              'Correo electrónico'),
    @("alt=`"Espa?ol`"",                 "alt=`"Español`""),
    @('>Espa?ol<',                       '>Español<'),
    @('cada d?a para mantener',          'cada día para mantener'),
    @('Sesi?n activa',                   'Sesión activa'),
    @('Cerrar sesi?n',                   'Cerrar sesión'),
    @(">Sesi?n<",                        ">Sesión<"),
    @('Reiniciar estad?sticas',          'Reiniciar estadísticas'),
    @('datos de pr?ctica',               'datos de práctica'),
    @("label: 'Ingl?s'",                 "label: 'Inglés'"),
    @("label: 'Franc?s'",                "label: 'Francés'"),
    @("label: 'Alem?n'",                 "label: 'Alemán'"),
    @("label: 'Espa?ol'",                "label: 'Español'"),
    @("label: 'Dan?s'",                  "label: 'Danés'"),
    @("label: 'Fin?s'",                  "label: 'Finés'"),
    @("label: 'Tailand?s'",              "label: 'Tailandés'"),
    @("label: 'Neerland?s'",             "label: 'Neerlandés'"),
    @('// A?adir idiomas',               '// Añadir idiomas'),
    @('A?n no has estudiado ning?n idioma.', 'Aún no has estudiado ningún idioma.'),
    @('// Orden cronol?gico:',           '// Orden cronológico:'),
    @('palabras guardadas a?n',          'palabras guardadas aún'),
    @("'>Sesi?n<",                       "'>Sesión<")
))

Write-Host "`nTodos los archivos corregidos."
