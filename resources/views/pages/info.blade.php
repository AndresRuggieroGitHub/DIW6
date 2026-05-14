@extends('layouts.site', ['title' => 'Lexi | Información', 'description' => 'Cómo funciona Lexi: metodología, flujo de uso y modelo freemium.', 'robots' => 'index,follow'])

@section('content')
<main id="mainContent" class="info page-main">
  <h1>Cómo funciona Lexi</h1>
  <p>Lexi está pensado para ejercitar vocabulario real sin rutas largas obligatorias. Tú decides qué estudiar y a qué ritmo.</p>

  <section class="info-grid">
    <article class="info-card">
      <h2 class="h5">1. Crea tu biblioteca</h2>
      <p>Sube listas, copia y pega palabras o guárdalas manualmente desde la app.</p>
    </article>
    <article class="info-card">
      <h2 class="h5">2. Ejercicios por habilidad</h2>
      <p>Elige listening, reading, speaking, writing o ejercicios combinados.</p>
    </article>
    <article class="info-card">
      <h2 class="h5">3. Sigue tu progreso</h2>
      <p>Controla aciertos, repaso y avance para centrarte en lo que te cuesta.</p>
    </article>
  </section>

  <section class="container mt-4">
    <h2 class="h4">Modelo de acceso</h2>
    <p>Lexi usa un modelo freemium: la parte gratuita cubre el uso diario y premium amplía límites y funciones avanzadas sin anuncios intrusivos.</p>
  </section>
</main>
@endsection