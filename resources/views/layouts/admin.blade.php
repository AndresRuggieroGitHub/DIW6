<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{{ $description }}">
  <meta name="robots" content="{{ $robots ?? 'noindex,follow' }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title }}</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body class="admin-body">
  <div class="admin-shell">
    <aside class="admin-sidebar">
      <div class="admin-brand" aria-label="Lexi Admin">
        <span class="admin-brand__name">Lexi Admin</span>
        <span class="admin-brand__sub">Control panel</span>
      </div>
      @isset($sidebarNoteTitle)
        <div class="admin-sidebar-note">
          <strong>{{ $sidebarNoteTitle }}</strong>
          <span>{{ $sidebarNoteText }}</span>
        </div>
      @endisset
      <div class="admin-sidebar-footer">
        <a href="app.html"><i class="bi bi-house-door"></i> Abrir app</a>
        <form method="POST" action="{{ route('logout') }}" class="admin-sidebar-footer__form">
          @csrf
          <button type="submit" class="admin-sidebar-footer__button"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</button>
        </form>
      </div>
    </aside>

    <main id="adminMain" class="admin-main">
      @yield('content')
    </main>
  </div>

  @yield('inlineScripts')
</body>
</html>