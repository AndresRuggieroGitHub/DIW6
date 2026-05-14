<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.admin', [
            'cards' => [
                ['title' => 'Users', 'description' => 'Cuentas, roles y estado de acceso.', 'count' => DB::table('users')->count() . ' usuarios', 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-people', 'href' => 'admin-users.html'],
                ['title' => 'Languages', 'description' => 'Idiomas activos y cobertura de catálogo.', 'count' => DB::table('words')->distinct()->count('language_code') . ' activos', 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-translate', 'href' => 'admin-languages.html'],
                ['title' => 'Words', 'description' => 'Diccionario global, categorías y nivel.', 'count' => DB::table('words')->count() . ' entradas', 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-book', 'href' => 'admin-words.html'],
                ['title' => 'Translations', 'description' => 'Pares léxicos y coherencia editorial.', 'count' => DB::table('translations')->count() . ' pares', 'status' => 'Revisión', 'statusClass' => 'admin-status--review', 'icon' => 'bi-arrow-left-right', 'href' => 'admin-translations.html'],
                ['title' => 'Collections', 'description' => 'Listas y seguimiento por usuario.', 'count' => DB::table('collections')->count() . ' colecciones', 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-collection', 'href' => 'admin-collections.html'],
                ['title' => 'Exercises', 'description' => 'Modos y sesiones registradas.', 'count' => DB::table('exercises')->count() . ' ejercicios', 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-grid', 'href' => 'admin-exercises.html'],
                ['title' => 'Categories', 'description' => 'Etiquetas de dominio y segmentación temática.', 'count' => DB::table('categories')->count() . ' categorías', 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-tags', 'href' => 'admin-categories.html'],
                ['title' => 'Roles', 'description' => 'Seguridad y niveles de acceso.', 'count' => DB::table('roles')->count() . ' roles', 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-shield-lock', 'href' => 'admin-roles.html'],
                ['title' => 'Billing', 'description' => 'Facturación y suscripciones premium.', 'count' => 'Sin integrar', 'status' => 'Pendiente', 'statusClass' => 'admin-status--draft', 'icon' => 'bi-credit-card', 'href' => 'admin-billing.html'],
                ['title' => 'Analytics', 'description' => 'Actividad y hábitos de estudio.', 'count' => DB::table('exercise_attempts')->count() . ' eventos', 'status' => 'Activo', 'statusClass' => 'admin-status--active', 'icon' => 'bi-graph-up-arrow', 'href' => 'admin-analytics.html'],
                ['title' => 'AI', 'description' => 'Prompts y revisión editorial asistida por IA.', 'count' => 'Sin integrar', 'status' => 'Pendiente', 'statusClass' => 'admin-status--draft', 'icon' => 'bi-robot', 'href' => 'admin-ai.html'],
            ],
        ]);
    }
}