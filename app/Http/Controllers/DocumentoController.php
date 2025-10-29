<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expediente;

class DocumentoController extends Controller
{
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