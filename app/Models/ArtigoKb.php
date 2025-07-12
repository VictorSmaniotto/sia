<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtigoKb extends Model
{
    use HasFactory;

    protected $table = 'artigos_kb';

    protected $fillable = [
        'tenant_id',
        'titulo',
        'resumo',
        'conteudo',
        'categoria',
        'palavras_chave',
        'status',
        'autor_id',
        'visualizacoes',
        'tempo_leitura',
        'publicado_por_id',
        'problema_id',
    ];

    protected $casts = [
        'publicado_em' => 'datetime',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // Relacionamentos
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function publicadoPor()
    {
        return $this->belongsTo(Usuario::class, 'publicado_por_id');
    }

    public function autor()
    {
        return $this->belongsTo(Usuario::class, 'autor_id');
    }

    public function problema()
    {
        return $this->belongsTo(Problema::class);
    }

    // Scopes
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeErrosConhecidos($query)
    {
        return $query->whereNotNull('problema_id');
    }

    public function scopeBuscarTexto($query, $termo)
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('titulo', 'LIKE', "%{$termo}%")
              ->orWhere('conteudo', 'LIKE', "%{$termo}%");
        });
    }
}
