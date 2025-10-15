<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteArbitro extends Model
{
    protected $table = 'expediente_arbitros';

    protected $fillable = [
        'expediente_id',
        'usuario_id',
    ];

    /**
     * Expediente relacionado
     */
    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    /**
     * Usuario (árbitro) relacionado
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
