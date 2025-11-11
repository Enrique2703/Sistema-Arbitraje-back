<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cedula extends Model
{
    /**
     * Tabla asociada
     *
     * @var string
     */
    protected $table = 'cedulas';

    /**
     * Atributos asignables
     *
     * @var array
     */
    protected $fillable = [
        'documentos_id',
        'comentarios',
        'enviado_a',
        'usuario_id'
    ];

    /**
     * Documento asociado (participe_documentos)
     */
    public function documento(): BelongsTo
    {
        return $this->belongsTo(ParticipeDocumento::class, 'documentos_id');
    }

    /**
     * Usuario que generó la cédula
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
