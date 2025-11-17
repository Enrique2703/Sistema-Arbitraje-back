<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParticipeDocumento extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'participe_documentos';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'participe_id',
        'expediente_id',
        'parte',
        'sumilla',
        'enlace_descarga',
        'habilitado',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'habilitado' => 'boolean',
    ];

    /**
     * Obtiene el partícipe asociado al documento.
     */
    public function participe(): BelongsTo
    {
        return $this->belongsTo(Participe::class);
    }

    /**
     * Obtiene el expediente asociado al documento.
     */
    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class);
    }

    /**
     * Obtiene los archivos asociados al documento.
     */
    public function archivos(): HasMany
    {
        return $this->hasMany(ParticipeDocumentoArchivo::class, 'participe_documentos_id');
    }
}