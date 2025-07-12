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
                        ->with(['responsavel']);

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
        $usuarios = Usuario::where('tenant_id', $tenantId)->where('ativo', true)->get(['id', 'nome']);

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
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'status' => 'required|in:Novo,Investigando,Erro Conhecido,Resolvido,Fechado',
            'prioridade' => 'required|in:Alta,Média,Baixa',
            'responsavel_id' => 'nullable|exists:usuarios,id',
            'causa_raiz' => 'nullable|string',
            'solucao_contorno' => 'nullable|string',
            'solucao_definitiva' => 'nullable|string',
            'incidentes_relacionados' => 'nullable|array',
            'incidentes_relacionados.*' => 'exists:incidentes,id'
        ]);

        // Validações de workflow ITIL
        if (in_array($validated['status'], ['Erro Conhecido', 'Resolvido']) && empty($validated['causa_raiz'])) {
            return back()->withErrors(['causa_raiz' => 'Causa raiz é obrigatória para este status.']);
        }

        if ($validated['status'] === 'Resolvido' && empty($validated['solucao_definitiva'])) {
            return back()->withErrors(['solucao_definitiva' => 'Solução definitiva é obrigatória para status Resolvido.']);
        }

        $problema = Problema::create([
            'tenant_id' => app('tenant')->id,
            'titulo' => $validated['titulo'],
            'descricao' => $validated['descricao'],
            'status' => $validated['status'],
            'prioridade' => $validated['prioridade'],
            'responsavel_id' => $validated['responsavel_id'],
            'causa_raiz' => $validated['causa_raiz'],
            'solucao_contorno' => $validated['solucao_contorno'],
            'solucao_definitiva' => $validated['solucao_definitiva'],
        ]);

        // Vincular incidentes relacionados
        if (!empty($validated['incidentes_relacionados'])) {
            Incidente::whereIn('id', $validated['incidentes_relacionados'])
                     ->where('tenant_id', app('tenant')->id)
                     ->update(['problema_id' => $problema->id]);
        }

        return redirect()->route('problemas.index')
                        ->with('success', 'Problema criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $problema = Problema::where('tenant_id', app('tenant')->id)
                           ->with(['responsavel'])
                           ->findOrFail($id);

        // Buscar incidentes relacionados
        $incidentesRelacionados = Incidente::where('tenant_id', app('tenant')->id)
                                          ->where('problema_id', $id)
                                          ->with(['solicitante', 'responsavel'])
                                          ->get();

        // Buscar comentários
        $comentarios = \App\Models\Comentario::where('tenant_id', app('tenant')->id)
                                            ->where('problema_id', $id)
                                            ->with(['autor'])
                                            ->orderBy('criado_em', 'asc')
                                            ->get();

        return Inertia::render('Problemas/Show', [
            'problema' => $problema,
            'incidentesRelacionados' => $incidentesRelacionados,
            'comentarios' => $comentarios
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $problema = Problema::where('tenant_id', app('tenant')->id)
                           ->findOrFail($id);

        $tenantId = app('tenant')->id;
        $usuarios = Usuario::where('tenant_id', $tenantId)->where('ativo', true)->get(['id', 'nome']);
        $incidentes = Incidente::where('tenant_id', $tenantId)
                              ->whereIn('status', ['Aberto', 'Em andamento'])
                              ->get(['id', 'titulo']);

        // Incidentes já relacionados
        $incidentesRelacionados = Incidente::where('tenant_id', $tenantId)
                                          ->where('problema_id', $id)
                                          ->get(['id', 'titulo']);

        return Inertia::render('Problemas/Edit', [
            'problema' => $problema,
            'usuarios' => $usuarios,
            'incidentes' => $incidentes,
            'incidentesRelacionados' => $incidentesRelacionados
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $problema = Problema::where('tenant_id', app('tenant')->id)
                           ->findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string',
            'status' => 'required|in:Novo,Investigando,Erro Conhecido,Resolvido,Fechado',
            'prioridade' => 'required|in:Alta,Média,Baixa',
            'responsavel_id' => 'nullable|exists:usuarios,id',
            'causa_raiz' => 'nullable|string',
            'solucao_contorno' => 'nullable|string',
            'solucao_definitiva' => 'nullable|string',
            'incidentes_relacionados' => 'nullable|array',
            'incidentes_relacionados.*' => 'exists:incidentes,id'
        ]);

        // Validações de workflow ITIL
        if (in_array($validated['status'], ['Erro Conhecido', 'Resolvido']) && empty($validated['causa_raiz'])) {
            return back()->withErrors(['causa_raiz' => 'Causa raiz é obrigatória para este status.']);
        }

        if ($validated['status'] === 'Resolvido' && empty($validated['solucao_definitiva'])) {
            return back()->withErrors(['solucao_definitiva' => 'Solução definitiva é obrigatória para status Resolvido.']);
        }

        $problema->update($validated);

        // Atualizar incidentes relacionados
        // Primeiro, remover vinculação de todos os incidentes atuais
        Incidente::where('problema_id', $id)->update(['problema_id' => null]);

        // Depois, vincular os novos incidentes selecionados
        if (!empty($validated['incidentes_relacionados'])) {
            Incidente::whereIn('id', $validated['incidentes_relacionados'])
                     ->where('tenant_id', app('tenant')->id)
                     ->update(['problema_id' => $id]);
        }

        return redirect()->route('problemas.show', $id)
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
