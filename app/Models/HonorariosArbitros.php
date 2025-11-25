<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HonorariosArbitros extends Model
{
    protected $table = 'honorarios_arbitros';

    protected $fillable = [
        'escala',
        'rango_min',
        'rango_max',
        'porcentaje',
        'monto_max',
        'monto_base',
        'regla',
    ];
}
