<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expediente;
use App\Models\ExpedienteArbitro;
use App\Models\ExpedienteAdjutador;
use App\Models\ExpedienteParticipe;
use App\Models\ExpedienteFechaLaudo;
use App\Models\ExpedienteFechaResolucion;
use App\Models\Participe;
use App\Models\SecretarioTecnico;
use App\Models\ParticipeDocumento;
use App\Traits\RegistraHistorial;
use App\Traits\RegistraAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class ExpedienteController extends Controller
{
    use RegistraHistorial, RegistraAuditoria;
    public function export()
    {
        try {
            Log::info('Iniciando exportación de expedientes');
            
            // Cargar expedientes con sus relaciones
            $expedientes = Expediente::withCount('participes')->get();
            Log::info('Expedientes recuperados', ['count' => count($expedientes)]);

            if ($expedientes->isEmpty()) {
                Log::warning('No se encontraron expedientes para exportar');
                return response()->json(['error' => 'No hay expedientes para exportar'], 404);
            }

            $data = $expedientes->map(function ($expediente) {
                try {
                    // Obtener cantidad de partícipes
                    $cantidadParticipes = $expediente->participes_count;
                    
                    // Contar documentos
                    $documentos = ParticipeDocumento::where('expediente_id', $expediente->id)->count();
                    
                    $datos = [
                        'id' => $expediente->id,
                        'numero' => $expediente->numero,
                        'anio' => $expediente->anio,
                        'codigo' => $expediente->codigo ?? '',
                        'estado' => $expediente->estado,
                        'cantidad_participes' => $cantidadParticipes,
                        'documentos' => $documentos,
                        'fecha_creacion' => $expediente->created_at,
                        'fecha_actualizacion' => $expediente->updated_at
                    ];
                    Log::debug('Procesando expediente', [
                        'id' => $expediente->id, 
                        'participes' => $cantidadParticipes,
                        'documentos' => $documentos
                    ]);
                    return $datos;
                } catch (Exception $e) {
                    Log::error('Error procesando expediente', [
                        'id' => $expediente->id,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            });
            
            Log::info('Datos procesados exitosamente', ['count' => count($data)]);
            
            // Registrar exportación en historial (asumiendo expediente genérico o primer expediente)
            if ($expedientes->isNotEmpty()) {
                HistorialController::registrar($expedientes->first()->id, 'Exportó listado de expedientes');
            }
            
            return response()->json($data->toArray());        } catch (Exception $e) {
            Log::error('Error al exportar expedientes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Error al cargar expedientes',
                'details' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Listar todos los expedientes
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);
        $search = $request->query('search');
        $estado = $request->query('estado'); //

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
                'id' => $expediente->id ?? 'N/A',
                'numero' => $expediente->numero,
                'anio' => $expediente->anio,
                'codigo' => $expediente->codigo,
                'estado' => $expediente->estado ?? 'Sin estado',
                'cantidad_participes' => $expediente->participes->count(),
                'documentos' => $expediente->participeDocumentos()->count(),
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

    public function indexParticipes(Request $request)
    {
        $credencial = auth('api')->user();
        $participe = Auth::user()->participe;

        if (!$participe) {
            return response()->json([
                'status' => false,
                'message' => 'No se encontró el participe asociado'
            ], 404);
        }

        $perPage = $request->query('per_page', 6);
        $search = $request->query('search');
        $estado = $request->query('estado');
        $rol = $request->query('tipo_proceso');

        $query = Expediente::orderByDesc('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('etapa_procesal', 'like', "%$search%")
                    ->orWhere('tipo_proceso', 'like', "%$search%");
            });
        }

        if ($estado && $estado !== 'Todos') {
            $query->where('estado', $estado);
        }

        if ($rol && $rol !== 'Todos') {
            $query->whereHas('participes', function ($q) use ($rol) {
                $q->where('condicion', $rol);
            });
        }

        $expedientesPaginated = $query->paginate($perPage);

        // 🔹 Transformar los datos antes de enviar al frontend
        $registros = $expedientesPaginated->map(function ($expediente) use ($participe) {
            // Get the participant role for this specific expediente
            $participeExpediente = $expediente->participes()->first();

            return [
            'id' => $expediente->id ?? 'N/A',
            'codigo' => sprintf('%s - %s/%s',
                $expediente->numero ?? 'Expediente', 
                $expediente->anio ?? '—',
                $expediente->codigo ?? '—'
            ),
            'estado' => $expediente->estado ?? 'Sin estado',
            'cantidad_participes' => $expediente->participes->count(),
            'condicion' => $participeExpediente?->condicion ?? 'Sin rol',
            'cantidad_documentos' => $expediente->participeDocumentos->count() ?? 0,
            'fecha_creacion' => $expediente->created_at?->format('Y-m-d'),
            'fecha_actualizacion' => $expediente->updated_at?->format('Y-m-d'),
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

        // Registrar en historial automático (middleware)
        HistorialController::registrar($expediente->id, 'Creó el expediente');
        
        // Registrar con detalles administrativos usando el trait
        $this->registrarAccionAdmin($expediente->id, 'Creó expediente completo', [
            "Número: {$expediente->numero}",
            "Año: {$expediente->anio}",
            "Código: {$expediente->codigo}"
        ]);

        // Árbitros
        if ($request->filled('arbitros')) {
            $arbitros = [];
            foreach ($request->arbitros as $arbitroId) {
                ExpedienteArbitro::create([
                    'expediente_id' => $expediente->id,
                    'usuario_id' => $arbitroId,
                ]);
                $arbitros[] = "Usuario ID: {$arbitroId}";
            }
            $this->registrarAsignacionUsuarios($expediente->id, 'árbitros', $arbitros);
        }

        // Adjutadores
        if ($request->filled('adjutadores')) {
            $adjutadores = [];
            foreach ($request->adjutadores as $adjutadorId) {
                ExpedienteAdjutador::create([
                    'expediente_id' => $expediente->id,
                    'usuario_id' => $adjutadorId,
                ]);
                $adjutadores[] = "Usuario ID: {$adjutadorId}";
            }
            $this->registrarAsignacionUsuarios($expediente->id, 'adjutadores', $adjutadores);
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

        // Preparar data manualmente para asegurar compatibilidad
        $data = $expediente->toArray();
        $data['fecha_laudo'] = $expediente->fechaLaudo->fecha ?? null;
        $data['fecha_resolucion'] = $expediente->fechaResolucion->fecha ?? null;
        
        // Asegurar que las relaciones estén disponibles con nombres correctos
        $data['arbitros'] = $expediente->arbitros->toArray();
        $data['adjutadores'] = $expediente->adjutadores->toArray();
        $data['secretarios_tecnicos'] = $expediente->secretariosTecnicos->toArray();
        $data['participes'] = $expediente->participes->toArray();

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

        // Capturar datos anteriores para auditoría
        $datosAnteriores = $expediente->toArray();
        
        $expediente->update($validated);
        
        // Capturar datos nuevos para auditoría
        $datosNuevos = $expediente->fresh()->toArray();
        
        // Registrar en historial
        HistorialController::registrar($expediente->id, 'Actualizó el expediente');
        
        // Registrar en auditoría
        self::registrarAuditoria(
            'Actualizó expediente',
            "Expediente #{$expediente->numero} - {$expediente->anio}/{$expediente->codigo} editado",
            'actualización',
            'expedientes',
            $datosAnteriores,
            $datosNuevos,
            "{$expediente->numero} - {$expediente->anio}/{$expediente->codigo}"
        );

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

        // Registrar en historial antes de eliminar
        HistorialController::registrar($expediente->id, 'Eliminó el expediente');
        
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
