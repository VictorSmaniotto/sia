<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'dominio',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'criado_em' => 'datetime',
    ];

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'updated_at';

    // Relacionamentos
    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }

    public function incidentes()
    {
        return $this->hasMany(Incidente::class);
    }

    public function problemas()
    {
        return $this->hasMany(Problema::class);
    }

    public function artigosKb()
    {
        return $this->hasMany(ArtigoKb::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }

    public function slas()
    {
        return $this->hasMany(Sla::class);
    }
}
