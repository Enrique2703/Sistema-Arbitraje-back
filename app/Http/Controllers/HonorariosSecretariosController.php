<?php

namespace App\Http\Controllers;

use App\Models\HonorariosSecretarios;
use Illuminate\Http\Request;

class HonorariosSecretariosController extends Controller
{
    public function index()
    {
        return HonorariosSecretarios::all();
    }

    public function show($id)
    {
        return HonorariosSecretarios::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'escala' => 'required|string',
            'rango_min' => 'required|numeric',
            'rango_max' => 'required|numeric',
            'porcentaje' => 'required|numeric',
            'monto_max' => 'nullable|numeric',
            'monto_base' => 'nullable|numeric',
            'regla' => 'nullable|string',
        ]);
        return HonorariosSecretarios::create($validated);
    }

    public function update(Request $request, $id)
    {
        $honorario = HonorariosSecretarios::findOrFail($id);
        $validated = $request->validate([
            'escala' => 'sometimes|string',
            'rango_min' => 'sometimes|numeric',
            'rango_max' => 'sometimes|numeric',
            'porcentaje' => 'sometimes|numeric',
            'monto_max' => 'nullable|numeric',
            'monto_base' => 'nullable|numeric',
            'regla' => 'nullable|string',
        ]);
        $honorario->update($validated);
        return $honorario;
    }

    public function destroy($id)
    {
        $honorario = HonorariosSecretarios::findOrFail($id);
        $honorario->delete();
        return response()->json(['message' => 'Eliminado correctamente']);
    }
}
