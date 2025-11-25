<?php

namespace App\Http\Controllers;

use App\Models\GastosAdministrativos;
use Illuminate\Http\Request;

class GastosAdministrativosController extends Controller
{
    public function index()
    {
        return GastosAdministrativos::all();
    }

    public function show($id)
    {
        return GastosAdministrativos::findOrFail($id);
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
        return GastosAdministrativos::create($validated);
    }

    public function update(Request $request, $id)
    {
        $gasto = GastosAdministrativos::findOrFail($id);
        $validated = $request->validate([
            'escala' => 'sometimes|string',
            'rango_min' => 'sometimes|numeric',
            'rango_max' => 'sometimes|numeric',
            'porcentaje' => 'sometimes|numeric',
            'monto_max' => 'nullable|numeric',
            'monto_base' => 'nullable|numeric',
            'regla' => 'nullable|string',
        ]);
        $gasto->update($validated);
        return $gasto;
    }

    public function destroy($id)
    {
        $gasto = GastosAdministrativos::findOrFail($id);
        $gasto->delete();
        return response()->json(['message' => 'Eliminado correctamente']);
    }
}
