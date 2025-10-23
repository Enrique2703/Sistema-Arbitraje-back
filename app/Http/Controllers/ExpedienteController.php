<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\ExpedienteArbitro;
use App\Models\ExpedienteAdjutador;
use App\Models\ExpedienteParticipe;
use App\Models\ExpedienteFechaLaudo;
use App\Models\ExpedienteFechaResolucion;
use App\Models\SecretarioTecnico;
use Illuminate\Http\Request;


class ExpedienteController extends Controller
{
    /**
     * Listar todos los expedientes
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);
        $search = $request->query('search');
        $estado = $request->query('estado'); // 🟢 Nuevo parámetro de filtro por estado

        $query = Expediente::with('participes.participe')->orderBydesc('id');

        // 🔍 Búsqueda por ID, etapa_procesal o tipo_proceso
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('etapa_procesal', 'like', "%$search%")
                    ->orWhere('tipo_proceso', 'like', "%$search%");
            });
        }

        // 🟢 Filtro por estado (si no es "Todos" ni vacío)
        if ($estado && $estado !== 'Todos') {
            $query->where('estado', $estado);
        }

        // 🔹 Paginación
        $expedientesPaginated = $query->paginate($perPage);

        // 🔹 Transformar los datos antes de enviar al frontend
        $registros = $expedientesPaginated->map(function ($expediente) {
            return [
                'id' => $expediente->id,
                'estado' => $expediente->estado ?? 'Sin estado',
                'cantidad_participes' => $expediente->participes->count(),
                'expediente' => 0,
                'fecha_creacion' => $expediente->created_at ? $expediente->created_at->format('Y-m-d') : null,
                'fecha_actualizacion' => $expediente->updated_at ? $expediente->updated_at->format('Y-m-d') : null,
            ];
        });

        return response()->json([
            'registros' => $registros,
            'meta' => [
                'current_page' => $expedientesPaginated->currentPage(),
                'last_page' => $expedientesPaginated->lastPage(),
                'per_page' => $expedientesPaginated->perPage(),
                'total' => $expedientesPaginated->total(),
            ],
        ]);
    }



    /**
     * Crear un nuevo expediente
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'nombre' => 'required|string|max:255',
            'numero' => 'required|string|max:50',
            'anio' => 'required|integer',
            'codigo' => 'nullable|string|max:255',
            'etapa_procesal' => 'nullable|string|max:255',
            'inicio_proceso' => 'nullable|date',
            'tipo_proceso' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'arbitros' => 'array',
            'adjutadores' => 'array',
            'secretarios_tecnicos' => 'array', // ✅ nuevo campo
            'participes' => 'array',
        ]);

        $expediente = Expediente::create($validated);

        // Árbitros
        if ($request->filled('arbitros')) {
            foreach ($request->arbitros as $arbitroId) {
                ExpedienteArbitro::create([
                    'expediente_id' => $expediente->id,
                    'usuario_id' => $arbitroId,
                ]);
            }
        }

        // Adjutadores
        if ($request->filled('adjutadores')) {
            foreach ($request->adjutadores as $adjutadorId) {
                ExpedienteAdjutador::create([
                    'expediente_id' => $expediente->id,
                    'usuario_id' => $adjutadorId,
                ]);
            }
        }

        // ✅ Secretarios Técnicos
        if ($request->filled('secretarios_tecnicos')) {
            foreach ($request->secretarios_tecnicos as $secretarioId) {
                SecretarioTecnico::create([
                    'expediente_id' => $expediente->id,
                    'usuario_id' => $secretarioId,
                ]);
            }
        }

        // Partícipes
        if ($request->filled('participes')) {
            foreach ($request->participes as $p) {
                ExpedienteParticipe::create([
                    'expediente_id' => $expediente->id,
                    'participe_id' => $p['id'],
                    'condicion' => $p['condicion'] ?? null,
                ]);
            }
        }

        // Fechas
        if ($request->filled('fecha_laudo')) {
            ExpedienteFechaLaudo::create([
                'expediente_id' => $expediente->id,
                'fecha' => $request->fecha_laudo,
            ]);
        }

        if ($request->filled('fecha_resolucion')) {
            ExpedienteFechaResolucion::create([
                'expediente_id' => $expediente->id,
                'fecha' => $request->fecha_resolucion,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Expediente creado correctamente',
            'data' => $expediente->load([
                'usuario',
                'arbitros.usuario',
                'adjutadores.usuario',
                'secretariosTecnicos.usuario', // ✅ agregado aquí
                'participes.participe',
                'fechaLaudo',
                'fechaResolucion',
            ])
        ], 201);
    }


    /**
     * Mostrar un expediente específico
     */
    public function show($id)
    {
        $expediente = Expediente::with([
            'usuario',
            'arbitros.usuario',
            'adjutadores.usuario',
            'secretariosTecnicos.usuario',
            'participes.participe',
            'fechaLaudo',
            'fechaResolucion',
        ])->find($id);

        if (!$expediente) {
            return response()->json([
                'status' => false,
                'message' => 'Expediente no encontrado'
            ], 404);
        }

        // Simplificar solo las fechas
        $data = $expediente->toArray();
        $data['fecha_laudo'] = $expediente->fechaLaudo->fecha ?? null;
        $data['fecha_resolucion'] = $expediente->fechaResolucion->fecha ?? null;

        return response()->json([
            'status' => true,
            'message' => 'Expediente obtenido correctamente',
            'data' => $data
        ]);
    }



