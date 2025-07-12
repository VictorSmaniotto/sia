<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Usuario extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'nome',
        'email',
        'email_verified_at',
        'email_verification_token',
        'senha_hash',
        'role',
        'ativo',
    ];

    protected $hidden = [
        'senha_hash',
        'email_verification_token',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'criado_em' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'updated_at';

    // Relacionamentos
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function incidentesSolicitados()
    {
        return $this->hasMany(Incidente::class, 'solicitante_id');
    }

    public function incidentesResponsavel()
    {
        return $this->hasMany(Incidente::class, 'responsavel_id');
    }

    public function problemasResponsavel()
    {
        return $this->hasMany(Problema::class, 'responsavel_id');
    }

    public function artigosPublicados()
    {
        return $this->hasMany(ArtigoKb::class, 'publicado_por_id');
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class, 'autor_id');
    }

    public function historicosIncidentes()
    {
        return $this->hasMany(HistoricoIncidente::class, 'alterado_por_id');
    }

    // Mutators
    public function setSenhaHashAttribute($value)
    {
        $this->attributes['senha_hash'] = Hash::make($value);
    }

    // Scopes para roles
    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeTecnicos($query)
    {
        return $query->where('role', 'tecnico');
    }

    public function scopeGestores($query)
    {
        return $query->where('role', 'gestor');
    }
}
