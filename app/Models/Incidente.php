<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incidente extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'titulo',
        'descricao',
        'status',
        'prioridade',
        'impacto',
        'urgencia',
        'solicitante_id',
        'responsavel_id',
        'problema_id',
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

    public function solicitante()
    {
        return $this->belongsTo(Usuario::class, 'solicitante_id');
    }

    public function responsavel()
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    public function problema()
    {
        return $this->belongsTo(Problema::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }

    public function historicos()
    {
        return $this->hasMany(HistoricoIncidente::class);
    }

    // Scopes por status
    public function scopeAbertos($query)
    {
        return $query->where('status', 'Aberto');
    }

    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'Em andamento');
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

    public function scopeCriticos($query)
    {
        return $query->where('prioridade', 'Alta')
                    ->where('impacto', 'Alto');
    }

    // Método para calcular prioridade baseado em impacto e urgência
    public function calcularPrioridade()
    {
        if ($this->impacto === 'Alto' && $this->urgencia === 'Alta') {
            return 'Alta';
        } elseif (($this->impacto === 'Alto' && $this->urgencia === 'Média') ||
                  ($this->impacto === 'Médio' && $this->urgencia === 'Alta')) {
            return 'Alta';
        } elseif ($this->impacto === 'Baixo' && $this->urgencia === 'Baixa') {
            return 'Baixa';
        } else {
            return 'Média';
        }
    }
}
