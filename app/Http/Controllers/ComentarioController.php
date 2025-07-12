<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use App\Models\Incidente;

class ComentarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'incidente_id' => 'nullable|exists:incidentes,id',
            'problema_id' => 'nullable|exists:problemas,id',
            'conteudo' => 'required|string|max:2000',
        ]);

        // Deve ter ou incidente_id ou problema_id, mas não ambos
        if (!$request->incidente_id && !$request->problema_id) {
            return back()->withErrors(['error' => 'Deve ser associado a um incidente ou problema.']);
        }

        if ($request->incidente_id && $request->problema_id) {
            return back()->withErrors(['error' => 'Não pode ser associado a incidente e problema simultaneamente.']);
        }

        $tenantId = app('tenant')->id;

        // Verificar se a entidade pertence ao tenant atual
        if ($request->incidente_id) {
            $incidente = \App\Models\Incidente::where('tenant_id', $tenantId)
                                             ->findOrFail($request->incidente_id);
        }

        if ($request->problema_id) {
            $problema = \App\Models\Problema::where('tenant_id', $tenantId)
                                           ->findOrFail($request->problema_id);
        }

        // Obter usuário atual da sessão (sistema customizado de auth)
        $usuarioId = session('usuario_id');
        if (!$usuarioId) {
            return back()->withErrors(['error' => 'Usuário não autenticado.']);
        }

        $comentario = Comentario::create([
            'tenant_id' => $tenantId,
            'incidente_id' => $request->incidente_id,
            'problema_id' => $request->problema_id,
            'autor_id' => $usuarioId,
            'conteudo' => $request->conteudo,
        ]);

        return back()->with('success', 'Comentário adicionado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
