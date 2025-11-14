<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use App\Models\Expediente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistorialController extends Controller
{
    public function index(Request $request, $expedienteId)
    {
        try {
            $query = Historial::with('usuario')
                ->where('expediente_id', $expedienteId);

            // Búsqueda
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('accion', 'like', "%{$search}%")
                        ->orWhereHas('usuario', function($userQuery) use ($search) {
                           $userQuery->where('nombre', 'like', "%{$search}%");
                        });
                });
            }

            $registros = $query->orderBy('created_at', 'desc')
                        ->paginate(10);

            // Transformar datos para incluir nombre del usuario
            $registros->getCollection()->transform(function ($item) {
                $item->usuario_nombre = $item->usuario ? $item->usuario->nombres : 'Sistema';
                return $item;
            });

            return response()->json([
                'status' => true,
                'registros' => $registros->items(),
                'meta' => [
                    'current_page' => $registros->currentPage(),
                    'last_page' => $registros->lastPage(),
                    'per_page' => $registros->perPage(),
                    'total' => $registros->total()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al cargar el historial: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, $expedienteId, $historialId)
    {
        try {
            $registro = Historial::with('usuario', 'expediente')
                ->where('expediente_id', $expedienteId)
                ->findOrFail($historialId);

            return response()->json([
                'status' => true,
                'registro' => $registro
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al cargar el detalle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método helper para registrar acciones en el historial
     */
    public static function registrar($expedienteId, $accion, $usuarioId = null)
    {
        try {
            Historial::create([
                'usuario_id' => $usuarioId ?? Auth::id(),
                'expediente_id' => $expedienteId,
                'accion' => $accion,
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the main flow
            \Log::error('Error al registrar historial: ' . $e->getMessage());
        }
    }

    public function export(Request $request, $expedienteId)
    {
        try {
            $registros = Historial::with('usuario')
                ->where('expediente_id', $expedienteId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Registrar la exportación
            self::registrar($expedienteId, 'Exportó el historial del expediente');

            // Crear archivo Excel (puedes usar Laravel Excel o crear CSV)
            $filename = "historial_expediente_{$expedienteId}_" . now()->format('Y-m-d') . ".csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($registros) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Fecha', 'Hora', 'Usuario', 'Acción']);

                foreach ($registros as $registro) {
                    fputcsv($file, [
                        $registro->created_at->format('d/m/Y'),
                        $registro->created_at->format('H:i:s'),
                        $registro->usuario ? $registro->usuario->nombre : 'Sistema',
                        $registro->accion
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error al exportar: ' . $e->getMessage()
            ], 500);
        }
    }
}