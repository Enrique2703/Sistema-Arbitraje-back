<?php
namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\SolicitudArchivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SolicitudController extends Controller
{
    // Listar solicitudes
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 7);
        $search = $request->input('search');
        $estado = $request->input('estado');

        $query = Solicitud::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('demandante', 'like', "%$search%")
                  ->orWhere('demandado', 'like', "%$search%")
                  ->orWhere('estado', 'like', "%$search%")
                  ->orWhere('id', $search);
            });
        }
        if ($estado && $estado !== 'Todos') {
            $query->where('estado', $estado);
        }


        $solicitudes = $query->with(['participe', 'archivos'])->orderByDesc('created_at')->paginate($perPage);

        // Adaptar los datos para el frontend mostrando nombres
        $data = $solicitudes->map(function($solicitud) {
            // Buscar nombre del demandante
            $demandanteNombre = null;
            $demandadoNombre = null;
            if (is_numeric($solicitud->demandante)) {
                $demandante = \App\Models\Participe::find($solicitud->demandante);
                $demandanteNombre = $demandante ? $demandante->nombres : $solicitud->demandante;
            } else {
                $demandanteNombre = $solicitud->demandante;
            }
            if (is_numeric($solicitud->demandado)) {
                $demandado = \App\Models\Participe::find($solicitud->demandado);
                $demandadoNombre = $demandado ? $demandado->nombres : $solicitud->demandado;
            } else {
                $demandadoNombre = $solicitud->demandado;
            }
            return [
                'id' => $solicitud->id,
                'estado' => $solicitud->estado,
                'demandante' => $demandanteNombre,
                'demandado' => $demandadoNombre,
                'numero_documentos' => $solicitud->archivos->count(),
                'fecha_inicio' => $solicitud->created_at,
            ];
        });

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $solicitudes->currentPage(),
                'last_page' => $solicitudes->lastPage(),
                'per_page' => $solicitudes->perPage(),
                'total' => $solicitudes->total(),
            ]
        ]);
    }

    // Crear nueva solicitud
    public function store(Request $request)
    {
        $request->validate([
            'participe_id' => 'required|exists:participes,id',
            'estado' => 'required|string',
            'demandante' => 'required|string',
            'demandado' => 'required|string',
            'archivos.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $solicitud = Solicitud::create($request->only(['participe_id','estado','demandante','demandado']));

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $path = $archivo->store('solicitudes');
                SolicitudArchivo::create([
                    'solicitud_id' => $solicitud->id,
                    'archivo_adjunto' => $path,
                ]);
            }
        }

        return response()->json($solicitud->load('archivos'), 201);
    }

    // Mostrar una solicitud
    public function show($id)
    {
        $solicitud = Solicitud::with(['participe.credencial', 'archivos'])->findOrFail($id);

        // Demandante
        $demandanteNombre = $solicitud->demandante;
        $demandanteCorreo = null;
        if (is_numeric($solicitud->demandante)) {
            $demandante = \App\Models\Participe::with('credencial')->find($solicitud->demandante);
            if ($demandante) {
                $demandanteNombre = $demandante->nombres;
                $demandanteCorreo = $demandante->credencial ? $demandante->credencial->email : null;
            }
        }

        // Demandado
        $demandadoNombre = $solicitud->demandado;
        $demandadoCorreo = null;
        if (is_numeric($solicitud->demandado)) {
            $demandado = \App\Models\Participe::with('credencial')->find($solicitud->demandado);
            if ($demandado) {
                $demandadoNombre = $demandado->nombres;
                $demandadoCorreo = $demandado->credencial ? $demandado->credencial->email : null;
            }
        }

        return response()->json([
            'id' => $solicitud->id,
            'estado' => $solicitud->estado,
            'created_at' => $solicitud->created_at,
            'demandante' => $demandanteNombre,
            'demandante_correo' => $demandanteCorreo,
            'demandado' => $demandadoNombre,
            'demandado_correo' => $demandadoCorreo,
            'archivos' => $solicitud->archivos,
            'participe' => $solicitud->participe,
        ]);
    }

    // Eliminar una solicitud y sus archivos
    public function destroy($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        foreach ($solicitud->archivos as $archivo) {
            Storage::delete($archivo->archivo_adjunto);
            $archivo->delete();
        }
        $solicitud->delete();
        return response()->json(['message' => 'Solicitud eliminada']);
    }
}
