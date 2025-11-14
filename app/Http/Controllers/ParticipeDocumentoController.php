<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\ParticipeDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        
        // Verificar si el usuario tiene un partícipe asociado
        if (!$user || !$user->participe) {
            return response()->json([
                'mensaje' => 'Usuario no tiene un partícipe asociado',
                'error' => 'No se puede crear el documento sin un partícipe'
            ], 403);
        }

        $participe = $user->participe;
        
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
            // Crear el documento
            $documento = ParticipeDocumento::create([
                'participe_id' => $participe->id,
                'expediente_id' => $request->expediente_id,
                'parte' => $request->parte,
                'sumilla' => $request->sumilla,
                'enlace_descarga' => $request->enlace_descarga,
            ]);

            // Procesar archivos adjuntos si existen
            if ($request->hasFile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    $path = $archivo->store('documentos/participes', 'public');

                    // Crear registro de archivo
                    $documento->archivos()->create([
                        'archivo_adjunto' => $path
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
}
