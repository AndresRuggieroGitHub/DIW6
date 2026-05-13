<?php

use Illuminate\Support\Facades\Route;

$staticPages = [
    'index' => 'index.html',
    'app' => 'app.html',
    'biblioteca' => 'biblioteca.html',
    'carrito' => 'carrito.html',
    'contacto' => 'contacto.html',
    'ejercicios' => 'ejercicios.html',
    'forgot-password' => 'forgot-password.html',
    'info' => 'info.html',
    'login' => 'login.html',
    'perfil' => 'perfil.html',
    'privacidad' => 'privacidad.html',
    'producto' => 'producto.html',
    'progreso' => 'progreso.html',
    'registro' => 'registro.html',
    'terminos' => 'terminos.html',
    'admin' => 'admin.html',
    'admin-users' => 'admin-users.html',
    'admin-languages' => 'admin-languages.html',
    'admin-words' => 'admin-words.html',
    'admin-translations' => 'admin-translations.html',
    'admin-collections' => 'admin-collections.html',
    'admin-exercises' => 'admin-exercises.html',
    'admin-categories' => 'admin-categories.html',
    'admin-roles' => 'admin-roles.html',
    'admin-billing' => 'admin-billing.html',
    'admin-analytics' => 'admin-analytics.html',
    'admin-ai' => 'admin-ai.html',
];

Route::get('/', function () {
    return response()->file(base_path('index.html'));
});

foreach ($staticPages as $slug => $file) {
    Route::get("/{$file}", function () use ($file) {
        return response()->file(base_path($file));
    })->name($slug);
}
