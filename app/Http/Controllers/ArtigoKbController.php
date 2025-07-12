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

        $artigos = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('BaseConhecimento/Index', [
            'artigos' => $artigos,
            'filtros' => $request->only(['categoria', 'status', 'busca']),
            'stats' => [
                'total' => ArtigoKb::where('tenant_id', $tenantId)->count(),
                'publicados' => ArtigoKb::where('tenant_id', $tenantId)->where('status', 'publicado')->count(),
                'rascunhos' => ArtigoKb::where('tenant_id', $tenantId)->where('status', 'rascunho')->count(),
                'visualizacoes' => ArtigoKb::where('tenant_id', $tenantId)->sum('visualizacoes'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = [
            'tutoriais',
            'procedimentos',
            'troubleshooting',
            'faq',
            'politicas',
            'documentacao',
            'configuracao',
            'seguranca'
        ];

        $statusOptions = [
            ['value' => 'rascunho', 'title' => 'Rascunho'],
            ['value' => 'revisao', 'title' => 'Em Revisão'],
            ['value' => 'publicado', 'title' => 'Publicado'],
            ['value' => 'arquivado', 'title' => 'Arquivado']
        ];

        return Inertia::render('BaseConhecimento/Create', [
            'categorias' => $categorias,
            'statusOptions' => $statusOptions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'resumo' => 'nullable|string|max:500',
            'conteudo' => 'required|string',
            'categoria' => 'required|string|max:100',
            'palavras_chave' => 'nullable|string',
            'status' => 'required|in:rascunho,revisao,publicado,arquivado',
            'tempo_leitura' => 'nullable|integer|min:1'
        ]);

        $artigo = ArtigoKb::create([
            'tenant_id' => app('tenant')->id,
            'titulo' => $request->titulo,
            'resumo' => $request->resumo,
            'conteudo' => $request->conteudo,
            'categoria' => $request->categoria,
            'palavras_chave' => $request->palavras_chave,
            'status' => $request->status,
            'autor_id' => auth()->user()->id,
            'visualizacoes' => 0,
            'tempo_leitura' => $request->tempo_leitura,
        ]);

        return redirect()->route('base-conhecimento.show', $artigo->id)
                        ->with('success', 'Artigo criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tenantId = app('tenant')->id;

        $artigo = ArtigoKb::where('tenant_id', $tenantId)
                         ->with(['autor'])
                         ->findOrFail($id);

        // Incrementar visualizações
        $artigo->increment('visualizacoes');

        // Buscar artigos relacionados (mesma categoria, excluindo o atual)
        $artigosRelacionados = ArtigoKb::where('tenant_id', $tenantId)
                                      ->where('categoria', $artigo->categoria)
                                      ->where('id', '!=', $id)
                                      ->where('status', 'publicado')
                                      ->orderBy('visualizacoes', 'desc')
                                      ->limit(5)
                                      ->get(['id', 'titulo', 'categoria', 'criado_em']);

        return Inertia::render('BaseConhecimento/Show', [
            'artigo' => $artigo,
            'artigosRelacionados' => $artigosRelacionados,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $artigo = ArtigoKb::where('tenant_id', app('tenant')->id)
                         ->findOrFail($id);

        $categorias = [
            'tutoriais',
            'procedimentos',
            'troubleshooting',
            'faq',
            'politicas',
            'documentacao',
            'configuracao',
            'seguranca'
        ];

        $statusOptions = [
            ['value' => 'rascunho', 'title' => 'Rascunho'],
            ['value' => 'revisao', 'title' => 'Em Revisão'],
            ['value' => 'publicado', 'title' => 'Publicado'],
            ['value' => 'arquivado', 'title' => 'Arquivado']
        ];

        return Inertia::render('BaseConhecimento/Edit', [
            'artigo' => $artigo,
            'categorias' => $categorias,
            'statusOptions' => $statusOptions,
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
            'resumo' => 'nullable|string|max:500',
            'conteudo' => 'required|string',
            'categoria' => 'required|string|max:100',
            'status' => 'required|in:rascunho,revisao,publicado,arquivado',
            'palavras_chave' => 'nullable|string',
            'tempo_leitura' => 'nullable|integer|min:1'
        ]);

        $artigo->update($request->only([
            'titulo',
            'resumo',
            'conteudo',
            'categoria',
            'status',
            'palavras_chave',
            'tempo_leitura'
        ]));

        return redirect()->route('base-conhecimento.show', $artigo->id)
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

        return redirect()->route('base-conhecimento.index')
                        ->with('success', 'Artigo excluído com sucesso!');
    }
}
