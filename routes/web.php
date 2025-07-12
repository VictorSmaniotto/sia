<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\IncidenteController;
use App\Http\Controllers\ProblemaController;
use App\Http\Controllers\ArtigoKbController;
use App\Http\Controllers\ComentarioController;

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

    // Rota raiz redireciona para login ou dashboard
    Route::get('/', function () {
        if (session()->has('user_id')) {
            return redirect('/dashboard');
        }
        return redirect('/login');
    });

    // Rotas de autenticação (públicas)
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rotas protegidas (necessitam autenticação)
    Route::middleware(['custom.auth'])->group(function () {
        // Dashboard principal (antiga Welcome)
        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard', [
                'tenant' => app('tenant'),
                'stats' => [
                    'incidentes_abertos' => 0,
                    'problemas_novos' => 0,
                    'artigos_kb' => 0,
                    'usuarios_ativos' => 0,
                ]
            ]);
        })->name('dashboard');

        // Rotas para incidentes
        Route::resource('incidentes', IncidenteController::class);

        // Rotas para problemas
        Route::resource('problemas', ProblemaController::class);

        // Rotas para base de conhecimento
        Route::resource('artigos-kb', ArtigoKbController::class);

        // Rotas para comentários
        Route::post('comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');
    });
});
