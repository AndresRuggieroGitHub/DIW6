$file = 'C:\Users\andre\Desktop\lexi\biblioteca.html'
$content = Get-Content $file -Raw -Encoding UTF8

$fixes = @{
    'w-kniga'    = 'книга'
    'w-rabota'   = 'работа'
    'w-dobroho'  = 'Доброго ранку'
    'w-dim'      = 'дім'
    'w-liubyty'  = 'любити'
    'w-yeia'     = 'Γεια σου'
    'w-nero'     = 'νερό'
    'w-thalassa' = 'Θάλασσα'
    'w-nihao'    = '你好'
    'w-chi'      = '吃'
    'w-gongzuo'  = '工作'
    'w-arigato'  = 'ありがとう'
    'w-taberu'   = '食べる'
    'w-sakura'   = '桜'
    'w-zdravey'  = 'Здравей'
    'w-voda-bg'  = 'вода'
    'w-krasota'  = 'красота'
    'w-marhaba'  = 'مرحبا'
    'w-shukran'  = 'شكراً'
    'w-maa'      = 'ماء'
    'w-he-bayit'  = 'בַּיִת'
    'w-he-lechem' = 'לֶחֶם'
    'w-he-avoda'  = 'עֲבוֹדָה'
    'w-he-tarbut' = 'תַּרְבּוּת'
    'w-hi-ghar'       = 'घर'
    'w-hi-khana'      = 'खाना'
    'w-hi-vidyalaya'  = 'विद्यालय'
    'w-hi-vyapar'     = 'व्यापार'
    'w-th-aharn'       = 'อาหาร'
    'w-th-ban'         = 'บ้าน'
    'w-th-thongthiao'  = 'ท่องเที่ยว'
    'w-th-watthanatham'= 'วัฒนธรรม'
    'w-camon' = 'cảm ơn'
    'w-nuoc'  = 'nước'
}

$fixed = [System.Collections.Generic.List[string]]::new()
$noMatch = [System.Collections.Generic.List[string]]::new()

foreach ($wordId in $fixes.Keys) {
    $correctWord = $fixes[$wordId]
    $pattern = '(<article[^>]*\bdata-word-id="' + [regex]::Escape($wordId) + '"[^>]*>(?:(?!</article>)[\s\S])*?<h2 class="card-word">)([^<]*)(<\/h2>)'
    $replacement = '${1}' + $correctWord + '${3}'
    $newContent = [regex]::Replace($content, $pattern, $replacement)
    if ($newContent -ne $content) {
        $fixed.Add("$wordId -> $correctWord")
        $content = $newContent
    } else {
        $noMatch.Add($wordId)
    }
}

$outEncoding = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText($file, $content, $outEncoding)

$log = "C:\Users\andre\Desktop\lexi\fix_log.txt"
"=== FIXED ($($fixed.Count)) ===" | Out-File $log -Encoding UTF8
$fixed | ForEach-Object { "  Fixed: $_" | Out-File $log -Append -Encoding UTF8 }
"" | Out-File $log -Append -Encoding UTF8
"=== NO MATCH ($($noMatch.Count)) ===" | Out-File $log -Append -Encoding UTF8
$noMatch | ForEach-Object { "  NO MATCH: $_" | Out-File $log -Append -Encoding UTF8 }
"Done." | Out-File $log -Append -Encoding UTF8
Write-Host "Script complete. Results in fix_log.txt"
