<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expediente;

class DocumentoController extends Controller
{
    public function index($id)
    {
        try {
            $documentos = \App\Models\ParticipeDocumento::with(['participe'])
                ->where('expediente_id', $id)
                ->get()
                ->map(function($doc) {
                    return [
                        'id' => $doc->id,
                        'titulo' => $doc->sumilla,
                        'estado' => $doc->parte,
                        'created_at' => $doc->created_at,
                        'usuario_nombre' => $doc->participe ? $doc->participe->nombres : 'N/A',
                        'rol' => $doc->participe ? $doc->participe->tipo : 'Sistema',
                        'habilitado' => $doc->habilitado ?? false
                    ];
                });

            return response()->json([
                'documentos' => $documentos,
                'meta' => [
                    'total' => count($documentos),
                    'page' => 1,
                    'per_page' => 10
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener documentos: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'comentarios' => 'nullable|string',
                'documento' => 'required|file|max:10240', // máximo 10MB
                'expediente_id' => 'required|integer'
            ]);

            // Guardar el archivo
            $path = $request->file('documento')->store('documentos', 'public');
            
            // Crear el registro del documento
            $documento = new \App\Models\ParticipeDocumento();
            $documento->expediente_id = $request->expediente_id;
            $documento->sumilla = $request->titulo;
            $documento->parte = 'Pendiente';
            $documento->enlace_descarga = $path;
            $documento->save();

            return response()->json([
                'message' => 'Documento creado exitosamente',
                'documento' => [
                    'id' => $documento->id,
                    'titulo' => $documento->sumilla,
                    'estado' => $documento->parte,
                    'created_at' => $documento->created_at
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear documento: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Aquí implementarías la lógica para eliminar el documento
            // Por ahora solo simularemos una respuesta exitosa
            return response()->json(['message' => 'Documento eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar documento: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            // Aquí implementarías la lógica para mostrar un documento específico
            // Por ahora solo simularemos una respuesta
            return response()->json([
                'documento' => [
                    'id' => $id,
                    'titulo' => 'Documento ' . $id,
                    'estado' => 'Pendiente',
                    'created_at' => now()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener documento: ' . $e->getMessage()], 500);
        }
    }

    public function getDocumentosPorExpediente(Request $request)
    {
        try {
            $expedienteId = $request->query('expediente_id');
            
            if (!$expedienteId) {
                return response()->json([
                    'status' => false,
                    'message' => 'ID de expediente no proporcionado'
                ], 400);
            }

            // Aquí simularemos algunos documentos de ejemplo
            // En un caso real, obtendrías esto de tu base de datos
            $documentos = [
                [
                    'id' => 1,
                    'participe' => ['nombres' => 'Nombre Apellido'],
                    'condicion' => 'Demandante',
                    'asunto' => 'Demanda A B C',
                    'fecha_presentacion' => now()->format('Y-m-d H:i:s'),
                    'proveido' => 'Resolución 22',
                    'fecha_proveido' => now()->format('Y-m-d H:i:s'),
                    'resolucion' => true,
                    'cedula' => true,
                    'nombre_archivo' => 'documento1.pdf'
                ],
                [
                    'id' => 2,
                    'participe' => ['nombres' => 'Nombre Apellido'],
                    'condicion' => 'Demandante',
                    'asunto' => 'Demanda A B C D',
                    'fecha_presentacion' => now()->format('Y-m-d H:i:s'),
                    'proveido' => null,
                    'fecha_proveido' => null,
                    'resolucion' => false,
                    'cedula' => false,
                    'nombre_archivo' => 'documento2.pdf'
                ]
            ];

            return response()->json([
                'status' => true,
                'message' => 'Documentos obtenidos correctamente',
                'registros' => $documentos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los documentos: ' . $e->getMessage()
            ], 500);
        }
    }
}