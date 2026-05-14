@extends('layouts.admin', ['title' => 'Lexi | Admin', 'description' => 'Panel de administración de Lexi.'])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>Panel</h1>
		<p>Resumen general del panel de administración.</p>
	</div>
</section>

<section class="admin-overview-grid">
	@foreach ($cards as $card)
	<article class="admin-card">
		<div class="admin-card__inner admin-entity-card">
			<div class="admin-entity-card__icon"><i class="bi {{ $card['icon'] }}"></i></div>
			<div>
				<h3>{{ $card['title'] }}</h3>
				<p>{{ $card['description'] }}</p>
			</div>
			<div class="admin-entity-card__meta">
				<span>{{ $card['count'] }}</span>
				<span class="admin-status {{ $card['statusClass'] }}">{{ $card['status'] }}</span>
			</div>
			<div class="admin-entity-card__footer">
				<a class="admin-inline-link" href="{{ $card['href'] }}">Ver todo</a>
			</div>
		</div>
	</article>
	@endforeach
</section>
@endsection