<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Credencial;
use App\Models\Participe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParticipeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);

        $query = Participe::with('credencial')
            ->withCount('documentos');

        if ($search = $request->query('search')) {
            $query->where('nombres', 'like', "%$search%");
        }
        if ($estado = $request->query('estado')) {
            $query->where('estado', $estado);
        }

        $participesPaginated = $query->paginate($perPage);

        return response()->json([
            'registros' => $participesPaginated->items(),
            'meta' => [
                'current_page' => $participesPaginated->currentPage(),
                'last_page' => $participesPaginated->lastPage(),
                'per_page' => $participesPaginated->perPage(),
                'total' => $participesPaginated->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'estado' => 'required|string|max:50',
            'email' => 'required|email|unique:credenciales,email',
            'password' => 'required|string|min:6',
        ]);

        $credencial = Credencial::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo_usuario' => 'participe',
        ]);

        $participe = Participe::create([
            'credencial_id' => $credencial->id,
            'nombres' => $request->nombres,
            'estado' => $request->estado,
        ]);

        // Auditoría: registrar creación
        \App\Traits\RegistraAuditoria::registrarAuditoria(
            'Partícipe creado',
            'Se creó un partícipe',
            'crear',
            'participes',
            null,
            $participe->toArray(),
            $participe->nombres
        );

        return response()->json(['mensaje' => 'Participe creado exitosamente'], 201);
    }

    public function show($id)
    {
        $participe = Participe::with('credencial')->findOrFail($id);
        return response()->json($participe);
    }

    public function update(Request $request, $id)
    {
        $participe = Participe::findOrFail($id);

        $request->validate([
            'nombres' => 'sometimes|string|max:255',
            'estado' => 'sometimes|string|max:50',
            'email' => 'sometimes|email|unique:credenciales,email,' . $participe->credencial_id,
            'password' => 'sometimes|string|min:6',
        ]);

        $participe->update($request->only(['nombres', 'estado']));

        if ($request->has('email') || $request->has('password')) {
            $participe->credencial->update([
                'email' => $request->email ?? $participe->credencial->email,
                'password' => $request->has('password')
                    ? Hash::make($request->password)
                    : $participe->credencial->password,
            ]);
        }

        return response()->json($participe->load('credencial'));
    }

    public function destroy($id)
    {
        $participe = Participe::findOrFail($id);
        $datosAnteriores = $participe->toArray();
        $nombre = $participe->nombres;
        $participe->credencial()->delete();
        $participe->delete();
        // Auditoría: registrar eliminación
        \App\Traits\RegistraAuditoria::registrarAuditoria(
            'Partícipe eliminado',
            'Se eliminó un partícipe',
            'eliminar',
            'participes',
            $datosAnteriores,
            null,
            $nombre
        );
        return response()->json(['message' => 'Participe eliminado correctamente']);
    }

    public function exportToExcel()
    {
        $participes = Participe::with('credencial')
            ->withCount('documentos')
            ->get()
            ->map(function ($participe) {
                return [
                    'ID' => str_pad($participe->id, 4, '0', STR_PAD_LEFT),
                    'NOMBRES' => $participe->nombres,
                    'ESTADO' => $participe->estado,
                    'EMAIL' => $participe->credencial ? $participe->credencial->email : '',
                    'EXPEDIENTES' => $participe->documentos_count
                ];
            });

        return response()->json($participes);
    }
}
