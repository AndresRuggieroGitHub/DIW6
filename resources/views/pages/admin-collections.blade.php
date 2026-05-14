@extends('layouts.admin', ['title' => 'Lexi | Admin Collections', 'description' => 'Visor real de colecciones en Lexi Admin.', 'sidebarNoteTitle' => 'Collections', 'sidebarNoteText' => 'Listas reales por usuario, idioma y enlace con palabras.'])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>Collections</h1>
		<p>Vista real de <strong>collections</strong> y de la tabla puente <strong>collection_words</strong>.</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="admin.html"><i class="bi bi-arrow-left"></i> Panel</a>
	</div>
</section>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">Collections</span>
			<div class="admin-stat__icon"><i class="bi bi-collection"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['collections']) }}</p>
		<p class="admin-stat__label">Filas en <strong>collections</strong></p>
		<span class="admin-stat__meta"><i class="bi bi-bookmark"></i> {{ number_format($stats['default_collections']) }} por defecto</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">Linked</span>
			<div class="admin-stat__icon"><i class="bi bi-link"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['linked_words']) }}</p>
		<p class="admin-stat__label">Filas en <strong>collection_words</strong></p>
		<span class="admin-stat__meta"><i class="bi bi-diagram-3"></i> Relaciones colección-palabra</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>Collection library</h2>
				<p>Listado real de listas por usuario con su idioma y cobertura de palabras.</p>
			</div>
		</div>

		<form class="row g-3 mb-4" method="get" action="{{ route('admin-collections') }}">
			<div class="col-md-6">
				<label class="form-label" for="collectionSearch">Buscar colección</label>
				<input class="form-control" id="collectionSearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="Guardado, viaje, francés...">
			</div>
			<div class="col-md-4">
				<label class="form-label" for="collectionLanguage">Idioma</label>
				<select class="form-select" id="collectionLanguage" name="language">
					<option value="">Todos</option>
					@foreach ($languages as $language)
						<option value="{{ $language->code }}" @selected($filters['language'] === $language->code)>{{ $language->name }} ({{ strtoupper($language->code) }})</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-2 d-flex align-items-end">
				<button class="admin-btn admin-btn--primary w-100" type="submit"><i class="bi bi-search"></i> Filtrar</button>
			</div>
		</form>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Name</th>
						<th>Owner</th>
						<th>Language</th>
						<th>Words</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($collections as $collection)
						@php
							$owner = trim(($collection->user?->name ?? '') . ' ' . ($collection->user?->surname ?? '')) ?: 'Sin usuario';
						@endphp
						<tr>
							<td>{{ $collection->id }}</td>
							<td>{{ $collection->name }}</td>
							<td>{{ $owner }}</td>
							<td>{{ strtoupper($collection->language_code) }}</td>
							<td>{{ number_format($collection->words_count) }}</td>
							<td><span class="admin-status {{ $collection->is_default ? 'admin-status--active' : 'admin-status--review' }}">{{ $collection->is_default ? 'Por defecto' : 'Personal' }}</span></td>
						</tr>
					@empty
						<tr>
							<td colspan="6">No hay colecciones para esos filtros.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($collections->hasPages())
			<div class="d-flex justify-content-between align-items-center pt-3">
				<p class="mb-0 text-muted small">Mostrando {{ $collections->firstItem() }}-{{ $collections->lastItem() }} de {{ $collections->total() }} filas.</p>
				<div>{{ $collections->onEachSide(1)->links() }}</div>
			</div>
		@endif
	</div>
</section>
@endsection