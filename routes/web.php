<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$publicStaticPages = [
    'index' => 'index.html',
    'contacto' => 'contacto.html',
    'info' => 'info.html',
    'privacidad' => 'privacidad.html',
    'producto' => 'producto.html',
    'terminos' => 'terminos.html',
];

$protectedStaticPages = [
    'app' => 'app.html',
    'biblioteca' => 'biblioteca.html',
    'carrito' => 'carrito.html',
    'ejercicios' => 'ejercicios.html',
    'progreso' => 'progreso.html',
];

$adminStaticPages = [
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

foreach ($publicStaticPages as $slug => $file) {
    Route::get("/{$file}", function () use ($file) {
        return response()->file(base_path($file));
    })->name($slug);
}

Route::middleware('guest')->group(function () {
    Route::get('/login.html', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login.html', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/registro.html', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro.html', [AuthController::class, 'register'])->name('register.store');
    Route::get('/forgot-password.html', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password.html', [PasswordResetLinkController::class, 'store'])->name('password.email');
});

Route::middleware('auth')->group(function () use ($protectedStaticPages) {
    foreach ($protectedStaticPages as $slug => $file) {
        Route::get("/{$file}", function () use ($file) {
            return response()->file(base_path($file));
        })->name($slug);
    }

    Route::get('/perfil.html', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/perfil.html', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::delete('/perfil.html', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/api/library/state', [\App\Http\Controllers\LibraryController::class, 'state'])->name('library.state');
    Route::post('/api/library/words', [\App\Http\Controllers\LibraryController::class, 'storeWord'])->name('library.words.store');
    Route::delete('/api/library/words/{clientKey}', [\App\Http\Controllers\LibraryController::class, 'destroyWord'])->name('library.words.destroy');
    Route::post('/api/library/import', [\App\Http\Controllers\LibraryController::class, 'import'])->name('library.import');
});

Route::middleware('auth')->group(function () use ($adminStaticPages) {
    foreach ($adminStaticPages as $slug => $file) {
        Route::get("/{$file}", function (Request $request) use ($file) {
            abort_unless($request->user()?->isAdmin(), 403);

            return response()->file(base_path($file));
        })->name($slug);
    }
});
