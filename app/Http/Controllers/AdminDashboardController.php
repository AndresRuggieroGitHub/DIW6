<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.admin', [
            'cards' => [
                ['title' => 'Users', 'description' => 'Cuentas, roles y estado de acceso.', 'count' => $this->countLabel('users', 'usuarios'), 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-people', 'href' => 'admin-users.html'],
                ['title' => 'Languages', 'description' => 'Idiomas activos y cobertura de catálogo.', 'count' => $this->countDistinctLabel('words', 'language_code', 'activos'), 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-translate', 'href' => 'admin-languages.html'],
                ['title' => 'Words', 'description' => 'Diccionario global, categorías y nivel.', 'count' => $this->countLabel('words', 'entradas'), 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-book', 'href' => 'admin-words.html'],
                ['title' => 'Translations', 'description' => 'Pares léxicos y coherencia editorial.', 'count' => $this->countLabel('translations', 'pares'), 'status' => 'Revisión', 'statusClass' => 'admin-status--review', 'icon' => 'bi-arrow-left-right', 'href' => 'admin-translations.html'],
                ['title' => 'Collections', 'description' => 'Listas y seguimiento por usuario.', 'count' => $this->countLabel('collections', 'colecciones'), 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-collection', 'href' => 'admin-collections.html'],
                ['title' => 'Exercises', 'description' => 'Modos y sesiones registradas.', 'count' => $this->countLabel('exercises', 'ejercicios'), 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-grid', 'href' => 'admin-exercises.html'],
                ['title' => 'Categories', 'description' => 'Etiquetas de dominio y segmentación temática.', 'count' => $this->countLabel('categories', 'categorías'), 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-tags', 'href' => 'admin-categories.html'],
                ['title' => 'Roles', 'description' => 'Seguridad y niveles de acceso.', 'count' => $this->countLabel('roles', 'roles'), 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-shield-lock', 'href' => 'admin-roles.html'],
                ['title' => 'Billing', 'description' => 'Facturación y suscripciones premium.', 'count' => $this->countLabel('subscriptions', 'suscripciones'), 'status' => Schema::hasTable('subscriptions') ? 'Activo' : 'Pendiente', 'statusClass' => Schema::hasTable('subscriptions') ? 'admin-status--active' : 'admin-status--draft', 'icon' => 'bi-credit-card', 'href' => 'admin-billing.html'],
                ['title' => 'Analytics', 'description' => 'Actividad y hábitos de estudio.', 'count' => $this->countLabel('exercise_attempts', 'eventos'), 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-graph-up-arrow', 'href' => 'admin-analytics.html'],
                ['title' => 'AI', 'description' => 'Prompts y revisión editorial asistida por IA.', 'count' => $this->countLabel('ai_generations', 'generaciones'), 'status' => Schema::hasTable('ai_generations') ? 'Activo' : 'Pendiente', 'statusClass' => Schema::hasTable('ai_generations') ? 'admin-status--active' : 'admin-status--draft', 'icon' => 'bi-robot', 'href' => 'admin-ai.html'],
            ],
        ]);
    }

    private function countLabel(string $table, string $suffix): string
    {
        if (!Schema::hasTable($table)) {
            return 'Pendiente';
        }

        return DB::table($table)->count() . ' ' . $suffix;
    }

    private function countDistinctLabel(string $table, string $column, string $suffix): string
    {
        if (!Schema::hasTable($table)) {
            return 'Pendiente';
        }

        return DB::table($table)->distinct()->count($column) . ' ' . $suffix;
    }
}