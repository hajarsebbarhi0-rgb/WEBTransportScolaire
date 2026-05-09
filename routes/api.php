<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EleveController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\TrajetController;
use App\Http\Controllers\Api\AbsenceController;
use App\Http\Controllers\Api\TrajetEleveController;
use App\Http\Controllers\Api\PresenceController;
use App\Http\Controllers\Api\RouteController;

// *************** AUTH ***************
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink($request->only('email'));
    return $status === Password::RESET_LINK_SENT
        ? response()->json(['message' => 'Email envoyé'])
        : response()->json(['message' => 'Email introuvable'], 404);
});

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'token'    => 'required',
        'password' => 'required|min:8|confirmed',
    ]);
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill(['password' => bcrypt($password)])->save();
        }
    );
    return $status === Password::PASSWORD_RESET
        ? response()->json(['message' => 'Mot de passe réinitialisé'])
        : response()->json(['message' => 'Token invalide'], 400);
});

// *************** ROUTES PROTÉGÉES ***************
Route::middleware('auth:sanctum')->group(function () {

    // AUTH
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());

    // PROFIL ÉLÈVE
    Route::get('/eleve/profile', [EleveController::class, 'profile']);
    Route::put('/eleve/profile', [EleveController::class, 'updateProfile']);
    Route::post('/eleve/photo', [EleveController::class, 'uploadPhoto']);
    Route::post('/eleve/set-home-arret', [EleveController::class, 'setHomeLocation']);
    Route::post('/eleve/absence', [AbsenceController::class, 'signalerAbsence']);

    // ASSOCIER TRAJET
    Route::post('/associer-trajet', [TrajetEleveController::class, 'associerTrajet']);

    // TRAJETS & ARRÊTS
    Route::get('/chauffeur/trajets', [TrajetController::class, 'getTrajetsChauffeur']);
    Route::apiResource('trajets', TrajetController::class)->only(['index', 'show']);
    
    Route::get('/trajets/{id}/arrets', [EleveController::class, 'arrets']);
    Route::post('/trajets/stop', [LocationController::class, 'stopTrajet']);
    Route::post('/trajets/{trajet}/route-next', [RouteController::class, 'routeToNextArret']);
    Route::post('/trajets/{trajet}/reorder-arrets', [RouteController::class, 'reorderArrets']);

    // LOCALISATION BUS
    Route::post('/send-location', [LocationController::class, 'store']);
    Route::get('/bus-location/{trajetId}', [LocationController::class, 'getLocation']);
    Route::get('/eleve/bus-location', [LocationController::class, 'getLocationForEleve']);

    // ABSENCES & PRÉSENCES
    Route::post('/chauffeur/absence', [AbsenceController::class, 'absenceParChauffeur']);
    Route::post('/presences', [PresenceController::class, 'store']);

    // ✅ ÉLÈVES — spécifiques AVANT générique
    Route::get('/chauffeur/eleves/tous', [EleveController::class, 'elevesChauffeur']);
    Route::get('/chauffeur/eleves/presents', [EleveController::class, 'elevesPresentsParTrajet']);
    Route::get('/chauffeur/arrets/presents', [EleveController::class, 'arretsPresents']);
    Route::get('/chauffeur/eleves', [EleveController::class, 'elevesParTrajet']);
});

// *************** PUBLIC ***************
Route::get('/eleves', [EleveController::class, 'index']);
Route::get('/eleves/{id}', [EleveController::class, 'show']);

Route::post('route/dynamic', [RouteController::class, 'dynamicRoute']);
Route::get('/trajets/{trajet}/route', [RouteController::class, 'routeForTrajet']);

Route::get('/admin/bus-positions', function () {
    return \App\Models\BusPosition::with('trajet.chauffeur')
        ->where('is_active', 1)
        ->where('updated_at', '>=', now()->subMinutes(2))
        ->whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('bus_positions')
                ->groupBy('trajet_id');
        })
        ->get()
        ->values();
});