<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\HistorialController;
use Illuminate\Support\Facades\Auth;

class RegistrarHistorialMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Solo registrar si el usuario está autenticado y es admin/staff
        if (Auth::check()) {
            $user = Auth::user();
            
            // Verificar si el usuario tiene nivel de administrador o staff
            if ($this->esUsuarioAdministrativo($user)) {
                $this->registrarAccion($request, $response);
            }
        }

        return $response;
    }

    private function esUsuarioAdministrativo($user)
    {
        // Verificar nivel de usuario (ajusta según tu estructura)
        if (isset($user->nivel_usuario)) {
            return in_array(strtolower($user->nivel_usuario), ['administrador', 'admin', 'staff']);
        }
        
        // Si no hay campo nivel_usuario, verificar por otros criterios
        return false;
    }

    private function registrarAccion(Request $request, $response)
    {
        $method = $request->method();
        $path = $request->path();
        
        // Solo registrar operaciones importantes
        if (!$this->debeRegistrarAccion($method, $path, $response)) {
            return;
        }

        $expedienteId = $this->extraerExpedienteId($request);
        if (!$expedienteId) {
            return;
        }

        $accion = $this->generarDescripcionAccion($method, $path, $request, $response);
        
        // Registrar la acción
        HistorialController::registrar($expedienteId, $accion);
    }

    private function debeRegistrarAccion($method, $path, $response)
    {
        // Solo registrar POST, PUT, DELETE exitosos
        if (!in_array($method, ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            return false;
        }

        // Solo registrar si la respuesta fue exitosa
        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            return false;
        }

        // Excluir rutas que no necesitan historial
        $rutasExcluidas = [
            'api/login',
            'api/logout',
            'api/historial',
            'historial/export'
        ];

        foreach ($rutasExcluidas as $rutaExcluida) {
            if (strpos($path, $rutaExcluida) !== false) {
                return false;
            }
        }

        return true;
    }

    private function extraerExpedienteId(Request $request)
    {
        // Intentar obtener el ID del expediente de varias formas
        
        // 1. Del parámetro expediente_id en el body
        if ($request->has('expediente_id')) {
            return $request->expediente_id;
        }

        // 2. De la URL (para rutas como /api/expedientes/{id})
        $path = $request->path();
        if (preg_match('/api\/expedientes\/(\d+)/', $path, $matches)) {
            return $matches[1];
        }

        // 3. Para documentos de partícipes, buscar el expediente
        if (strpos($path, 'participe-documentos') !== false) {
            if ($request->has('expediente_id')) {
                return $request->expediente_id;
            }
        }

        // 4. Para cédulas, buscar el expediente
        if (strpos($path, 'cedulas') !== false) {
            if ($request->has('expediente_id')) {
                return $request->expediente_id;
            }
        }

        return null;
    }

    private function generarDescripcionAccion($method, $path, $request, $response)
    {
        $usuario = Auth::user();
        $nombreUsuario = $usuario->nombres ?? 'Usuario';

        // Generar descripción según el tipo de operación
        switch ($method) {
            case 'POST':
                return $this->generarAccionCreacion($path, $request, $nombreUsuario);
            
            case 'PUT':
            case 'PATCH':
                return $this->generarAccionActualizacion($path, $request, $nombreUsuario);
            
            case 'DELETE':
                return $this->generarAccionEliminacion($path, $request, $nombreUsuario);
            
            default:
                return "Realizó una acción en " . $path;
        }
    }

    private function generarAccionCreacion($path, $request, $nombreUsuario)
    {
        if (strpos($path, 'expedientes') !== false) {
            return "Creó un nuevo expediente: " . ($request->numero ?? 'Sin número');
        }
        
        if (strpos($path, 'participe-documentos') !== false) {
            return "Presentó un nuevo documento: " . ($request->sumilla ?? 'Sin sumilla');
        }
        
        if (strpos($path, 'cedulas') !== false) {
            return "Generó una nueva cédula: " . ($request->titulo ?? 'Sin título');
        }
        
        if (strpos($path, 'usuarios') !== false) {
            return "Creó un nuevo usuario: " . ($request->nombres ?? 'Sin nombre');
        }
        
        if (strpos($path, 'participes') !== false) {
            return "Registró un nuevo partícipe: " . ($request->nombres ?? 'Sin nombre');
        }

        return "Creó un nuevo registro";
    }

    private function generarAccionActualizacion($path, $request, $nombreUsuario)
    {
        if (strpos($path, 'expedientes') !== false) {
            return "Actualizó el expediente: " . ($request->numero ?? 'Sin número');
        }
        
        if (strpos($path, 'participe-documentos') !== false) {
            return "Modificó un documento: " . ($request->sumilla ?? 'Sin sumilla');
        }
        
        if (strpos($path, 'usuarios') !== false) {
            return "Actualizó un usuario: " . ($request->nombres ?? 'Sin nombre');
        }
        
        if (strpos($path, 'participes') !== false) {
            return "Modificó un partícipe: " . ($request->nombres ?? 'Sin nombre');
        }

        return "Actualizó un registro";
    }

    private function generarAccionEliminacion($path, $request, $nombreUsuario)
    {
        if (strpos($path, 'expedientes') !== false) {
            return "Eliminó un expediente";
        }
        
        if (strpos($path, 'participe-documentos') !== false) {
            return "Eliminó un documento";
        }
        
        if (strpos($path, 'usuarios') !== false) {
            return "Eliminó un usuario";
        }
        
        if (strpos($path, 'participes') !== false) {
            return "Eliminó un partícipe";
        }

        return "Eliminó un registro";
    }
}