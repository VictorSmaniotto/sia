<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incidente;
use App\Models\Usuario;
use App\Models\Problema;
use Inertia\Inertia;

class IncidenteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenantId = app('tenant')->id;

        $query = Incidente::where('tenant_id', $tenantId)
                          ->with(['solicitante', 'responsavel', 'problema']);

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
                  ->orWhere('descricao', 'LIKE', '%' . $request->busca . '%');
            });
        }

        $incidentes = $query->orderBy('criado_em', 'desc')->paginate(15);

        // Dados para filtros
        $usuarios = Usuario::where('tenant_id', $tenantId)->where('ativo', true)->get();

        return Inertia::render('Incidentes/Index', [
            'incidentes' => $incidentes,
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
        $problemas = Problema::where('tenant_id', $tenantId)->get();

        return Inertia::render('Incidentes/Create', [
            'usuarios' => $usuarios,
            'problemas' => $problemas,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'prioridade' => 'required|in:Alta,Média,Baixa',
            'impacto' => 'nullable|in:Alto,Médio,Baixo',
            'urgencia' => 'nullable|in:Alta,Média,Baixa',
            'solicitante_id' => 'nullable|exists:usuarios,id',
            'responsavel_id' => 'nullable|exists:usuarios,id',
            'problema_id' => 'nullable|exists:problemas,id',
        ]);

        $incidente = Incidente::create([
            'tenant_id' => app('tenant')->id,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'status' => 'Aberto',
            'prioridade' => $request->prioridade,
            'impacto' => $request->impacto,
            'urgencia' => $request->urgencia,
            'solicitante_id' => $request->solicitante_id,
            'responsavel_id' => $request->responsavel_id,
            'problema_id' => $request->problema_id,
        ]);

        return redirect()->route('incidentes.index')
                        ->with('success', 'Incidente criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $incidente = Incidente::where('tenant_id', app('tenant')->id)
                             ->with([
                                 'solicitante',
                                 'responsavel',
                                 'problema',
                                 'comentarios.autor',
                                 'historicos'
                             ])
                             ->findOrFail($id);

        return Inertia::render('Incidentes/Show', [
            'incidente' => $incidente,
            'usuarios' => Usuario::where('tenant_id', app('tenant')->id)
                              ->select('id', 'nome', 'email')
                              ->get(),
            'problemas' => Problema::where('tenant_id', app('tenant')->id)
                               ->select('id', 'titulo')
                               ->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $incidente = Incidente::where('tenant_id', app('tenant')->id)
                             ->with(['solicitante', 'responsavel', 'problema'])
                             ->findOrFail($id);

        $tenantId = app('tenant')->id;
        $usuarios = Usuario::where('tenant_id', $tenantId)->where('ativo', true)->get();
        $problemas = Problema::where('tenant_id', $tenantId)->get();

        return Inertia::render('Incidentes/Edit', [
            'incidente' => $incidente,
            'usuarios' => $usuarios,
            'problemas' => $problemas,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $incidente = Incidente::where('tenant_id', app('tenant')->id)
                             ->findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|in:Aberto,Em Andamento,Aguardando,Resolvido,Fechado',
            'prioridade' => 'required|in:Alta,Média,Baixa',
            'impacto' => 'nullable|in:Alto,Médio,Baixo',
            'urgencia' => 'nullable|in:Alta,Média,Baixa',
            'solicitante_id' => 'nullable|exists:usuarios,id',
            'responsavel_id' => 'nullable|exists:usuarios,id',
            'problema_id' => 'nullable|exists:problemas,id',
            'resolucao' => 'nullable|string',
        ]);

        $incidente->update($request->only([
            'titulo',
            'descricao',
            'status',
            'prioridade',
            'impacto',
            'urgencia',
            'solicitante_id',
            'responsavel_id',
            'problema_id',
            'resolucao'
        ]));

        return redirect()->route('incidentes.index')
                        ->with('success', 'Incidente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $incidente = Incidente::where('tenant_id', app('tenant')->id)
                             ->findOrFail($id);

        $incidente->delete();

        return redirect()->route('incidentes.index')
                        ->with('success', 'Incidente excluído com sucesso!');
    }
}
