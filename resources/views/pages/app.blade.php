@extends('layouts.site', ['title' => 'Lexi | Inicio', 'description' => 'Lexi, prototipo de aprendizaje de idiomas centrado en vocabulario, ejercicios y progreso, con MVP priorizado para español e inglés.', 'robots' => 'noindex,follow', 'activeNav' => 'home', 'afterFooter' => '<button id="btnSubir" type="button" aria-label="Subir arriba">&uarr;</button>'])

@section('content')
<main id="mainContent" class="hero">
  <div class="hero-text scroll-animado">
    <h1>Aprende idiomas a tu ritmo</h1>
    <p class="hero-sub">Tu vocabulario, tus ejercicios, tu progreso.</p>
  </div>
</main>

<div class="hero-divider" aria-hidden="true"></div>

<section class="container section-space home-cards">
  <h2>¿Qué puedes hacer con Lexi?</h2>
  <div class="home-card-grid">
    <article class="home-card">
      <div class="home-card-icon"><i class="bi bi-journals"></i></div>
      <h3 class="h5">Tu propio vocabulario</h3>
      <p>Sube tus palabras, organízalas por colecciones y repásalas cuando quieras.</p>
      <a class="btn btn-primary" href="biblioteca.html">Ir a mi biblioteca</a>
    </article>
    <article class="home-card">
      <div class="home-card-icon"><i class="bi bi-lightning-charge"></i></div>
      <h3 class="h5">Ejercicios personalizados</h3>
      <p>Ejercicios de vocabulario adaptados a lo que has guardado.</p>
      <a class="btn btn-primary" href="ejercicios.html">Elegir ejercicio</a>
    </article>
    <article class="home-card">
      <div class="home-card-icon"><i class="bi bi-graph-up-arrow"></i></div>
      <h3 class="h5">Análisis de progreso</h3>
      <p>Sigue tu evolución mediante gráficas y estadísticas claras.</p>
      <a class="btn btn-primary" href="progreso.html">Ver tu progreso</a>
    </article>
  </div>
</section>

<section class="container section-space how-it-works">
  <h2>¿Cómo funciona?</h2>
  <div class="how-steps">
    <div class="how-step">
      <div class="how-step-num">1</div>
      <h3>Busca palabras</h3>
      <p>Explora el catálogo y encuentra las palabras que necesitas o sube tu propio vocabulario.</p>
    </div>
    <div class="how-step-divider" aria-hidden="true"></div>
    <div class="how-step">
      <div class="how-step-num">2</div>
      <h3>Guárdalas</h3>
      <p>Organízalas en colecciones personalizadas según tus temas o niveles.</p>
    </div>
    <div class="how-step-divider" aria-hidden="true"></div>
    <div class="how-step">
      <div class="how-step-num">3</div>
      <h3>Ejercicios</h3>
      <p>Repasa con ejercicios adaptados justo a lo que has guardado.</p>
    </div>
  </div>
</section>

<section class="container home-premium-section">
  <article class="home-card home-premium-card">
    <div class="home-card-icon"><i class="bi bi-gem"></i></div>
    <h3 class="h5">Desbloquear premium</h3>
    <p>Accede a más ejercicios personalizados y ejercicios guiados por profesores nativos.</p>
    <a class="btn btn-success" href="producto.html">Ver planes premium</a>
  </article>
  <div class="home-premium-image">
    <img src="images/premium_illustration.png" alt="Ilustración aprendizaje premium">
  </div>
</section>

<div class="home-cta-banner">
  <p>¡Listo para empezar!</p>
  <a class="btn btn-warning" href="biblioteca.html">Explorar catálogo</a>
</div>
@endsection