<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Illuminate\Support\Facades\View;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Identifica o tenant pelo domínio acessado
        $host = $request->getHost();

        $tenant = Tenant::where('dominio', $host)->where('ativo', true)->first();

        if (!$tenant || !$tenant->ativo) {
            abort(404, 'Tenant não encontrado ou inativo');
        }

        // Definir o tenant no contexto global
        app()->instance('tenant', $tenant);

        // Compartilhar o tenant com todas as views
        View::share('tenant', $tenant);

        // Configurar o contexto do banco para Row Level Security (se necessário)
        // DB::statement("SET app.tenant_id = ?", [$tenantId]);

        return $next($request);
    }
}
