<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipeDocumentoArchivo extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'participe_documento_archivos';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'participe_documentos_id',
        'archivo_adjunto',
    ];

    /**
     * Obtiene el documento de partícipe asociado al archivo.
     */
    public function participeDocumento(): BelongsTo
    {
        return $this->belongsTo(ParticipeDocumento::class, 'participe_documentos_id');
    }
}