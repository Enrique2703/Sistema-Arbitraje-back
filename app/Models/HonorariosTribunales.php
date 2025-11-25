<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HonorariosTribunales extends Model
{
    protected $table = 'honorarios_tribunales';

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
