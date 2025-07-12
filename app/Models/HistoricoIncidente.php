<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoIncidente extends Model
{
    use HasFactory;

    protected $table = 'historico_incidentes';

    protected $fillable = [
        'incidente_id',
        'alterado_por_id',
        'campo_alterado',
        'valor_anterior',
        'valor_novo',
    ];

    protected $casts = [
        'data_alteracao' => 'datetime',
    ];

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // Relacionamentos
    public function incidente()
    {
        return $this->belongsTo(Incidente::class);
    }

    public function alteradoPor()
    {
        return $this->belongsTo(Usuario::class, 'alterado_por_id');
    }
}
