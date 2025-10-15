<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'credencial_id',
        'nombres',
        'nivel_usuario',
        'estado'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function credencial()
    {
        return $this->belongsTo(Credencial::class, 'credencial_id');
    }

    /**
     * Obtiene las auditorías realizadas por el usuario.
     */
    public function auditorias()
    {
        return $this->hasMany(Auditoria::class);
    }
}
