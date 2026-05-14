<!DOCTYPE html>
<html lang="es">
@include('partials.site-head', [
  'title' => $title,
  'description' => $description,
  'robots' => $robots ?? 'index,follow',
  'extraHead' => $extraHead ?? null,
])
<body @if (!empty($bodyAttributes)) {!! $bodyAttributes !!} @endif>
<a href="#mainContent" class="skip-link">Saltar al contenido</a>

@include('partials.site-header', ['activeNav' => $activeNav ?? null])

@yield('content')

@isset($afterContent)
  {!! $afterContent !!}
@endisset

@if (($showFooter ?? true) !== false)
  @include('partials.site-footer')
@endif

@isset($afterFooter)
  {!! $afterFooter !!}
@endisset

@include('partials.site-scripts')

@yield('inlineScripts')
</body>
</html>