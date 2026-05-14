@extends('layouts.admin', ['title' => 'Lexi | Admin Words', 'description' => 'Visor real de palabras y estructura de base de datos en Lexi Admin.', 'sidebarNoteTitle' => 'Words', 'sidebarNoteText' => 'Tabla real de palabras, conexión activa y control editorial.'])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>Words</h1>
		<p>Vista real de la tabla <strong>words</strong> con filtros y resumen de base de datos.</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="admin.html"><i class="bi bi-arrow-left"></i> Panel</a>
	</div>
</section>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--amber">Words</span>
			<div class="admin-stat__icon"><i class="bi bi-book"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['words']) }}</p>
		<p class="admin-stat__label">Filas en <strong>words</strong></p>
		<span class="admin-stat__meta"><i class="bi bi-database"></i> {{ $databaseInfo['driver'] }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">Translations</span>
			<div class="admin-stat__icon"><i class="bi bi-translate"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['translations']) }}</p>
		<p class="admin-stat__label">Filas en <strong>translations</strong></p>
		<span class="admin-stat__meta"><i class="bi bi-tags"></i> {{ number_format($stats['categories']) }} categorías</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">Usage</span>
			<div class="admin-stat__icon"><i class="bi bi-bookmark-check"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['saved_links']) }}</p>
		<p class="admin-stat__label">Filas en <strong>user_words</strong></p>
		<span class="admin-stat__meta"><i class="bi bi-hdd-stack"></i> {{ $databaseInfo['database'] ?: 'sin nombre detectado' }}</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>Database snapshot</h2>
				<p>Conexión activa y estado real del entorno de datos con el que está corriendo Lexi ahora mismo.</p>
			</div>
		</div>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<tbody>
					<tr>
						<th>Conexión</th>
						<td>{{ $databaseInfo['connection'] }}</td>
						<th>Driver</th>
						<td>{{ $databaseInfo['driver'] }}</td>
					</tr>
					<tr>
						<th>Base de datos</th>
						<td>{{ $databaseInfo['database'] ?: 'N/D' }}</td>
						<th>Objetivo</th>
						<td>{{ strtoupper($databaseInfo['target']) }}</td>
					</tr>
					<tr>
						<th>Estado</th>
						<td colspan="3">
							@if ($databaseInfo['is_mysql_like'])
								<span class="admin-status admin-status--active">MySQL activo</span>
							@else
								<span class="admin-status admin-status--review">SQLite local activa</span>
							@endif
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>Word inventory</h2>
				<p>Listado real de la tabla <strong>words</strong>, con primera traducción asociada y metadatos principales.</p>
			</div>
		</div>

		<form class="row g-3 mb-4" method="get" action="{{ route('admin-words') }}">
			<div class="col-md-6">
				<label class="form-label" for="wordSearch">Buscar palabra</label>
				<input class="form-control" id="wordSearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="heritage, maison, arbeit...">
			</div>
			<div class="col-md-4">
				<label class="form-label" for="languageFilter">Idioma</label>
				<select class="form-select" id="languageFilter" name="language">
					<option value="">Todos</option>
					@foreach ($languages as $language)
						<option value="{{ $language->code }}" @selected($filters['language'] === $language->code)>{{ $language->name }} ({{ strtoupper($language->code) }})</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-2 d-flex align-items-end gap-2">
				<button class="admin-btn admin-btn--primary w-100" type="submit"><i class="bi bi-search"></i> Filtrar</button>
			</div>
		</form>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Client key</th>
						<th>Word</th>
						<th>Idioma</th>
						<th>Traducción</th>
						<th>Categoría</th>
						<th>Nivel</th>
						<th>Estado</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($words as $word)
						@php
							$translation = $word->translations->first()?->targetWord?->text;
							$statusClass = $translation && $word->category && $word->cefr_level ? 'admin-status--active' : ($translation ? 'admin-status--review' : 'admin-status--draft');
							$statusLabel = $translation && $word->category && $word->cefr_level ? 'Revisada' : ($translation ? 'Pendiente' : 'Borrador');
						@endphp
						<tr>
							<td>{{ $word->id }}</td>
							<td>{{ $word->client_key ?: 'N/D' }}</td>
							<td>{{ $word->text }}</td>
							<td>{{ strtoupper($word->language_code) }}</td>
							<td>{{ $translation ?: 'Sin traducción' }}</td>
							<td>{{ $word->category?->name ?: 'Sin categoría' }}</td>
							<td>{{ $word->cefr_level ?: 'N/D' }}</td>
							<td><span class="admin-status {{ $statusClass }}">{{ $statusLabel }}</span></td>
						</tr>
					@empty
						<tr>
							<td colspan="8">No hay filas para esos filtros.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($words->hasPages())
			<div class="d-flex justify-content-between align-items-center pt-3">
				<p class="mb-0 text-muted small">Mostrando {{ $words->firstItem() }}-{{ $words->lastItem() }} de {{ $words->total() }} filas.</p>
				<div>{{ $words->onEachSide(1)->links() }}</div>
			</div>
		@endif
	</div>
</section>
@endsection