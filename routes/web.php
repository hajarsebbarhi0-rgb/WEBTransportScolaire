<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TransportController;
use App\Http\Controllers\Admin\TrajetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminRapportController;
use App\Http\Controllers\Admin\HistoriqueController;


Route::get('/', function () {
    return view('welcome');
});

// =============================
// Tableau de bord (par défaut)
// =============================
Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('dashboard');

// =============================
// Routes profil utilisateur
// =============================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ATTENTION : Suppression de ce bloc car les routes trajets sont gérées ci-dessous.
    /*
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('trajets', TrajetController::class);
    });
    */
});

// =============================
// Routes ADMIN (avec auth et role:admin)
// =============================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Tableau de bord (accès via /admin/dashboard)
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // ROUTE DE RECHERCHE DÉPLACÉE ICI ET PLACÉE AVANT LA RESSOURCE USERS
        Route::get('/users/search', [UserController::class, 'search'])->name('users.search');

        // Gestion des utilisateurs
        Route::resource('users', UserController::class);

        // Gestion des transports
        Route::get('/transports/search', [TransportController::class, 'search'])->name('transports.search');
        Route::resource('transports', TransportController::class);

        // >>> Gestion des trajets (Recherche dynamique placée AVANT la ressource) <<<
        Route::get('/trajets/search', [TrajetController::class, 'search'])->name('trajets.search'); 
        Route::resource('trajets', TrajetController::class);
        
        // Rapports
        Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');
        Route::get('/historique', [HistoriqueController::class, 'index'])->name('historique.index');
         Route::get('/suivi', [AdminDashboardController::class, 'suivi'])->name('suivi');
        Route::get('/presences', [AdminDashboardController::class, 'presences'])
    ->name('presences');

    });

Route::get('/admin/rapports', [AdminRapportController::class, 'index'])->name('admin.rapports');
require __DIR__.'/auth.php';
Route::get('/password-reset-success', function () {
    return view('password-reset-success'); // Aucun préfixe de dossier nécessaire

});
 Route::post('/test-post', function () {
    return 'POST OK';
});

