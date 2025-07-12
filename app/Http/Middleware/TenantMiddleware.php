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
        // Por enquanto, vamos usar um tenant padrão (ID = 1) para desenvolvimento
        // Futuramente isso pode ser baseado no domínio ou subdomínio
        $tenantId = 1;

        // Verificar se o tenant existe
        $tenant = Tenant::find($tenantId);

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
