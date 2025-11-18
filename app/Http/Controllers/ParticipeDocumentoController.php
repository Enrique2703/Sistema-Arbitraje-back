<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\ParticipeDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ParticipeDocumentoController extends Controller
{
    /**
     * Mostrar una lista de documentos.
     */
    public function index(Request $request)
    {
        $query = ParticipeDocumento::with(['participe', 'expediente', 'archivos']);

        // Filtrar por expediente si se proporciona
        if ($request->has('expediente_id')) {
            $query->where('expediente_id', $request->expediente_id);
        }

        // Filtrar por partícipe si se proporciona
        if ($request->has('participe_id')) {
            $query->where('participe_id', $request->participe_id);
        }

        // Si el usuario no es admin o staff, solo mostrar documentos habilitados
        $user = Auth::user();
        if ($user) {
            // Obtener el nivel del usuario desde la relación
            $nivelUsuario = $user->tipo_usuario === 'participe' 
                ? 'participe' 
                : ($user->usuario->nivel_usuario ?? null);
            
            // Si no es administrador o staff, solo mostrar documentos habilitados
            // Comparar en minúsculas para evitar problemas de case-sensitive
            $nivelUsuarioLower = strtolower($nivelUsuario ?? '');
            if (!in_array($nivelUsuarioLower, ['administrador', 'admin', 'staff'])) {
                $query->where('habilitado', true);
            }
        }

        $documentos = $query->paginate(10);

        return response()->json([
            'registros' => $documentos->items(),
            'meta' => [
                'current_page' => $documentos->currentPage(),
                'last_page' => $documentos->lastPage(),
                'per_page' => $documentos->perPage(),
                'total' => $documentos->total(),
            ]
        ]);
    }

    /**
     * Almacenar un nuevo documento.
     */
    public function store(Request $request)
    {
        // Obtener el usuario autenticado
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'mensaje' => 'Usuario no autenticado',
                'error' => 'Debe iniciar sesión para crear documentos'
            ], 401);
        }

        // Determinar si es admin/staff o participe
        $participeId = null;
        if ($user->participe) {
            $participeId = $user->participe->id;
        } else {
            // Si no tiene participe (es admin/staff), usar el partícipe genérico "Árbitro Sistema"
            // El ID 1 corresponde al partícipe genérico creado por el seeder
            $participeId = 1;
        }
        
        $validator = Validator::make($request->all(), [
            'expediente_id' => 'required|exists:expedientes,id',
            'parte' => 'required|string|max:255',
            'sumilla' => 'nullable|string',
            'enlace_descarga' => 'nullable|string|max:255',
            'archivos.*' => 'nullable|file|max:10240', // máximo 10MB por archivo
        ]);

        if ($validator->fails()) {
            return response()->json([
                'mensaje' => 'Error de validación',
                'errores' => $validator->errors()
            ], 422);
        }

        // Iniciar transacción
        DB::beginTransaction();
        
        try {
            // Crear el documento (participe_id puede ser null para admin/staff)
            $documento = ParticipeDocumento::create([
                'participe_id' => $participeId,
                'created_by_user_id' => $user->id, // Guardar quién creó el documento
                'expediente_id' => $request->expediente_id,
                'parte' => $request->parte,
                'sumilla' => $request->sumilla,
                'enlace_descarga' => $request->enlace_descarga,
                'habilitado' => true, // Por defecto habilitado
            ]);

            // Procesar archivos adjuntos si existen
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    $path = $archivo->store('documentos/participes', 'public');
                    $tamano = $archivo->getSize(); // Obtener tamaño en bytes

                    // Crear registro de archivo
                    $documento->archivos()->create([
                        'archivo_adjunto' => $path,
                        'tamano' => $tamano
                    ]);
                }
            }

            DB::commit();

            // Registrar en historial
            HistorialController::registrar($request->expediente_id, 'Presentó un nuevo documento: ' . $request->sumilla);

            // Cargar la relación de archivos
            $documento->load('archivos');

            return response()->json([
                'mensaje' => 'Documento creado exitosamente',
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'mensaje' => 'Error al crear el documento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un documento específico.
     */
    public function show($id)
    {
        $documento = ParticipeDocumento::with(['participe', 'expediente', 'archivos'])
            ->findOrFail($id);

        return response()->json([
            'registro' => $documento
        ]);
    }

    /**
     * Actualizar un documento específico.
     */
    public function update(Request $request, $id)
    {
        $documento = ParticipeDocumento::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'participe_id' => 'sometimes|required|exists:participes,id',
            'expediente_id' => 'sometimes|required|exists:expedientes,id',
            'parte' => 'nullable|string|max:255',
            'sumilla' => 'nullable|string',
            'enlace_descarga' => 'sometimes|required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $documento->update($validator->validated());
        
        // Registrar en historial
        HistorialController::registrar($documento->expediente_id, 'Actualizó el documento: ' . $documento->sumilla);

        return response()->json([
            'mensaje' => 'Documento actualizado exitosamente',
            'registro' => $documento
        ]);
    }

    /**
     * Eliminar un documento específico.
     */
    public function destroy($id)
    {
        $documento = ParticipeDocumento::findOrFail($id);
        
        // Registrar en historial antes de eliminar
        HistorialController::registrar($documento->expediente_id, 'Eliminó el documento: ' . $documento->sumilla);
        
        $documento->delete();

        return response()->json([
            'mensaje' => 'Documento eliminado exitosamente'
        ]);
    }

    /**
     * Alternar el estado de habilitado de un documento.
     * Solo admin y staff pueden usar esta función.
     */
    public function toggleHabilitado($id)
    {
        try {
            $credencial = Auth::user();

            // Recargar la credencial con la relación usuario
            $credencial = \App\Models\Credencial::with('usuario')->find($credencial->id);

            // Obtener el nivel del usuario
            $nivelUsuario = null;
            
            if ($credencial->tipo_usuario === 'participe') {
                $nivelUsuario = 'participe';
            } elseif ($credencial->usuario) {
                $nivelUsuario = $credencial->usuario->nivel_usuario;
            }

            // Log para depuración
            Log::info('Toggle Habilitado Debug', [
                'credencial_id' => $credencial->id,
                'tipo_usuario' => $credencial->tipo_usuario,
                'tiene_usuario' => $credencial->usuario ? 'Si' : 'No',
                'nivel_usuario' => $nivelUsuario
            ]);

            // Verificar que el usuario sea administrador o staff
            // Comparar en minúsculas para evitar problemas de case-sensitive
            $nivelUsuarioLower = strtolower($nivelUsuario ?? '');
            if (!in_array($nivelUsuarioLower, ['administrador', 'admin', 'staff'])) {
                return response()->json([
                    'error' => 'No tiene permisos para realizar esta acción',
                    'debug' => [
                        'tipo_usuario' => $credencial->tipo_usuario,
                        'nivel_usuario' => $nivelUsuario,
                        'nivel_usuario_lower' => $nivelUsuarioLower
                    ]
                ], 403);
            }

            $documento = ParticipeDocumento::findOrFail($id);
            
            // Alternar el valor de habilitado
            $documento->habilitado = !$documento->habilitado;
            $documento->save();

            // Registrar en historial
            $accion = $documento->habilitado ? 'habilitó' : 'deshabilitó';
            HistorialController::registrar(
                $documento->expediente_id, 
                ucfirst($accion) . ' el documento: ' . $documento->sumilla
            );

            return response()->json([
                'mensaje' => 'Estado actualizado exitosamente',
                'habilitado' => $documento->habilitado
            ]);
        } catch (\Exception $e) {
            Log::error('Error en toggleHabilitado: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }
}
