<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\IncidenteController;
use App\Http\Controllers\ProblemaController;
use App\Http\Controllers\ArtigoKbController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\PerfilController;

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
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rotas protegidas (necessitam autenticação)
    Route::middleware(['custom.auth'])->group(function () {
        // Dashboard principal (antiga Welcome)
        Route::get('/dashboard', function () {
            $tenantId = app('tenant')->id;

            // Calcular métricas reais
            $incidentesAbertos = \App\Models\Incidente::where('tenant_id', $tenantId)
                                                     ->whereIn('status', ['Aberto', 'Em andamento'])
                                                     ->count();

            $problemasNovos = \App\Models\Problema::where('tenant_id', $tenantId)
                                                 ->whereIn('status', ['Novo', 'Investigando'])
                                                 ->count();

            $artigosKb = \App\Models\ArtigoKb::where('tenant_id', $tenantId)->count();

            $usuariosAtivos = \App\Models\Usuario::where('tenant_id', $tenantId)
                                                ->where('ativo', true)
                                                ->count();

            // Métricas adicionais
            $incidentesCriticosAbertos = \App\Models\Incidente::where('tenant_id', $tenantId)
                                                             ->where('prioridade', 'Alta')
                                                             ->whereIn('status', ['Aberto', 'Em andamento'])
                                                             ->count();

            $problemasErroConhecido = \App\Models\Problema::where('tenant_id', $tenantId)
                                                         ->where('status', 'Erro Conhecido')
                                                         ->count();

            return Inertia::render('Dashboard', [
                'tenant' => app('tenant'),
                'stats' => [
                    'incidentes_abertos' => $incidentesAbertos,
                    'problemas_novos' => $problemasNovos,
                    'artigos_kb' => $artigosKb,
                    'usuarios_ativos' => $usuariosAtivos,
                ],
                'metricas_extras' => [
                    'incidentes_criticos' => $incidentesCriticosAbertos,
                    'erros_conhecidos' => $problemasErroConhecido,
                ]
            ]);
        })->name('dashboard');

        // Rotas para incidentes
        Route::resource('incidentes', IncidenteController::class);

        // Rotas para problemas
        Route::resource('problemas', ProblemaController::class);

        // Rotas para base de conhecimento
        Route::resource('base-conhecimento', ArtigoKbController::class)->names([
            'index' => 'base-conhecimento.index',
            'create' => 'base-conhecimento.create',
            'store' => 'base-conhecimento.store',
            'show' => 'base-conhecimento.show',
            'edit' => 'base-conhecimento.edit',
            'update' => 'base-conhecimento.update',
            'destroy' => 'base-conhecimento.destroy',
        ]);

        // Rotas para comentários
        Route::post('comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');

        // Perfil do usuário
        Route::get('/perfil', [PerfilController::class, 'show'])->name('perfil.show');
        Route::get('/perfil/edit', [PerfilController::class, 'edit'])->name('perfil.edit');
        Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');
        Route::post('/perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');
        Route::get('/perfil/configuracoes', [PerfilController::class, 'configuracoes'])->name('perfil.configuracoes');
    });
});
