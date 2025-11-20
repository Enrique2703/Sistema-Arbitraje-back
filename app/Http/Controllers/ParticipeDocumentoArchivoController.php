<?php

namespace App\Http\Controllers;

use App\Models\ParticipeDocumentoArchivo;
use App\Models\Expediente;
use App\Traits\RegistraAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ParticipeDocumentoArchivoController extends Controller
{
    use RegistraAuditoria;

    /**
     * Descargar un archivo específico.
     */
    public function download($id)
    {
        $archivo = ParticipeDocumentoArchivo::with('participeDocumento.expediente')->findOrFail($id);
        $ruta = $archivo->archivo_adjunto;
        $nombre = basename($ruta);
        if (!Storage::disk('public')->exists($ruta)) {
            abort(404, 'Archivo no encontrado');
        }

        // Registrar descarga en auditoría
        $expediente = $archivo->participeDocumento->expediente ?? null;
        $expedienteNombre = $expediente ? "{$expediente->numero} - {$expediente->anio}/{$expediente->codigo}" : null;
        
        self::registrarAuditoria(
            'Archivo descargado: ' . $nombre,
            'Se descargó el archivo del documento: ' . ($archivo->participeDocumento->sumilla ?? 'N/A'),
            'descargar',
            'documentos',
            null,
            ['archivo' => $nombre, 'ruta' => $ruta],
            $expedienteNombre
        );

        return response()->download(Storage::disk('public')->path($ruta), $nombre);
    }
    /**
     * Mostrar una lista de archivos.
     */
    public function index(Request $request)
    {
        $query = ParticipeDocumentoArchivo::with('participeDocumento');
        
        // Filtrar por documento si se proporciona
        if ($request->has('participe_documentos_id')) {
            $query->where('participe_documentos_id', $request->participe_documentos_id);
        }

        $archivos = $query->paginate(10);
        
        return response()->json([
            'registros' => $archivos->items(),
            'meta' => [
                'current_page' => $archivos->currentPage(),
                'last_page' => $archivos->lastPage(),
                'per_page' => $archivos->perPage(),
                'total' => $archivos->total(),
            ]
        ]);
    }

    /**
     * Almacenar un nuevo archivo.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'participe_documentos_id' => 'required|exists:participe_documentos,id',
            'archivo' => 'required|file|max:10240', // máximo 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $path = $archivo->store('documentos/participes', 'public');

            $documentoArchivo = ParticipeDocumentoArchivo::create([
                'participe_documentos_id' => $request->participe_documentos_id,
                'archivo_adjunto' => $path
            ]);

            return response()->json([
                'mensaje' => 'Archivo subido exitosamente',
                'registro' => $documentoArchivo
            ], 201);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No se pudo procesar el archivo'
        ], 422);
    }

    /**
     * Mostrar un archivo específico.
     */
    public function show($id)
    {
        $archivo = ParticipeDocumentoArchivo::with('participeDocumento')
            ->findOrFail($id);

        return response()->json([
            'registro' => $archivo
        ]);
    }

    /**
     * Actualizar un archivo específico.
     */
    public function update(Request $request, $id)
    {
        $archivo = ParticipeDocumentoArchivo::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'participe_documentos_id' => 'sometimes|required|exists:participe_documentos,id',
            'archivo' => 'sometimes|required|file|max:10240', // máximo 10MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->hasFile('archivo')) {
            // Eliminar archivo anterior
            if (Storage::disk('public')->exists($archivo->archivo_adjunto)) {
                Storage::disk('public')->delete($archivo->archivo_adjunto);
            }

            // Guardar nuevo archivo
            $nuevoArchivo = $request->file('archivo');
            $path = $nuevoArchivo->store('documentos/participes', 'public');
            
            $archivo->archivo_adjunto = $path;
        }

        if ($request->has('participe_documentos_id')) {
            $archivo->participe_documentos_id = $request->participe_documentos_id;
        }

        $archivo->save();

        return response()->json([
            'mensaje' => 'Archivo actualizado exitosamente',
            'registro' => $archivo
        ]);
    }

    /**
     * Eliminar un archivo específico.
     */
    public function destroy($id)
    {
        $archivo = ParticipeDocumentoArchivo::findOrFail($id);
        
        // Eliminar archivo físico
        if (Storage::disk('public')->exists($archivo->archivo_adjunto)) {
            Storage::disk('public')->delete($archivo->archivo_adjunto);
        }

        $archivo->delete();

        return response()->json([
            'mensaje' => 'Archivo eliminado exitosamente'
        ]);
    }

    /**
     * Descargar un archivo específico.
     */

}