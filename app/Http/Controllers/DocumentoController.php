<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expediente;

class DocumentoController extends Controller
{
    public function index($id)
    {
        try {
            $documentos = \App\Models\ParticipeDocumento::with(['participe', 'createdByUser.participe', 'createdByUser.usuario', 'archivos'])
                ->where('expediente_id', $id)
                ->get()
                ->map(function($doc) {
                    // Determinar el nombre del usuario y su rol
                    $nombreUsuario = 'N/A';
                    $rol = 'Sistema';
                    
                    // Si hay un usuario creador
                    if ($doc->createdByUser) {
                        // Si es un partícipe (usuario normal)
                        if ($doc->createdByUser->participe) {
                            $nombreUsuario = $doc->createdByUser->participe->nombres;
                            $rol = $doc->parte; // Usar el rol del documento
                        }
                        // Si es admin/staff
                        elseif ($doc->createdByUser->usuario) {
                            $nombreUsuario = $doc->createdByUser->usuario->nombres;
                            $rol = 'Árbitro'; // Admin/staff siempre es Árbitro
                        }
                    }
                    // Fallback al participe si no hay usuario creador
                    elseif ($doc->participe) {
                        $nombreUsuario = $doc->participe->nombres;
                        $rol = $doc->parte;
                    }
                    
                    return [
                        'id' => $doc->id,
                        'titulo' => $doc->sumilla,
                        'estado' => $rol,
                        'created_at' => $doc->created_at,
                        'usuario_nombre' => $nombreUsuario,
                        'rol' => $rol,
                        'habilitado' => $doc->habilitado ?? false,
                        'revisado' => $doc->revisado ?? false,
                        'archivos' => $doc->archivos ? $doc->archivos->map(function($archivo) {
                            // Si no tiene tamaño guardado, calcularlo del archivo físico
                            $tamano = $archivo->tamano;
                            if (!$tamano || $tamano == 0) {
                                $rutaCompleta = storage_path('app/public/' . $archivo->archivo_adjunto);
                                if (file_exists($rutaCompleta)) {
                                    $tamano = filesize($rutaCompleta);
                                    // Actualizar en base de datos para futuros requests
                                    $archivo->update(['tamano' => $tamano]);
                                }
                            }
                            return [
                                'id' => $archivo->id,
                                'archivo_adjunto' => $archivo->archivo_adjunto,
                                'tamano' => $tamano ?? 0
                            ];
                        }) : []
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
            $file = $request->file('documento');
            $path = $file->store('documentos', 'public');
            $tamano = $file->getSize(); // Obtener tamaño en bytes
            
            // Crear el registro del documento
            $documento = new \App\Models\ParticipeDocumento();
            $documento->expediente_id = $request->expediente_id;
            $documento->sumilla = $request->titulo;
            $documento->parte = 'Pendiente';
            $documento->enlace_descarga = $path;
            $documento->save();
            
            // Crear registro del archivo con tamaño
            $documento->archivos()->create([
                'archivo_adjunto' => $path,
                'tamano' => $tamano
            ]);

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
            $documento = \App\Models\ParticipeDocumento::findOrFail($id);
            $datosAnteriores = $documento->toArray();
            $expediente = $documento->expediente;
            $expedienteNombre = $expediente ? ($expediente->numero . ' - ' . $expediente->anio . '/' . $expediente->codigo) : null;

            $documento->delete();

            // Registrar en auditoría
            if (in_array(auth()->user()->tipo_usuario ?? '', ['admin', 'staff', 'arbitro'])) {
                \App\Traits\RegistraAuditoria::registrarAuditoria(
                    'Documento eliminado',
                    'Se eliminó un documento de expediente',
                    'eliminar',
                    'documentos',
                    $datosAnteriores,
                    null,
                    $expedienteNombre
                );
            }

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