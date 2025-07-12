<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('usuario_id')) {
            return redirect('/login');
        }

        // Verificar se o usuário ainda existe e está ativo
        $usuario = \App\Models\Usuario::where('id', Session::get('usuario_id'))
                                    ->where('ativo', true)
                                    ->first();

        if (!$usuario) {
            Session::forget(['usuario_id', 'usuario']);
            return redirect('/login');
        }

        // Atualizar dados do usuário na sessão
        Session::put('usuario', $usuario);

        return $next($request);
    }
}
