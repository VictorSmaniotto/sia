<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar usuário por email no tenant atual
        $tenantId = app('tenant')->id;
        $usuario = Usuario::where('tenant_id', $tenantId)
                         ->where('email', $request->email)
                         ->where('ativo', true)
                         ->first();

        if ($usuario && Hash::check($request->password, $usuario->senha_hash)) {
            // Login bem-sucedido
            Session::put('usuario_id', $usuario->id);
            Session::put('usuario', $usuario);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não são válidas.',
        ]);
    }

    public function logout(Request $request)
    {
        Session::forget(['usuario_id', 'usuario']);
        return redirect('/login');
    }

    public function showDashboard()
    {
        $usuario = Session::get('usuario');

        if (!$usuario) {
            return redirect('/login');
        }

        $tenantId = app('tenant')->id;

        $stats = [
            'incidentes_abertos' => \App\Models\Incidente::where('tenant_id', $tenantId)->where('status', 'Aberto')->count(),
            'incidentes_em_andamento' => \App\Models\Incidente::where('tenant_id', $tenantId)->where('status', 'Em andamento')->count(),
            'problemas_novos' => \App\Models\Problema::where('tenant_id', $tenantId)->where('status', 'Novo')->count(),
            'problemas_investigando' => \App\Models\Problema::where('tenant_id', $tenantId)->where('status', 'Investigando')->count(),
            'artigos_kb' => \App\Models\ArtigoKb::where('tenant_id', $tenantId)->count(),
            'usuarios_ativos' => \App\Models\Usuario::where('tenant_id', $tenantId)->where('ativo', true)->count(),
        ];

        return Inertia::render('Dashboard', [
            'usuario' => $usuario,
            'stats' => $stats,
            'tenant' => app('tenant'),
        ]);
    }
}
