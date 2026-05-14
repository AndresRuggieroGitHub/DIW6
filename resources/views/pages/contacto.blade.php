@extends('layouts.site', ['title' => 'Lexi | Contacto', 'description' => 'Página de contacto de Lexi.', 'robots' => 'index,follow'])

@section('content')
<main id="mainContent" class="page-main container section-space">
  <h1 class="mb-3">Centro de soporte</h1>
  <p class="text-muted mb-4">Si tienes dudas de uso, facturación o gestión académica, contacta con el equipo de Lexi.</p>

  <div class="contact-grid">
    <section>
      <h2 class="h4">Enviar consulta</h2>
      <form class="contact-form" action="#" method="post">
        <div class="mb-3">
          <label for="nombre" class="form-label">Nombre</label>
          <input id="nombre" name="nombre" class="form-control" type="text" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input id="email" name="email" class="form-control" type="email" required>
        </div>
        <div class="mb-3">
          <label for="motivo" class="form-label">Motivo</label>
          <select id="motivo" name="motivo" class="form-select" required>
            <option value="">Selecciona una opción</option>
            <option value="tecnico">Soporte técnico</option>
            <option value="premium">Facturación premium</option>
            <option value="profesor">Cuenta de profesor</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="mensaje" class="form-label">Mensaje</label>
          <textarea id="mensaje" name="mensaje" class="form-control" rows="4" required></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Enviar</button>
      </form>
    </section>

    <section>
      <h2 class="h4">Canales disponibles</h2>
      <div class="support-list">
        <article class="support-item">
          <h3 class="h6 mb-1">Soporte técnico</h3>
          <p class="mb-1">Incidencias con cuenta, biblioteca y ejercicios.</p>
          <p class="mb-0">soporte@lexi.app</p>
        </article>
        <article class="support-item">
          <h3 class="h6 mb-1">Facturación premium</h3>
          <p class="mb-1">Pagos, renovaciones y cancelaciones.</p>
          <p class="mb-0">billing@lexi.app</p>
        </article>
        <article class="support-item">
          <h3 class="h6 mb-1">Docentes y centros</h3>
          <p class="mb-1">Activacion de perfil profesor y grupos.</p>
          <p class="mb-0">teachers@lexi.app</p>
        </article>
      </div>
    </section>
  </div>
</main>
@endsection