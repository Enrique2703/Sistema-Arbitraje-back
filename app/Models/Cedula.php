<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'expedientes_id',
        'titulo',
        'comentarios',
        'enviado_a',
        'usuario_id'
    ];

    public $timestamps = true;

    /**
     * Atributos adicionales a incluir en JSON
     *
     * @var array
     */
    protected $appends = ['enviado_a_nombres'];

    /**
     * Usuario que generó la cédula
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Expediente asociado
     */
    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'expedientes_id');
    }

    /**
     * Accessor para obtener los nombres de los usuarios a quienes se envió
     */
    public function getEnviadoANombresAttribute(): string
    {
        if (empty($this->enviado_a)) {
            return '—';
        }

        $ids = explode(',', $this->enviado_a);
        $usuarios = Usuario::whereIn('id', $ids)->get();
        
        if ($usuarios->isEmpty()) {
            return '—';
        }

        return $usuarios->pluck('nombres')->implode(', ');
    }
}
