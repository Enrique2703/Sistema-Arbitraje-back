<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Auditoria;

class AuditoriaController extends Controller
{
    // Listar auditorías con paginación y búsqueda
    public function index(Request $request)
    {
        $query = Auditoria::query();
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('usuario_nombre', 'like', "%$search%")
                  ->orWhere('accion', 'like', "%$search%")
                  ->orWhere('detalle', 'like', "%$search%")
                  ->orWhere('ip', 'like', "%$search%")
                  ->orWhere('expediente', 'like', "%$search%");
            });
        }
        
        $perPage = $request->get('per_page', 10);
        $auditorias = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Formatear para la tabla
        $registros = $auditorias->map(function($a) {
            return [
                'id' => $a->id,
                'expediente' => $a->expediente ?? 'N/A',
                'created_at' => $a->created_at,
                'usuario' => $a->usuario_nombre ?? 'Sistema',
                'accion' => $a->accion,
            ];
        });

        return response()->json([
            'registros' => $registros,
            'meta' => [
                'current_page' => $auditorias->currentPage(),
                'last_page' => $auditorias->lastPage(),
                'per_page' => $auditorias->perPage(),
                'total' => $auditorias->total(),
            ]
        ]);
    }

    // Mostrar detalle de una auditoría
    public function show($id)
    {
        $auditoria = Auditoria::findOrFail($id);
        return response()->json($auditoria);
    }
}
