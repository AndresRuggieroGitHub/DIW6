@extends('layouts.admin', ['title' => 'Lexi | Admin Billing', 'description' => 'Estado comercial real en Lexi Admin.', 'sidebarNoteTitle' => 'Billing', 'sidebarNoteText' => 'Planes y suscripciones persistidos. La pasarela de cobro sigue pendiente, pero el modelo comercial base ya existe.'])

@section('content')
<section class="admin-page-head"><div><h1>Billing</h1><p>Base comercial real: planes persistidos, suscripciones activas y métrica mensual estimada.</p></div><div class="admin-page-actions"><a class="admin-btn admin-btn--ghost" href="admin.html"><i class="bi bi-arrow-left"></i> Panel</a></div></section>

<section class="admin-stats">
	<article class="admin-card admin-stat"><div class="admin-stat__row"><span class="admin-chip admin-chip--green">Planes</span><div class="admin-stat__icon"><i class="bi bi-gem"></i></div></div><p class="admin-stat__value">{{ $stats['plans'] }}</p><p class="admin-stat__label">planes activos</p><span class="admin-stat__meta"><i class="bi bi-check2-circle"></i> Catálogo comercial base</span></article>
	<article class="admin-card admin-stat"><div class="admin-stat__row"><span class="admin-chip admin-chip--violet">Suscripciones</span><div class="admin-stat__icon"><i class="bi bi-people"></i></div></div><p class="admin-stat__value">{{ $stats['active_subscriptions'] }}</p><p class="admin-stat__label">suscripciones activas</p><span class="admin-stat__meta"><i class="bi bi-stars"></i> {{ $stats['paid_subscriptions'] }} de pago</span></article>
	<article class="admin-card admin-stat"><div class="admin-stat__row"><span class="admin-chip admin-chip--amber">MRR</span><div class="admin-stat__icon"><i class="bi bi-cash-stack"></i></div></div><p class="admin-stat__value">{{ number_format($stats['mrr_cents'] / 100, 2, ',', '.') }} EUR</p><p class="admin-stat__label">ingreso mensual estimado</p><span class="admin-stat__meta"><i class="bi bi-lightning-charge"></i> {{ $stats['trialing_subscriptions'] }} en trial</span></article>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head"><div><h2>Planes</h2><p>Oferta comercial persistida en base de datos.</p></div><span class="admin-chip admin-chip--green">Real</span></div>
		<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Plan</th><th>Código</th><th>Precio</th><th>Intervalo</th><th>Suscriptores</th><th>Estado</th></tr></thead><tbody>@forelse ($plans as $plan)<tr><td>{{ $plan->name }}</td><td>{{ $plan->code }}</td><td>{{ number_format($plan->price_cents / 100, 2, ',', '.') }} {{ $plan->currency }}</td><td>{{ $plan->billing_interval }}</td><td>{{ $plan->subscribers }}</td><td><span class="admin-status {{ $plan->is_active ? 'admin-status--active' : 'admin-status--draft' }}">{{ $plan->is_active ? 'Activo' : 'Inactivo' }}</span></td></tr>@empty<tr><td colspan="6">Aún no hay planes definidos.</td></tr>@endforelse</tbody></table></div>
	</div>
</section>

<section class="admin-card">
	<div class="admin-card__inner">
		<div class="admin-card__head"><div><h2>Suscripciones recientes</h2><p>Últimos registros comerciales del sistema.</p></div><span class="admin-chip admin-chip--violet">Persistencia</span></div>
		<div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Usuario</th><th>Email</th><th>Plan</th><th>Estado</th><th>Renovación</th><th>Proveedor</th></tr></thead><tbody>@forelse ($subscriptions as $subscription)<tr><td>{{ trim(($subscription->name ?? '') . ' ' . ($subscription->surname ?? '')) ?: 'Sin nombre' }}</td><td>{{ $subscription->email }}</td><td>{{ $subscription->plan_name }}</td><td><span class="admin-status {{ in_array($subscription->status, ['active', 'trialing'], true) ? 'admin-status--active' : 'admin-status--draft' }}">{{ $subscription->status }}</span></td><td>{{ $subscription->renews_at ? \Illuminate\Support\Carbon::parse($subscription->renews_at)->format('d/m/Y') : 'Sin fecha' }}</td><td>{{ $subscription->provider ?: 'manual' }}</td></tr>@empty<tr><td colspan="6">Aún no hay suscripciones registradas.</td></tr>@endforelse</tbody></table></div>
	</div>
</section>

<section class="admin-card"><div class="admin-card__inner"><div class="admin-card__head"><div><h2>Siguiente deuda técnica</h2><p>La estructura comercial ya existe; ahora falta conectar la pasarela y los eventos de pago.</p></div><span class="admin-chip admin-chip--amber">Pendiente</span></div><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Área</th><th>Necesario</th><th>Estado</th></tr></thead><tbody><tr><td>Checkout</td><td>Integración con Stripe o equivalente para alta y renovación</td><td><span class="admin-status admin-status--draft">Pendiente</span></td></tr><tr><td>Webhooks</td><td>Eventos de cobro, renovación, fallo y cancelación</td><td><span class="admin-status admin-status--draft">Pendiente</span></td></tr><tr><td>Histórico</td><td>Ledger de pagos e invoices para auditoría</td><td><span class="admin-status admin-status--draft">Pendiente</span></td></tr></tbody></table></div></div></section>
@endsection