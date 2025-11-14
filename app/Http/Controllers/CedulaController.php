<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cedula;
use App\Models\CedulaCorreo;
use App\Models\ParticipeDocumento;

class CedulaController extends Controller
{

    public function index(Request $request)
    {
        $query = Cedula::query()->with(['usuario', 'expediente']);

        if ($request->has('expediente_id')) {
            $query->where('expedientes_id', $request->get('expediente_id'));
        }

        if ($request->has('expedientes_id')) {
            $expedienteId = (int) $request->get('expedientes_id');
            $query->where('cedulas.expedientes_id', $expedienteId)
                ->select('cedulas.*');
        }

        $perPage = $request->get('per_page', 10);

        // Aplicar paginación
        $cedulasPaginated = $query->orderBy('cedulas.created_at', 'desc')->paginate($perPage);

        return response()->json([
            'status' => true,
            'registros' => $cedulasPaginated->items(),
            'meta' => [
                'current_page' => $cedulasPaginated->currentPage(),
                'last_page' => $cedulasPaginated->lastPage(),
                'per_page' => $cedulasPaginated->perPage(),
                'total' => $cedulasPaginated->total(),
            ],
        ]);
    }


    /**
     * Mostrar una cédula con sus correos.
     *
     * @param int $id
     */
    public function show(int $id)
    {
        $cedula = Cedula::with(['usuario', 'expediente'])->find($id);
        if (! $cedula) {
            return response()->json(['status' => false, 'message' => 'Cédula no encontrada'], 404);
        }

        $correos = CedulaCorreo::with('usuario')->where('cedulas_id', $cedula->id)->get();

        return response()->json(['status' => true, 'cedula' => $cedula, 'correos' => $correos]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'expediente_id' => 'required|integer|exists:expedientes,id',
            'titulo' => 'nullable|string|max:255',
            'comentarios' => 'nullable|string',
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'integer|exists:usuarios,id'
        ]);

        DB::beginTransaction();

        // Obtener el usuario autenticado desde la tabla usuarios usando el email del token
        $usuarioId = null;
        if ($request->user()) {
            $credencial = \App\Models\Credencial::where('email', $request->user()->email)->first();
            if ($credencial) {
                $usuario = \App\Models\Usuario::where('credencial_id', $credencial->id)->first();
                $usuarioId = $usuario ? $usuario->id : null;
            }
        }

        $cedula = Cedula::create([
            'expedientes_id' => $request->get('expediente_id'),
            'titulo' => $request->get('titulo'),
            'comentarios' => $request->get('comentarios'),
            'enviado_a' => $request->has('usuarios') ? implode(',', $request->get('usuarios')) : null,
            'usuario_id' => $usuarioId
        ]);

        // Registrar en historial
        HistorialController::registrar($request->get('expediente_id'), 'Generó una cédula: ' . $request->get('titulo'));

        if ($request->has('usuarios') && is_array($request->get('usuarios'))) {
            foreach ($request->get('usuarios') as $uid) {
                CedulaCorreo::create([
                    'usuario_id' => $uid,
                    'cedulas_id' => $cedula->id
                ]);
            }
        }

            DB::commit();

            // Cargar relaciones para devolver en la respuesta
            $cedula->load(['usuario', 'expediente']);
            $correos = CedulaCorreo::with('usuario')->where('cedulas_id', $cedula->id)->get();
            $cedula->correos = $correos;

            return response()->json([
                'status' => true,
                'id' => $cedula->id,
                'cedula' => $cedula,
            ], 201);
    }


    public function destroy(int $id)
    {
        DB::beginTransaction();

        $cedula = Cedula::find($id);
        if (! $cedula) {
            return response()->json(['status' => false, 'message' => 'Cédula no encontrada'], 404);
        }

        // Eliminar correos relacionados
        CedulaCorreo::where('cedulas_id', $cedula->id)->delete();
        $cedula->delete();

        DB::commit();
        return response()->json(['status' => true, 'message' => 'Cédula eliminada']);
    }
}
