<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteFechaLaudo extends Model
{
    protected $table = 'expediente_fecha_laudo';

    protected $fillable = [
        'expediente_id',
        'fecha',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }
}
