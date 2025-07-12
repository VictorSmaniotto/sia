<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\IncidenteController;
use App\Http\Controllers\ProblemaController;
use App\Http\Controllers\ArtigoKbController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Aplicar middleware tenant em todas as rotas
Route::middleware(['tenant'])->group(function () {

    // Rotas públicas (sem autenticação)
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'tenant' => app('tenant'),
            'stats' => [
                'incidentes_abertos' => \App\Models\Incidente::where('tenant_id', app('tenant')->id)->where('status', 'Aberto')->count(),
                'problemas_novos' => \App\Models\Problema::where('tenant_id', app('tenant')->id)->where('status', 'Novo')->count(),
                'artigos_kb' => \App\Models\ArtigoKb::where('tenant_id', app('tenant')->id)->count(),
                'usuarios_ativos' => \App\Models\Usuario::where('tenant_id', app('tenant')->id)->where('ativo', true)->count(),
            ]
        ]);
    });

    // Rotas de autenticação
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rotas protegidas (necessitam autenticação)
    Route::middleware(['custom.auth'])->group(function () {
        Route::get('/dashboard', [AuthController::class, 'showDashboard']);

        // Rotas para incidentes
        Route::resource('incidentes', IncidenteController::class);

        // Rotas para problemas
        Route::resource('problemas', ProblemaController::class);

        // Rotas para base de conhecimento
        Route::resource('artigos-kb', ArtigoKbController::class);
    });
});
