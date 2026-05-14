@extends('layouts.admin', ['title' => 'Lexi | Admin Categories', 'description' => 'Gestión de categorías en Lexi Admin.', 'sidebarNoteTitle' => 'Categories', 'sidebarNoteText' => 'Taxonomía temática para palabras, ejercicios y recorridos de aprendizaje.'])
@extends('layouts.admin', ['title' => 'Lexi | Admin Categories', 'description' => 'Visor real de categorías en Lexi Admin.', 'sidebarNoteTitle' => 'Categories', 'sidebarNoteText' => 'Taxonomía temática real y número de palabras enlazadas.'])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>Categories</h1>
		<p>Vista real de la tabla <strong>categories</strong> con recuento de palabras enlazadas.</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="admin.html"><i class="bi bi-arrow-left"></i> Panel</a>
	</div>
</section>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">Categories</span>
			<div class="admin-stat__icon"><i class="bi bi-tags"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['categories']) }}</p>
		<p class="admin-stat__label">Filas en <strong>categories</strong></p>
		<span class="admin-stat__meta"><i class="bi bi-diagram-3"></i> Taxonomía disponible</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">Linked</span>
			<div class="admin-stat__icon"><i class="bi bi-link-45deg"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['linked_words']) }}</p>
		<p class="admin-stat__label">Palabras categorizadas</p>
		<span class="admin-stat__meta"><i class="bi bi-check2-circle"></i> {{ number_format($stats['empty_categories']) }} vacías</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>Category catalog</h2>
				<p>Listado real de categorías con idioma base y cobertura de palabras.</p>
			</div>
		</div>

		<form class="row g-3 mb-4" method="get" action="{{ route('admin-categories') }}">
			<div class="col-md-6">
				<label class="form-label" for="categorySearch">Buscar categoría</label>
				<input class="form-control" id="categorySearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="travel, work, culture...">
			</div>
			<div class="col-md-4">
				<label class="form-label" for="categoryLanguage">Idioma</label>
				<select class="form-select" id="categoryLanguage" name="language">
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
						<th>Idioma</th>
						<th>Descripción</th>
						<th>Words</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($categories as $category)
						@php
							$hasWords = $category->words_count > 0;
						@endphp
						<tr>
							<td>{{ $category->id }}</td>
							<td>{{ $category->name }}</td>
							<td>{{ strtoupper($category->language_code ?: '--') }}</td>
							<td>{{ $category->description ?: 'Sin descripción' }}</td>
							<td>{{ number_format($category->words_count) }}</td>
							<td><span class="admin-status {{ $hasWords ? 'admin-status--active' : 'admin-status--draft' }}">{{ $hasWords ? 'En uso' : 'Vacía' }}</span></td>
						</tr>
					@empty
						<tr>
							<td colspan="6">No hay categorías para esos filtros.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($categories->hasPages())
			<div class="d-flex justify-content-between align-items-center pt-3">
				<p class="mb-0 text-muted small">Mostrando {{ $categories->firstItem() }}-{{ $categories->lastItem() }} de {{ $categories->total() }} filas.</p>
				<div>{{ $categories->onEachSide(1)->links() }}</div>
			</div>
		@endif
	</div>
</section>
@endsection