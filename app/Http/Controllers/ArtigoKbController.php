<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArtigoKb;
use App\Models\Usuario;
use Inertia\Inertia;

class ArtigoKbController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenantId = app('tenant')->id;

        $query = ArtigoKb::where('tenant_id', $tenantId)
                        ->with(['autor']);

        // Filtros
        if ($request->categoria) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->busca) {
            $query->where(function($q) use ($request) {
                $q->where('titulo', 'LIKE', '%' . $request->busca . '%')
                  ->orWhere('conteudo', 'LIKE', '%' . $request->busca . '%')
                  ->orWhere('palavras_chave', 'LIKE', '%' . $request->busca . '%');
            });
        }

        $artigos = $query->orderBy('criado_em', 'desc')->paginate(15);

        return Inertia::render('ArtigosKb/Index', [
            'artigos' => $artigos,
            'filtros' => $request->only(['categoria', 'status', 'busca']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('ArtigosKb/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'categoria' => 'required|string|max:100',
            'palavras_chave' => 'nullable|string',
        ]);

        $artigo = ArtigoKb::create([
            'tenant_id' => app('tenant')->id,
            'titulo' => $request->titulo,
            'conteudo' => $request->conteudo,
            'categoria' => $request->categoria,
            'palavras_chave' => $request->palavras_chave,
            'status' => 'Rascunho',
            'autor_id' => auth()->user()->id,
            'visualizacoes' => 0,
        ]);

        return redirect()->route('artigos-kb.index')
                        ->with('success', 'Artigo criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $artigo = ArtigoKb::where('tenant_id', app('tenant')->id)
                         ->with(['autor'])
                         ->findOrFail($id);

        // Incrementar visualizações
        $artigo->increment('visualizacoes');

        return Inertia::render('ArtigosKb/Show', [
            'artigo' => $artigo,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $artigo = ArtigoKb::where('tenant_id', app('tenant')->id)
                         ->findOrFail($id);

        return Inertia::render('ArtigosKb/Edit', [
            'artigo' => $artigo,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $artigo = ArtigoKb::where('tenant_id', app('tenant')->id)
                         ->findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
            'categoria' => 'required|string|max:100',
            'status' => 'required|in:Rascunho,Publicado,Arquivado',
            'palavras_chave' => 'nullable|string',
        ]);

        $artigo->update($request->only([
            'titulo',
            'conteudo',
            'categoria',
            'status',
            'palavras_chave'
        ]));

        return redirect()->route('artigos-kb.index')
                        ->with('success', 'Artigo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $artigo = ArtigoKb::where('tenant_id', app('tenant')->id)
                         ->findOrFail($id);

        $artigo->delete();

        return redirect()->route('artigos-kb.index')
                        ->with('success', 'Artigo excluído com sucesso!');
    }
}
