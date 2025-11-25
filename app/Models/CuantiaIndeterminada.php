<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuantiaIndeterminada extends Model
{
    protected $table = 'cuantia_indeterminada';

    protected $fillable = [
        'porcentaje_arbitro',
        'porcentaje_secretario',
        'porcentaje_nulidad',
        'porcentaje_resolucion',
        'porcentaje_tarifa',
    ];
}
