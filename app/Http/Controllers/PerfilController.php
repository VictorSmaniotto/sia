<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PerfilController extends Controller
{
    public function show()
    {
        $usuario = Usuario::find(Session::get('usuario_id'));

        if (!$usuario) {
            return redirect('/login');
        }

        return Inertia::render('Perfil/Show', [
            'usuario' => $usuario
        ]);
    }

    public function edit()
    {
        $usuario = Usuario::find(Session::get('usuario_id'));

        if (!$usuario) {
            return redirect('/login');
        }

        return Inertia::render('Perfil/Edit', [
            'usuario' => $usuario
        ]);
    }

    public function update(Request $request)
    {
        $usuarioId = Session::get('usuario_id');
        $usuario = Usuario::find($usuarioId);

        if (!$usuario) {
            return redirect('/login');
        }

        $tenantId = app('tenant')->id;

        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('usuarios')->where(function ($query) use ($tenantId) {
                    return $query->where('tenant_id', $tenantId);
                })->ignore($usuarioId)
            ],
        ]);

        $usuario->update([
            'nome' => $request->nome,
            'email' => $request->email,
        ]);

        // Atualizar dados na sessão
        Session::put('usuario', $usuario->fresh());

        return redirect()->back()->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updatePassword(Request $request)
    {
        $usuarioId = Session::get('usuario_id');
        $usuario = Usuario::find($usuarioId);

        if (!$usuario) {
            return redirect('/login');
        }

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Verificar senha atual
        if (!Hash::check($request->current_password, $usuario->senha_hash)) {
            return back()->withErrors([
                'current_password' => 'A senha atual está incorreta.',
            ]);
        }

        $usuario->update([
            'senha_hash' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Senha alterada com sucesso!');
    }

    public function configuracoes()
    {
        $usuario = Usuario::find(Session::get('usuario_id'));

        if (!$usuario) {
            return redirect('/login');
        }

        return Inertia::render('Perfil/Configuracoes', [
            'usuario' => $usuario
        ]);
    }
}
