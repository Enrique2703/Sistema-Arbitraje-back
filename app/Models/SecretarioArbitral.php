<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecretarioArbitral extends Model
{
    protected $table = 'secretario_arbitrales';

    protected $fillable = [
        'expediente_id',
        'usuario_id',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
