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
            'rango_min' => 'required|numeric|min:0',
            'rango_max' => 'required|numeric|min:0',
            'porcentaje' => 'required|numeric',
            'monto_max' => 'nullable|numeric',
            'monto_base' => 'nullable|numeric',
            'regla' => 'nullable|string',
        ]);
        
        // Validar que rango_min < rango_max
        if ($validated['rango_min'] >= $validated['rango_max']) {
            return response()->json([
                'message' => 'El rango mínimo debe ser menor que el rango máximo'
            ], 422);
        }
        
        // Validar que monto_base <= monto_max (si ambos están presentes)
        if (isset($validated['monto_base']) && isset($validated['monto_max']) && 
            $validated['monto_base'] > $validated['monto_max']) {
            return response()->json([
                'message' => 'El monto base no puede ser mayor que el monto máximo'
            ], 422);
        }
        
        return GastosAdministrativos::create($validated);
    }

    public function update(Request $request, $id)
    {
        $gasto = GastosAdministrativos::findOrFail($id);
        $validated = $request->validate([
            'escala' => 'sometimes|string',
            'rango_min' => 'sometimes|numeric|min:0',
            'rango_max' => 'sometimes|numeric|min:0',
            'porcentaje' => 'sometimes|numeric',
            'monto_max' => 'nullable|numeric',
            'monto_base' => 'nullable|numeric',
            'regla' => 'nullable|string',
        ]);
        
        // Obtener valores actuales si no se proporcionan en la actualización
        $rangoMin = $validated['rango_min'] ?? $gasto->rango_min;
        $rangoMax = $validated['rango_max'] ?? $gasto->rango_max;
        
        // Validar que rango_min < rango_max
        if ($rangoMin >= $rangoMax) {
            return response()->json([
                'message' => 'El rango mínimo debe ser menor que el rango máximo'
            ], 422);
        }
        
        // Validar que monto_base <= monto_max
        $montoBase = $validated['monto_base'] ?? $gasto->monto_base;
        $montoMax = $validated['monto_max'] ?? $gasto->monto_max;
        
        if ($montoBase !== null && $montoMax !== null && $montoBase > $montoMax) {
            return response()->json([
                'message' => 'El monto base no puede ser mayor que el monto máximo'
            ], 422);
        }
        
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
