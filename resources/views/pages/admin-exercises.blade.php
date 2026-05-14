@extends('layouts.admin', ['title' => 'Lexi | Admin Exercises', 'description' => 'Visor real de ejercicios e intentos en Lexi Admin.', 'sidebarNoteTitle' => 'Exercises', 'sidebarNoteText' => 'Inventario real de ejercicios y actividad registrada.'])

@section('content')
<section class="admin-page-head">
	<div>
		<h1>Exercises</h1>
		<p>Vista real de ejercicios creados, intentos y respuestas detalladas registradas.</p>
	</div>
	<div class="admin-page-actions">
		<a class="admin-btn admin-btn--ghost" href="admin.html"><i class="bi bi-arrow-left"></i> Panel</a>
	</div>
</section>

<section class="admin-stats">
	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">Publicados</span>
			<div class="admin-stat__icon"><i class="bi bi-ui-checks-grid"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['published']) }}</p>
		<p class="admin-stat__label">Ejercicios registrados</p>
		<span class="admin-stat__meta"><i class="bi bi-play-circle"></i> Tabla real de exercises</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--amber">Intentos</span>
			<div class="admin-stat__icon"><i class="bi bi-pencil-square"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['attempts']) }}</p>
		<p class="admin-stat__label">Intentos registrados</p>
		<span class="admin-stat__meta"><i class="bi bi-hourglass-split"></i> {{ number_format($stats['drafts']) }} borradores</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--green">Respuestas</span>
			<div class="admin-stat__icon"><i class="bi bi-list-check"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['answers']) }}</p>
		<p class="admin-stat__label">Respuestas detalladas</p>
		<span class="admin-stat__meta"><i class="bi bi-bullseye"></i> {{ $stats['accuracy_rate'] !== null ? $stats['accuracy_rate'] . '%' : 'Sin precisión aún' }}</span>
	</article>

	<article class="admin-card admin-stat">
		<div class="admin-stat__row">
			<span class="admin-chip admin-chip--violet">Plantillas</span>
			<div class="admin-stat__icon"><i class="bi bi-diagram-3"></i></div>
		</div>
		<p class="admin-stat__value">{{ number_format($stats['templates']) }}</p>
		<p class="admin-stat__label">Templates normalizadas</p>
		<span class="admin-stat__meta"><i class="bi bi-list-ol"></i> {{ number_format($stats['template_items']) }} items</span>
	</article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>Exercise catalog</h2>
				<p>Listado real de ejercicios disponibles para administración con uso y precisión agregada.</p>
			</div>
		</div>

		<form class="row g-3 mb-4" method="get" action="{{ route('admin-exercises') }}">
			<div class="col-md-10">
				<label class="form-label" for="exerciseSearch">Buscar ejercicio</label>
				<input class="form-control" id="exerciseSearch" type="search" name="q" value="{{ $filters['q'] }}" placeholder="reading, listening, writing...">
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
						<th>Title</th>
						<th>Type</th>
						<th>Source</th>
						<th>Status</th>
						<th>Attempts</th>
						<th>Answers</th>
						<th>Accuracy</th>
					</tr>
				</thead>
				<tbody>
					@forelse($exercises as $exercise)
						@php
							$accuracyRate = $exercise->answers_total > 0
								? (int) round(($exercise->correct_answers_total / $exercise->answers_total) * 100)
								: null;
						@endphp
						<tr>
							<td>{{ $exercise->id }}</td>
							<td>{{ $exercise->title ?: 'Sin título' }}</td>
							<td>{{ ucfirst($exercise->type) }}</td>
							<td>{{ $exercise->source ?: 'manual' }}</td>
							<td>
								<span class="admin-status {{ $exercise->attempts_total > 0 ? 'admin-status--active' : 'admin-status--review' }}">
									{{ $exercise->attempts_total > 0 ? 'Con uso' : 'Sin intentos' }}
								</span>
							</td>
							<td>{{ number_format($exercise->attempts_total) }}</td>
							<td>{{ number_format($exercise->answers_total) }} <span class="text-muted">/ {{ number_format($exercise->correct_answers_total) }} correctas</span></td>
							<td>{{ $accuracyRate !== null ? $accuracyRate . '%' : 'N/D' }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="8">No hay ejercicios para ese filtro.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>

		@if ($exercises->hasPages())
			<div class="d-flex justify-content-between align-items-center pt-3">
				<p class="mb-0 text-muted small">Mostrando {{ $exercises->firstItem() }}-{{ $exercises->lastItem() }} de {{ $exercises->total() }} filas.</p>
				<div>{{ $exercises->onEachSide(1)->links() }}</div>
			</div>
		@endif
	</div>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head">
			<div>
				<h2>Template inventory</h2>
				<p>Plantillas normalizadas ya presentes en la base: items, opciones e instancias generadas.</p>
			</div>
		</div>

		<div class="admin-table-wrap">
			<table class="admin-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Template</th>
						<th>Type</th>
						<th>Source</th>
						<th>Version</th>
						<th>Items</th>
						<th>Options</th>
						<th>Instances</th>
						<th>Author</th>
					</tr>
				</thead>
				<tbody>
					@forelse($templates as $template)
						@php
							$author = trim(($template->author_name ?? '') . ' ' . ($template->author_surname ?? '')) ?: 'Sistema';
						@endphp
						<tr>
							<td>{{ $template->id }}</td>
							<td>{{ $template->title ?: 'Sin título' }}</td>
							<td>{{ ucfirst($template->type) }}</td>
							<td>{{ $template->source ?: 'manual' }}</td>
							<td>v{{ $template->schema_version }}</td>
							<td>{{ number_format($template->items_total) }}</td>
							<td>{{ number_format($template->options_total) }}</td>
							<td>{{ number_format($template->instances_total) }}</td>
							<td>{{ $author }}</td>
						</tr>
					@empty
						<tr>
							<td colspan="9">No hay plantillas de ejercicios todavía.</td>
						</tr>
					@endforelse
				</tbody>
			</table>
		</div>
	</div>
</section>
@endsection