<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastosAdministrativos extends Model
{
    protected $table = 'gastos_administrativos';

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
