<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'autor_id',
        'incidente_id',
        'problema_id',
        'conteudo',
    ];

    protected $casts = [
        'criado_em' => 'datetime',
    ];

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'updated_at';

    // Relacionamentos
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function autor()
    {
        return $this->belongsTo(Usuario::class, 'autor_id');
    }

    public function incidente()
    {
        return $this->belongsTo(Incidente::class);
    }

    public function problema()
    {
        return $this->belongsTo(Problema::class);
    }
}
