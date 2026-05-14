@php
  $activeNav = $activeNav ?? null;
@endphp

<header class="header">
  <div class="header-left">
    <div class="logo">
      <img src="icons/logo.svg" alt="Logo Lexi">
    </div>
    <button class="nav-toggle" type="button" aria-label="Abrir menu" aria-expanded="false" aria-controls="mainNav">
      <i class="bi bi-list"></i>
    </button>
    <nav class="nav-left" id="mainNav">
      <a href="app.html"@class(['active' => $activeNav === 'home'])>Inicio</a>
      <a href="biblioteca.html"@class(['active' => $activeNav === 'library'])>Biblioteca</a>
      <a href="ejercicios.html"@class(['active' => $activeNav === 'exercises'])>Ejercicios</a>
    </nav>
  </div>

  <div class="header-right">
    <button class="icon-btn" type="button" aria-label="Tu progreso" data-progress-trigger>
      <i class="bi bi-rocket-takeoff"></i>
    </button>
    <div class="lang-dropdown-wrap" data-lang-wrap>
      <button class="icon-btn" type="button" aria-label="Seleccionar idioma" data-lang-trigger aria-haspopup="true" aria-expanded="false">
        <img src="" alt="Idioma seleccionado" id="activeLangFlag">
      </button>
      <div class="lang-dropdown" id="langDropdown" hidden></div>
    </div>
    <script>(function(){var f={en:"icons/flags/united_kingdom_flag.svg",fr:"icons/flags/france_flag.svg",de:"icons/flags/germany_flag.svg",es:"icons/flags/spain_flag.svg",it:"icons/flags/italy_flag.svg",pt:"icons/flags/brazil_flag.svg",no:"icons/flags/norway_flag.svg",sv:"icons/flags/sweden_flag.svg",dk:"icons/flags/denmark_flag.svg",fi:"icons/flags/finland_flag.svg",ko:"icons/flags/south_korea_flag.svg",ja:"icons/flags/japan_flag.svg",zh:"icons/flags/china_flag.svg",ru:"icons/flags/russia_flag.svg",ua:"icons/flags/ukraine_flag.svg",bg:"icons/flags/bulgaria_flag.svg",ro:"icons/flags/romania_flag.svg",cs:"icons/flags/czech_republic_flag.svg",sk:"icons/flags/slovakia_flag.svg",hu:"icons/flags/hungary_flag.svg",gr:"icons/flags/greece_flag.svg",ar:"icons/flags/saudi_arabia_flag.svg",vi:"icons/flags/vietnam_flag.svg",pl:"icons/flags/poland_flag.svg",tr:"icons/flags/turkey_flag.svg",id:"icons/flags/indonesia_flag.svg",he:"icons/flags/israel_flag.svg",hi:"icons/flags/india_flag.svg",th:"icons/flags/thailand_flag.svg",nl:"icons/flags/netherlands_flag.svg"};var img=document.getElementById("activeLangFlag");if(img)img.src=f[localStorage.getItem("lexiLang")||"en"]||f.en;})();</script>
    <a href="perfil.html" class="icon-btn" aria-label="Perfil">
      <img src="icons/profile.svg" alt="Perfil">
    </a>
    <button class="icon-btn utility-trigger" type="button" data-utility-trigger aria-label="Abrir menu secundario" aria-expanded="false">
      <img src="icons/menu.svg" alt="" aria-hidden="true">
    </button>
  </div>
</header>