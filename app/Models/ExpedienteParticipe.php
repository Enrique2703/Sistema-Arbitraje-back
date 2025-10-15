<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteParticipe extends Model
{
    protected $table = 'expediente_participes';

    protected $fillable = [
        'expediente_id',
        'participe_id',
        'condicion',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function participe()
    {
        return $this->belongsTo(Participe::class, 'participe_id');
    }
}
