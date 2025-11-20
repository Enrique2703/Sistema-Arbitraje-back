<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditorias';

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'usuario_id',
        'usuario_nombre',
        'expediente',
        'accion',
        'detalle',
        'tipo_accion',
        'modulo',
        'ip',
        'datos_anteriores',
        'datos_nuevos'
    ];

    /**
     * Obtiene el usuario que realizó la acción.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Registrar una acción en auditoría
     */
    public static function registrar($data)
    {
        return self::create([
            'usuario_id' => $data['usuario_id'] ?? null,
            'usuario_nombre' => $data['usuario_nombre'] ?? 'Sistema',
            'expediente' => $data['expediente'] ?? null,
            'accion' => $data['accion'],
            'detalle' => $data['detalle'] ?? null,
            'tipo_accion' => $data['tipo_accion'] ?? null,
            'modulo' => $data['modulo'] ?? null,
            'ip' => $data['ip'] ?? request()->ip(),
            'datos_anteriores' => isset($data['datos_anteriores']) ? json_encode($data['datos_anteriores']) : null,
            'datos_nuevos' => isset($data['datos_nuevos']) ? json_encode($data['datos_nuevos']) : null,
        ]);
    }
}