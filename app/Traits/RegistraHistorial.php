<?php

namespace App\Traits;

use App\Http\Controllers\HistorialController;
use Illuminate\Support\Facades\Auth;

trait RegistraHistorial
{
    /**
     * Registrar una acción administrativa en el historial
     */
    protected function registrarAccionAdmin($expedienteId, $accion, $detalles = [])
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();
        
        // Verificar que sea un usuario administrativo
        if (!$this->esUsuarioAdministrativo($user)) {
            return;
        }

        $nombreUsuario = $user->nombres ?? 'Administrador';
        $accionCompleta = "[ADMIN] {$nombreUsuario}: {$accion}";
        
        // Agregar detalles si existen
        if (!empty($detalles)) {
            $accionCompleta .= " - " . implode(', ', $detalles);
        }

        HistorialController::registrar($expedienteId, $accionCompleta);
    }

    /**
     * Registrar cambio de estado de expediente
     */
    protected function registrarCambioEstado($expedienteId, $estadoAnterior, $estadoNuevo)
    {
        $accion = "Cambió el estado del expediente de '{$estadoAnterior}' a '{$estadoNuevo}'";
        $this->registrarAccionAdmin($expedienteId, $accion);
    }

    /**
     * Registrar asignación de usuarios a expediente
     */
    protected function registrarAsignacionUsuarios($expedienteId, $tipo, $usuarios)
    {
        $nombresUsuarios = is_array($usuarios) ? implode(', ', $usuarios) : $usuarios;
        $accion = "Asignó {$tipo}: {$nombresUsuarios}";
        $this->registrarAccionAdmin($expedienteId, $accion);
    }

    /**
     * Registrar exportación de datos
     */
    protected function registrarExportacion($expedienteId, $tipoExportacion)
    {
        $accion = "Exportó {$tipoExportacion}";
        $this->registrarAccionAdmin($expedienteId, $accion);
    }

    /**
     * Registrar acciones masivas
     */
    protected function registrarAccionMasiva($expedienteIds, $accion)
    {
        if (is_array($expedienteIds)) {
            foreach ($expedienteIds as $id) {
                $this->registrarAccionAdmin($id, "ACCIÓN MASIVA: {$accion}");
            }
        } else {
            $this->registrarAccionAdmin($expedienteIds, "ACCIÓN MASIVA: {$accion}");
        }
    }

    /**
     * Verificar si el usuario es administrativo
     */
    private function esUsuarioAdministrativo($user)
    {
        if (isset($user->nivel_usuario)) {
            return in_array(strtolower($user->nivel_usuario), ['administrador', 'admin', 'staff']);
        }
        
        return false;
    }

    /**
     * Registrar acceso a información sensible
     */
    protected function registrarAccesoSensible($expedienteId, $recurso)
    {
        $accion = "Accedió a información sensible: {$recurso}";
        $this->registrarAccionAdmin($expedienteId, $accion);
    }

    /**
     * Registrar modificación de configuraciones
     */
    protected function registrarCambioConfiguracion($expedienteId, $configuracion, $valorAnterior, $valorNuevo)
    {
        $accion = "Modificó {$configuracion}: de '{$valorAnterior}' a '{$valorNuevo}'";
        $this->registrarAccionAdmin($expedienteId, $accion);
    }
}