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
        try {
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
                            // Para partícipes, usar un ID temporal o dejar null
                            // Como la columna no acepta NULL, usar 0 o crear un usuario especial
                            $usuarioId = 0; // Temporal - se debe crear un usuario "Partícipe" con ID=0 o hacer la columna nullable
                        }
                    }
                }
            }

            // Si no hay usuario_id, no registrar en auditoría para evitar errores
            if ($usuarioId === null) {
                \Log::warning('No se pudo registrar auditoría: usuario_id es null', [
                    'accion' => $accion,
                    'usuario_nombre' => $usuarioNombre
                ]);
                return null;
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
        } catch (\Exception $e) {
            // Log del error pero no bloquear la operación principal
            \Log::error('Error al registrar auditoría: ' . $e->getMessage(), [
                'accion' => $accion,
                'detalle' => $detalle
            ]);
            return null;
        }
    }
}
