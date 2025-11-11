<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CedulaCorreo extends Model
{
    /**
     * Tabla asociada
     *
     * @var string
     */
    protected $table = 'cedula_correos';

    /**
     * Atributos asignables
     *
     * @var array
     */
    protected $fillable = [
        'usuario_id',
        'cedulas_id'
    ];

    /**
     * Usuario asociado
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Cédula asociada
     */
    public function cedula(): BelongsTo
    {
        return $this->belongsTo(Cedula::class, 'cedulas_id');
    }
}
