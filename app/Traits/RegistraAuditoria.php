<?php

namespace App\Traits;

use App\Models\Auditoria;

trait RegistraAuditoria
{
    /**
     * Registrar una acción en auditoría
     */
    public static function registrarAuditoria($accion, $detalle = null, $tipoAccion = null, $modulo = null, $datosAnteriores = null, $datosNuevos = null, $expediente = null)
    {
        // Obtener información del usuario autenticado
        $usuarioId = null;
        $usuarioNombre = 'Sistema';
        
        if (auth()->check()) {
            $user = auth()->user();
            
            // Intentar obtener el nombre del usuario desde la tabla usuarios
            $credencial = \App\Models\Credencial::where('email', $user->email)->first();
            if ($credencial) {
                $usuario = \App\Models\Usuario::where('credencial_id', $credencial->id)->first();
                if ($usuario) {
                    $usuarioId = $usuario->id;
                    $usuarioNombre = $usuario->nombres;
                } else {
                    // Si no es usuario, puede ser partícipe
                    $participe = \App\Models\Participe::where('credencial_id', $credencial->id)->first();
                    if ($participe) {
                        $usuarioNombre = $participe->nombres;
                    }
                }
            }
        }

        return Auditoria::registrar([
            'usuario_id' => $usuarioId,
            'usuario_nombre' => $usuarioNombre,
            'expediente' => $expediente,
            'accion' => $accion,
            'detalle' => $detalle,
            'tipo_accion' => $tipoAccion,
            'modulo' => $modulo,
            'ip' => request()->ip(),
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
        ]);
    }
}