    /**
     * Actualizar expediente
     */
    public function update(Request $request, $id)
    {
        $expediente = Expediente::find($id);

        if (!$expediente) {
            return response()->json([
                'status' => false,
                'message' => 'Expediente no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'nombre' => 'required|string|max:255',
            'numero' => 'required|string|max:50',
            'anio' => 'required|integer',
            'codigo' => 'nullable|string|max:255',
            'etapa_procesal' => 'nullable|string|max:255',
            'inicio_proceso' => 'nullable|date',
            'tipo_proceso' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'arbitros' => 'array',
            'adjutadores' => 'array',
            'secretarios_tecnicos' => 'array',
            'participes' => 'array',
        ]);

        $expediente->update($validated);

        // ---------------- FECHAS ----------------
        if ($request->filled('fecha_laudo')) {
            $expediente->fechaLaudo()->updateOrCreate(
                ['expediente_id' => $expediente->id],
                ['fecha' => $request->fecha_laudo]
            );
        }

        if ($request->filled('fecha_resolucion')) {
            $expediente->fechaResolucion()->updateOrCreate(
                ['expediente_id' => $expediente->id],
                ['fecha' => $request->fecha_resolucion]
            );
        }

        // ---------------- ÁRBITROS ----------------
        \App\Models\ExpedienteArbitro::where('expediente_id', $expediente->id)->delete();
        if ($request->filled('arbitros')) {
            foreach ($request->arbitros as $arbitroId) {
                \App\Models\ExpedienteArbitro::create([
                    'expediente_id' => $expediente->id,
                    'usuario_id' => $arbitroId,
                ]);
            }
        }

        // ---------------- ADJUTADORES ----------------
        \App\Models\ExpedienteAdjutador::where('expediente_id', $expediente->id)->delete();
        if ($request->filled('adjutadores')) {
            foreach ($request->adjutadores as $adjutadorId) {
                \App\Models\ExpedienteAdjutador::create([
                    'expediente_id' => $expediente->id,
                    'usuario_id' => $adjutadorId,
                ]);
            }
        }

        // ---------------- SECRETARIOS TÉCNICOS ----------------
        \App\Models\SecretarioTecnico::where('expediente_id', $expediente->id)->delete();
        if ($request->filled('secretarios_tecnicos')) {
            foreach ($request->secretarios_tecnicos as $secretarioId) {
                \App\Models\SecretarioTecnico::create([
                    'expediente_id' => $expediente->id,
                    'usuario_id' => $secretarioId,
                ]);
            }
        }

        // ---------------- PARTÍCIPES ----------------
        \App\Models\ExpedienteParticipe::where('expediente_id', $expediente->id)->delete();
        if ($request->filled('participes')) {
            foreach ($request->participes as $p) {
                \App\Models\ExpedienteParticipe::create([
                    'expediente_id' => $expediente->id,
                    'participe_id' => $p['id'],
                    'condicion' => $p['condicion'] ?? null,
                ]);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Expediente actualizado correctamente',
            'data' => $expediente->load([
                'usuario',
                'arbitros.usuario',
                'adjutadores.usuario',
                'secretariosTecnicos.usuario',
                'participes.participe',
                'fechaLaudo',
                'fechaResolucion',
            ])
        ]);
    }


    /**
     * Eliminar expediente
     */
    public function destroy($id)
    {
        $expediente = Expediente::find($id);

        if (!$expediente) {
            return response()->json([
                'status' => false,
                'message' => 'Expediente no encontrado'
            ], 404);
        }

        // Eliminar relaciones dependientes
        $expediente->arbitros()->delete();
        $expediente->adjutadores()->delete();
        $expediente->participes()->delete();
        $expediente->fechaLaudo()?->delete();
        $expediente->fechaResolucion()?->delete();

        $expediente->delete();

        return response()->json([
            'status' => true,
            'message' => 'Expediente eliminado correctamente'
        ]);
    }
}
