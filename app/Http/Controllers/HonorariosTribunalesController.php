<?php

namespace App\Http\Controllers;

use App\Models\HonorariosTribunales;
use Illuminate\Http\Request;

class HonorariosTribunalesController extends Controller
{
    public function index()
    {
        return HonorariosTribunales::all();
    }

    public function show($id)
    {
        return HonorariosTribunales::findOrFail($id);
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
        return HonorariosTribunales::create($validated);
    }

    public function update(Request $request, $id)
    {
        $honorario = HonorariosTribunales::findOrFail($id);
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
        $honorario = HonorariosTribunales::findOrFail($id);
        $honorario->delete();
        return response()->json(['message' => 'Eliminado correctamente']);
    }
}
