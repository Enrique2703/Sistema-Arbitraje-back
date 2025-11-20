<?php

namespace App\Traits;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait RegistraAuditoria
{
    /**
     * Register an action in the audit log
     */
    public static function registrarAuditoria($accion, $detalle = null, $tipoAccion = null, $modulo = null, $datosAnteriores = null, $datosNuevos = null, $expediente = null)
    {
        try {
            // Get authenticated user information
            $usuarioId = null;
                    if (Auth::check()) {
                        $user = Auth::user();
                        
                        // Try to get the user's name from the users table
                        $credencial = \App\Models\Credencial::where('email', $user->email)->first();
                        if ($credencial) {
                            $usuario = \App\Models\Usuario::where('credencial_id', $credencial->id)->first();
                            if ($usuario) {
                                $usuarioId = $usuario->id;
                                $usuarioNombre = $usuario->nombres;
                            } else {
                                // If not a user, it may be a participant
                                $participe = \App\Models\Participe::where('credencial_id', $credencial->id)->first();
                                if ($participe) {
                                    $usuarioNombre = $participe->nombres;
                                    // For participants, usuario_id will be null (after running the migration)
                                    $usuarioId = null;
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
                } catch (\Exception $e) {
                    // Log the error but don't block the main operation
                    Log::error('Error al registrar auditoría: ' . $e->getMessage(), [
                        'accion' => $accion,
                        'detalle' => $detalle
                    ]);
                    return null;
                }
    }
}
