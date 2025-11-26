<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarifario extends Model
{
    protected $table = 'tarifario';

    protected $fillable = [
        'archivo_adjunto',
        'nombre_original',
    ];
    
    protected $guarded = [];
}
