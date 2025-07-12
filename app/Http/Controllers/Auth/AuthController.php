<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
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

    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'organization' => 'required|string|max:255',
        ]);

        $tenantId = app('tenant')->id;

        // Verificar se o email já existe no tenant atual
        $existingUser = Usuario::where('tenant_id', $tenantId)
                              ->where('email', $request->email)
                              ->first();

        if ($existingUser) {
            return back()->withErrors([
                'email' => 'Este e-mail já está sendo usado.',
            ]);
        }

        // Criar o usuário
        $usuario = Usuario::create([
            'tenant_id' => $tenantId,
            'nome' => $request->nome,
            'email' => $request->email,
            'senha_hash' => Hash::make($request->password),
            'role' => 'tecnico', // Role padrão
            'ativo' => true,
        ]);

        // Login automático após registro
        Session::put('usuario_id', $usuario->id);
        Session::put('usuario', $usuario);

        return redirect('/dashboard')->with('success', 'Conta criada com sucesso!');
    }

    public function showForgotPassword()
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $tenantId = app('tenant')->id;

        // Verificar se o usuário existe no tenant atual
        $usuario = Usuario::where('tenant_id', $tenantId)
                         ->where('email', $request->email)
                         ->where('ativo', true)
                         ->first();

        if (!$usuario) {
            return back()->withErrors([
                'email' => 'Não encontramos um usuário com este endereço de e-mail.',
            ]);
        }

        // Gerar token de reset
        $token = Str::random(64);

        // Salvar na tabela password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Enviar email (por enquanto apenas retornar sucesso)
        // TODO: Implementar envio de email real

        return back()->with('status', 'Link de recuperação enviado para seu e-mail!');
    }

    public function showResetPassword(Request $request)
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $request->route('token'),
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Verificar token
        $passwordReset = DB::table('password_reset_tokens')
                          ->where('email', $request->email)
                          ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors([
                'email' => 'Token de recuperação inválido.',
            ]);
        }

        // Verificar se o token não expirou (24 horas)
        if (now()->diffInHours($passwordReset->created_at) > 24) {
            return back()->withErrors([
                'email' => 'Token de recuperação expirado.',
            ]);
        }

        $tenantId = app('tenant')->id;

        // Atualizar senha do usuário
        $usuario = Usuario::where('tenant_id', $tenantId)
                         ->where('email', $request->email)
                         ->first();

        if (!$usuario) {
            return back()->withErrors([
                'email' => 'Usuário não encontrado.',
            ]);
        }

        $usuario->update([
            'senha_hash' => Hash::make($request->password),
        ]);

        // Remover token usado
        DB::table('password_reset_tokens')
          ->where('email', $request->email)
          ->delete();

        return redirect('/login')->with('success', 'Senha redefinida com sucesso!');
    }
}
