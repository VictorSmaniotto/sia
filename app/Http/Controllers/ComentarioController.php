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
            'incidente_id' => 'required|exists:incidentes,id',
            'conteudo' => 'required|string',
        ]);

        // Verificar se o incidente pertence ao tenant atual
        $incidente = Incidente::where('tenant_id', app('tenant')->id)
                             ->findOrFail($request->incidente_id);

        $comentario = Comentario::create([
            'tenant_id' => app('tenant')->id,
            'incidente_id' => $request->incidente_id,
            'usuario_id' => auth()->user()->id,
            'conteudo' => $request->conteudo,
        ]);

        return redirect()->back()->with('success', 'Comentário adicionado com sucesso!');
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
