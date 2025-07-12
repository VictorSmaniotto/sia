<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sla extends Model
{
    use HasFactory;

    protected $table = 'sla';

    protected $fillable = [
        'tenant_id',
        'categoria',
        'prioridade',
        'tempo_resolucao_em_horas',
    ];

    // Relacionamentos
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Método para verificar SLA
    public static function obterTempoResolucao($tenantId, $categoria, $prioridade)
    {
        return static::where('tenant_id', $tenantId)
                    ->where('categoria', $categoria)
                    ->where('prioridade', $prioridade)
                    ->value('tempo_resolucao_em_horas') ?? 24; // Default 24h
    }
}
