<?php

namespace App\Http\Controllers;

use App\Models\CuantiaIndeterminada;
use Illuminate\Http\Request;

class CuantiaIndeterminadaController extends Controller
{
    public function index()
    {
        return CuantiaIndeterminada::all();
    }

    public function show($id)
    {
        return CuantiaIndeterminada::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'porcentaje_arbitro' => 'required|numeric',
            'porcentaje_secretario' => 'required|numeric',
            'porcentaje_nulidad' => 'required|numeric',
            'porcentaje_resolucion' => 'required|numeric',
            'porcentaje_tarifa' => 'required|numeric',
        ]);
        return CuantiaIndeterminada::create($validated);
    }

    public function update(Request $request, $id)
    {
        $cuantia = CuantiaIndeterminada::findOrFail($id);
        $validated = $request->validate([
            'porcentaje_arbitro' => 'sometimes|numeric',
            'porcentaje_secretario' => 'sometimes|numeric',
            'porcentaje_nulidad' => 'sometimes|numeric',
            'porcentaje_resolucion' => 'sometimes|numeric',
            'porcentaje_tarifa' => 'sometimes|numeric',
        ]);
        $cuantia->update($validated);
        return $cuantia;
    }

    public function destroy($id)
    {
        $cuantia = CuantiaIndeterminada::findOrFail($id);
        $cuantia->delete();
        return response()->json(['message' => 'Eliminado correctamente']);
    }
}
