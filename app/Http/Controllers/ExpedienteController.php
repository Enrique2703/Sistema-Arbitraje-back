<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\ExpedienteArbitro;
use App\Models\ExpedienteAdjutador;
use App\Models\ExpedienteParticipe;
use App\Models\ExpedienteFechaLaudo;
use App\Models\ExpedienteFechaResolucion;
use Illuminate\Http\Request;

class ExpedienteController extends Controller
{
    /**
     * Listar todos los expedientes
     */
    public function index()
    {
        $expedientes = Expediente::with([
            'usuario',
            'arbitros.usuario',
            'adjutadores.usuario',
            'participes.participe',
            'fechaLaudo',
            'fechaResolucion',
        ])->get();

        return response()->json([
            'status' => true,
            'message' => 'Lista de expedientes obtenida correctamente',
            'data' => $expedientes
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
            'etapa_procesal' => 'nullable|string|max:255',
            'inicio_proceso' => 'nullable|date',
            'tipo_proceso' => 'nullable|string|max:255',
            'arbitros' => 'array',
            'adjutadores' => 'array',
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

        return response()->json([
            'status' => true,
            'message' => 'Expediente obtenido correctamente',
            'data' => $expediente
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
            'etapa_procesal' => 'nullable|string|max:255',
            'inicio_proceso' => 'nullable|date',
            'tipo_proceso' => 'nullable|string|max:255',
        ]);

        $expediente->update($validated);

        // Fechas
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

        return response()->json([
            'status' => true,
            'message' => 'Expediente actualizado correctamente',
            'data' => $expediente->load([
                'usuario',
                'arbitros.usuario',
                'adjutadores.usuario',
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
