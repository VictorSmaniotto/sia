<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::middleware(['tenant'])->group(function () {
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
});
