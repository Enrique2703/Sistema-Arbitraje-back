<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expediente extends Model
{
    protected $table = 'expedientes';

    protected $fillable = [
        'usuario_id',
        'estado',
        'cerrado',
        'nombre',
        'numero',
        'anio',
        'codigo',
        'etapa_procesal',
        'inicio_proceso',
        'tipo_proceso',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function arbitros()
    {
        return $this->hasMany(ExpedienteArbitro::class, 'expediente_id');
    }

    public function adjutadores()
    {
        return $this->hasMany(ExpedienteAdjutador::class, 'expediente_id');
    }

    public function secretariosArbitrales()
    {
        return $this->hasMany(SecretarioArbitral::class, 'expediente_id');
    }

    public function secretariosTecnicos()
    {
        return $this->hasMany(SecretarioTecnico::class, 'expediente_id');
    }

    public function participes()
    {
        return $this->hasMany(ExpedienteParticipe::class, 'expediente_id');
    }

    public function fechaLaudo()
    {
        return $this->hasOne(ExpedienteFechaLaudo::class, 'expediente_id');
    }

    public function fechaResolucion()
    {
        return $this->hasOne(ExpedienteFechaResolucion::class, 'expediente_id');
    }

    public function participeDocumentos()
    {
        return $this->hasMany(ParticipeDocumento::class, 'expediente_id');
    }
}
