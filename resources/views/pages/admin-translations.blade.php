@extends('layouts.admin', ['title' => 'Lexi | Admin Translations', 'description' => 'Visor real de traducciones en Lexi Admin.', 'sidebarNoteTitle' => 'Translations', 'sidebarNoteText' => 'Relaciones origen-destino y notas de contexto reales.'])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>Translations</h1>
		<p>Vista real de la tabla <strong>translations</strong> con origen, destino y contexto.</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="admin.html"><i class="bi bi-arrow-left"></i> Panel</a>
	</div>
</section>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">Pairs</span>
			<div class="admin-stat__icon"><i class="bi bi-shuffle"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['pairs']) }}</p>
		<p class="admin-stat__label">Pares en <strong>translations</strong></p>
		<span class="admin-stat__meta"><i class="bi bi-link-45deg"></i> Relaciones origen-destino</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">Context</span>
			<div class="admin-stat__icon"><i class="bi bi-chat-square-text"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['with_context']) }}</p>
		<p class="admin-stat__label">Con nota de contexto</p>
		<span class="admin-stat__meta"><i class="bi bi-check2-circle"></i> {{ number_format($stats['without_context']) }} sin contexto</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>Translation map</h2>
				<p>Relaciones reales entre palabras del diccionario global con filtros rápidos.</p>
			</div>
		</div>

		<form class="row g-3 mb-4" method="get" action="{{ route('admin-translations') }}">
			<div class="col-md-4">
				<label class="form-label" for="translationSearch">Buscar</label>
				<input class="form-control" id="translationSearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="heritage, patrimonio, travel...">
			</div>
			<div class="col-md-3">
				<label class="form-label" for="sourceLanguage">Idioma origen</label>
				<select class="form-select" id="sourceLanguage" name="source_language">
					<option value="">Todos</option>
					@foreach ($languages as $language)
						<option value="{{ $language->code }}" @selected($filters['source_language'] === $language->code)>{{ $language->name }} ({{ strtoupper($language->code) }})</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-3">
				<label class="form-label" for="targetLanguage">Idioma destino</label>
				<select class="form-select" id="targetLanguage" name="target_language">
					<option value="">Todos</option>
					@foreach ($languages as $language)
						<option value="{{ $language->code }}" @selected($filters['target_language'] === $language->code)>{{ $language->name }} ({{ strtoupper($language->code) }})</option>
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
						<th>Source</th>
						<th>Target</th>
						<th>Context</th>
						<th>Categoría origen</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					@forelse ($translations as $translation)
						@php
							$source = $translation->sourceWord;
							$target = $translation->targetWord;
							$hasContext = filled($translation->context_note);
							$statusClass = $hasContext ? 'admin-status--active' : 'admin-status--review';
							$statusLabel = $hasContext ? 'Contextualizada' : 'Sin contexto';
						@endphp
						<tr>
							<td>{{ $translation->id }}</td>
							<td>{{ $source?->text ?: 'N/D' }} ({{ strtoupper($source?->language_code ?: '--') }})</td>
							<td>{{ $target?->text ?: 'N/D' }} ({{ strtoupper($target?->language_code ?: '--') }})</td>
							<td>{{ $translation->context_note ?: 'Sin nota' }}</td>
							<td>{{ $source?->category?->name ?: 'Sin categoría' }}</td>
							<td><span class="admin-status {{ $statusClass }}">{{ $statusLabel }}</span></td>
						</tr>
					@empty
						<tr>
							<td colspan="6">No hay traducciones para esos filtros.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($translations->hasPages())
			<div class="d-flex justify-content-between align-items-center pt-3">
				<p class="mb-0 text-muted small">Mostrando {{ $translations->firstItem() }}-{{ $translations->lastItem() }} de {{ $translations->total() }} filas.</p>
				<div>{{ $translations->onEachSide(1)->links() }}</div>
			</div>
		@endif
	</div>
</section>
@endsection