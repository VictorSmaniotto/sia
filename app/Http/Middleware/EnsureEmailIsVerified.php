<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $usuarioId = session('usuario_id');

        if (!$usuarioId) {
            return redirect('/login');
        }

        $usuario = \App\Models\Usuario::find($usuarioId);

        if (!$usuario || !$usuario->email_verified_at) {
            return redirect('/email/verify');
        }

        return $next($request);
    }
}
