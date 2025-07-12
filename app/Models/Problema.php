<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Problema extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'titulo',
        'descricao',
        'status',
        'prioridade',
        'causa_raiz',
        'solucao_contorno',
        'solucao_definitiva',
        'responsavel_id',
    ];

    protected $casts = [
        'criado_em' => 'datetime',
        'atualizado_em' => 'datetime',
    ];

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    // Relacionamentos
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function responsavel()
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    public function incidentes()
    {
        return $this->hasMany(Incidente::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }

    public function artigosKb()
    {
        return $this->hasMany(ArtigoKb::class);
    }

    // Scopes por status
    public function scopeNovos($query)
    {
        return $query->where('status', 'Novo');
    }

    public function scopeInvestigando($query)
    {
        return $query->where('status', 'Investigando');
    }

    public function scopeErroConhecido($query)
    {
        return $query->where('status', 'Erro Conhecido');
    }

    public function scopeResolvidos($query)
    {
        return $query->where('status', 'Resolvido');
    }

    public function scopeFechados($query)
    {
        return $query->where('status', 'Fechado');
    }

    // Scope por prioridade
    public function scopePrioridadeAlta($query)
    {
        return $query->where('prioridade', 'Alta');
    }
}
