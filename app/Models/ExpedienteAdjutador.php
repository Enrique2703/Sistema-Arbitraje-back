<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpedienteAdjutador extends Model
{
    protected $table = 'expediente_adjutadores';

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
