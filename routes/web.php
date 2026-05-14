<?php

use App\Http\Controllers\AdminCategoriesController;
use App\Http\Controllers\AdminAnalyticsController;
use App\Http\Controllers\AdminCollectionsController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminExercisesController;
use App\Http\Controllers\AdminLanguagesController;
use App\Http\Controllers\AdminRolesController;
use App\Http\Controllers\AdminWordsController;
use App\Http\Controllers\AdminTranslationsController;
use App\Http\Controllers\AdminUsersController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressController;
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
    'admin-billing' => 'admin-billing.html',
    'admin-ai' => 'admin-ai.html',
];

$renderStaticPage = function (string $file, ?string $view = null) {
    $viewName = $view ?? 'pages.' . str_replace(['/', '.html'], ['.', ''], $file);

    if (view()->exists($viewName)) {
        return response()->view($viewName);
    }

    return response()->file(base_path($file));
};

Route::get('/', function (Request $request) use ($renderStaticPage) {
    if ($request->user()) {
        return redirect('/app.html');
    }

    return $renderStaticPage('index.html');
});

foreach ($publicStaticPages as $slug => $file) {
    Route::get("/{$file}", function (Request $request) use ($file, $renderStaticPage) {
        if ($file === 'index.html' && $request->user()) {
            return redirect('/app.html');
        }

        return $renderStaticPage($file);
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

Route::middleware('auth')->group(function () use ($protectedStaticPages, $renderStaticPage) {
    foreach ($protectedStaticPages as $slug => $file) {
        Route::get("/{$file}", function () use ($file, $renderStaticPage) {
            return $renderStaticPage($file);
        })->name($slug);
    }

    Route::get('/perfil.html', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/perfil.html', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/api/session/state', [ProfileController::class, 'sessionState'])->name('session.state');
    Route::put('/api/session/active-language', [ProfileController::class, 'updateActiveLanguage'])->name('session.active-language');
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::delete('/perfil.html', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/api/library/state', [\App\Http\Controllers\LibraryController::class, 'state'])->name('library.state');
    Route::get('/api/progress/state', [ProgressController::class, 'state'])->name('progress.state');
    Route::post('/api/exercise-attempts', [ExerciseController::class, 'storeAttempt'])->name('exercise-attempts.store');
    Route::post('/api/library/words', [\App\Http\Controllers\LibraryController::class, 'storeWord'])->name('library.words.store');
    Route::delete('/api/library/words', [\App\Http\Controllers\LibraryController::class, 'clearLibrary'])->name('library.words.clear');
    Route::delete('/api/library/words/{clientKey}', [\App\Http\Controllers\LibraryController::class, 'destroyWord'])->name('library.words.destroy');
    Route::post('/api/library/import', [\App\Http\Controllers\LibraryController::class, 'import'])->name('library.import');
    Route::post('/api/library/collections', [\App\Http\Controllers\LibraryController::class, 'storeCollection'])->name('library.collections.store');
    Route::patch('/api/library/collections/{collection}', [\App\Http\Controllers\LibraryController::class, 'updateCollection'])->name('library.collections.update');
    Route::delete('/api/library/collections/{collection}', [\App\Http\Controllers\LibraryController::class, 'destroyCollection'])->name('library.collections.destroy');
    Route::delete('/api/library/collections/{collection}/words', [\App\Http\Controllers\LibraryController::class, 'clearCollection'])->name('library.collections.clear');
    Route::post('/api/library/collections/{collection}/toggle-word', [\App\Http\Controllers\LibraryController::class, 'toggleCollectionWord'])->name('library.collections.toggle-word');
});

Route::middleware('auth')->group(function () use ($adminStaticPages, $renderStaticPage) {
    Route::get('/admin-analytics', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminAnalyticsController::class)->index($request);
    });

    Route::get('/admin-analytics.html', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminAnalyticsController::class)->index($request);
    })->name('admin-analytics');

    Route::get('/admin', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminDashboardController::class)->index($request);
    });

    Route::get('/admin.html', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminDashboardController::class)->index($request);
    })->name('admin');

    Route::get('/admin-words', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminWordsController::class)->index($request);
    });

    Route::get('/admin-words.html', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminWordsController::class)->index($request);
    })->name('admin-words');

    Route::get('/admin-translations', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminTranslationsController::class)->index($request);
    });

    Route::get('/admin-translations.html', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminTranslationsController::class)->index($request);
    })->name('admin-translations');

    Route::get('/admin-categories', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminCategoriesController::class)->index($request);
    });

    Route::get('/admin-categories.html', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminCategoriesController::class)->index($request);
    })->name('admin-categories');

    Route::get('/admin-collections', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminCollectionsController::class)->index($request);
    });

    Route::get('/admin-collections.html', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminCollectionsController::class)->index($request);
    })->name('admin-collections');

    Route::get('/admin-users', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminUsersController::class)->index($request);
    });

    Route::get('/admin-users.html', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminUsersController::class)->index($request);
    })->name('admin-users');

    Route::get('/admin-languages', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminLanguagesController::class)->index($request);
    });

    Route::get('/admin-languages.html', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminLanguagesController::class)->index($request);
    })->name('admin-languages');

    Route::get('/admin-roles', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminRolesController::class)->index($request);
    });

    Route::get('/admin-roles.html', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminRolesController::class)->index($request);
    })->name('admin-roles');

    Route::get('/admin-exercises', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminExercisesController::class)->index($request);
    });

    Route::get('/admin-exercises.html', function (Request $request) {
        abort_unless($request->user()?->isAdmin(), 403);

        return app(AdminExercisesController::class)->index($request);
    })->name('admin-exercises');

    foreach ($adminStaticPages as $slug => $file) {
        Route::get("/{$slug}", function (Request $request) use ($file, $renderStaticPage) {
            abort_unless($request->user()?->isAdmin(), 403);

            return $renderStaticPage($file);
        });

        Route::get("/{$file}", function (Request $request) use ($file, $renderStaticPage) {
            abort_unless($request->user()?->isAdmin(), 403);

            return $renderStaticPage($file);
        })->name($slug);
    }
});
