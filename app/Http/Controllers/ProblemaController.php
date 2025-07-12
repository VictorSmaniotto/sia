<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Problema;
use App\Models\Usuario;
use App\Models\Incidente;
use Inertia\Inertia;

class ProblemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenantId = app('tenant')->id;

        $query = Problema::where('tenant_id', $tenantId)
                        ->with(['responsavel', 'incidentesRelacionados']);

        // Filtros
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->prioridade) {
            $query->where('prioridade', $request->prioridade);
        }

        if ($request->responsavel) {
            $query->where('responsavel_id', $request->responsavel);
        }

        if ($request->busca) {
            $query->where(function($q) use ($request) {
                $q->where('titulo', 'LIKE', '%' . $request->busca . '%')
                  ->orWhere('descricao', 'LIKE', '%' . $request->busca . '%')
                  ->orWhere('causa_raiz', 'LIKE', '%' . $request->busca . '%');
            });
        }

        $problemas = $query->orderBy('criado_em', 'desc')->paginate(15);

        // Dados para filtros
        $usuarios = Usuario::where('tenant_id', $tenantId)->where('ativo', true)->get();

        return Inertia::render('Problemas/Index', [
            'problemas' => $problemas,
            'usuarios' => $usuarios,
            'filtros' => $request->only(['status', 'prioridade', 'responsavel', 'busca']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenantId = app('tenant')->id;
        $usuarios = Usuario::where('tenant_id', $tenantId)->where('ativo', true)->get();
        $incidentes = Incidente::where('tenant_id', $tenantId)
                              ->whereNull('problema_id')
                              ->select('id', 'titulo')
                              ->get();

        return Inertia::render('Problemas/Create', [
            'usuarios' => $usuarios,
            'incidentes' => $incidentes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'prioridade' => 'required|in:Alta,Média,Baixa',
            'impacto' => 'nullable|in:Alto,Médio,Baixo',
            'responsavel_id' => 'nullable|exists:usuarios,id',
            'causa_raiz' => 'nullable|string',
            'solucao_alternativa' => 'nullable|string',
        ]);

        $problema = Problema::create([
            'tenant_id' => app('tenant')->id,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'status' => 'Novo',
            'prioridade' => $request->prioridade,
            'impacto' => $request->impacto,
            'responsavel_id' => $request->responsavel_id,
            'causa_raiz' => $request->causa_raiz,
            'solucao_alternativa' => $request->solucao_alternativa,
        ]);

        return redirect()->route('problemas.index')
                        ->with('success', 'Problema criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $problema = Problema::where('tenant_id', app('tenant')->id)
                           ->with([
                               'responsavel',
                               'incidentesRelacionados.solicitante',
                               'comentarios.usuario',
                               'historicos'
                           ])
                           ->findOrFail($id);

        return Inertia::render('Problemas/Show', [
            'problema' => $problema,
            'usuarios' => Usuario::where('tenant_id', app('tenant')->id)
                              ->select('id', 'nome', 'email')
                              ->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $problema = Problema::where('tenant_id', app('tenant')->id)
                           ->with(['responsavel'])
                           ->findOrFail($id);

        $tenantId = app('tenant')->id;
        $usuarios = Usuario::where('tenant_id', $tenantId)->where('ativo', true)->get();

        return Inertia::render('Problemas/Edit', [
            'problema' => $problema,
            'usuarios' => $usuarios,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $problema = Problema::where('tenant_id', app('tenant')->id)
                           ->findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'status' => 'required|in:Novo,Em Investigação,Em Análise,Resolvido,Fechado',
            'prioridade' => 'required|in:Alta,Média,Baixa',
            'impacto' => 'nullable|in:Alto,Médio,Baixo',
            'responsavel_id' => 'nullable|exists:usuarios,id',
            'causa_raiz' => 'nullable|string',
            'solucao_alternativa' => 'nullable|string',
            'solucao_definitiva' => 'nullable|string',
        ]);

        $problema->update($request->only([
            'titulo',
            'descricao',
            'status',
            'prioridade',
            'impacto',
            'responsavel_id',
            'causa_raiz',
            'solucao_alternativa',
            'solucao_definitiva'
        ]));

        return redirect()->route('problemas.index')
                        ->with('success', 'Problema atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $problema = Problema::where('tenant_id', app('tenant')->id)
                           ->findOrFail($id);

        $problema->delete();

        return redirect()->route('problemas.index')
                        ->with('success', 'Problema excluído com sucesso!');
    }
}
