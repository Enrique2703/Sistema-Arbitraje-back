<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteFechaResolucion extends Model
{
    protected $table = 'expediente_fecha_resolucion';

    protected $fillable = [
        'expediente_id',
        'fecha',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }
}
