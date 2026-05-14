@extends('layouts.site', ['title' => 'Lexi | Tu progreso', 'description' => 'Tu progreso de aprendizaje en Lexi.', 'robots' => 'noindex', 'extraHead' => '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>'])

@section('content')
<main id="mainContent" class="profile-main">

  <div class="progress-page-hero">
    <div>
      <h1 class="progress-page-title">Tu progreso</h1>
    </div>
  </div>

  <div class="progress-streak-card">
    <div class="progress-streak-left">
      <span class="progress-streak-fire" id="streakFire">🔥🌱</span>
      <div>
        <div class="progress-streak-num" id="streakNum">0</div>
        <div class="progress-streak-label">días de racha</div>
      </div>
    </div>
    <div class="progress-streak-right">
      <p class="progress-streak-msg" id="streakMsg">Completa un ejercicio hoy para empezar tu racha.</p>
    </div>
  </div>

  <div class="profile-stats-row">
    <div class="profile-stat-card">
      <span class="profile-stat-icon" style="--sc:#4f8ef7"><i class="bi bi-book-half"></i></span>
      <span class="profile-stat-num" id="statWords">0</span>
      <span class="profile-stat-label">palabras guardadas</span>
    </div>
    <div class="profile-stat-card">
      <span class="profile-stat-icon" style="--sc:#f9b233"><i class="bi bi-trophy-fill"></i></span>
      <span class="profile-stat-num" id="statExDone">0</span>
      <span class="profile-stat-label">ejercicios completados</span>
    </div>
    <div class="profile-stat-card">
      <span class="profile-stat-icon" style="--sc:#2dc98b"><i class="bi bi-translate"></i></span>
      <span class="profile-stat-num" id="statLang">?</span>
      <span class="profile-stat-label">idioma activo</span>
    </div>
  </div>

  <section class="profile-section">
    <h2 class="profile-section-title">Nivel estimado</h2>
    <div class="profile-level-card">
      <div class="profile-level-badge" id="levelBadge">A1</div>
      <div class="profile-level-info">
        <p class="profile-level-desc" id="levelDesc">Estás empezando. ¡Cada palabra cuenta!</p>
        <div class="profile-level-bar-wrap">
          <div class="profile-level-bar" id="levelBar" style="width:0%"></div>
        </div>
        <p class="profile-level-next" id="levelNext">0 / 10 palabras para A2</p>
      </div>
    </div>
    <p class="progress-cefr-note">El nivel se estima según las palabras guardadas en tu biblioteca.</p>
  </section>

  <section class="profile-section">
    <h2 class="profile-section-title">Vocabulario &amp; sesiones</h2>
    <div class="progress-charts-grid">
      <div class="progress-chart-card">
        <p class="progress-chart-title"><i class="bi bi-pie-chart-fill"></i> Palabras por idioma</p>
        <div class="progress-chart-wrap">
          <canvas id="langDonutChart"></canvas>
        </div>
        <p class="progress-chart-empty" id="donutEmpty" hidden>Aún no hay palabras guardadas.</p>
      </div>
      <div class="progress-chart-card">
        <p class="progress-chart-title"><i class="bi bi-bar-chart-fill"></i> Sesiones de ejercicios</p>
        <div class="progress-chart-wrap">
          <canvas id="modeBarChart"></canvas>
        </div>
        <p class="progress-chart-empty" id="barEmpty" hidden>Aún no hay sesiones registradas.</p>
      </div>
    </div>
  </section>

  <section class="profile-section">
    <div class="profile-section-header">
      <h2 class="profile-section-title">Sesiones de ejercicios</h2>
      <a href="ejercicios.html" class="profile-section-link">Ejercitar →</a>
    </div>
    <div class="profile-modes-grid">
      <div class="profile-mode-item">
        <span class="profile-mode-icon" style="--mc:#4f8ef7"><i class="bi bi-book-half"></i></span>
        <span class="profile-mode-count" id="modeReading">0</span>
        <span class="profile-mode-label">Lectura</span>
      </div>
      <div class="profile-mode-item">
        <span class="profile-mode-icon" style="--mc:#f76b4f"><i class="bi bi-headphones"></i></span>
        <span class="profile-mode-count" id="modeListening">0</span>
        <span class="profile-mode-label">Escucha</span>
      </div>
      <div class="profile-mode-item">
        <span class="profile-mode-icon" style="--mc:#2dc98b"><i class="bi bi-mic-fill"></i></span>
        <span class="profile-mode-count" id="modeSpeaking">0</span>
        <span class="profile-mode-label">Habla</span>
      </div>
      <div class="profile-mode-item">
        <span class="profile-mode-icon" style="--mc:#a855f7"><i class="bi bi-pencil-fill"></i></span>
        <span class="profile-mode-count" id="modeWriting">0</span>
        <span class="profile-mode-label">Escritura</span>
      </div>
      <div class="profile-mode-item">
        <span class="profile-mode-icon" style="--mc:#f9b233"><i class="bi bi-shuffle"></i></span>
        <span class="profile-mode-count" id="modeMix">0</span>
        <span class="profile-mode-label">Combinado</span>
      </div>
    </div>
  </section>

  <section class="profile-section">
    <div class="profile-section-header">
      <h2 class="profile-section-title">Últimas palabras guardadas</h2>
      <a href="biblioteca.html" class="profile-section-link">Ver todas →</a>
    </div>
    <ul class="profile-words-list" id="recentWordsList">
      <li class="profile-words-empty">Aún no has guardado ninguna palabra. <a href="biblioteca.html">Explorar biblioteca</a></li>
    </ul>
  </section>

</main>
@endsection