<?php

namespace App\Http\Controllers;

use App\Models\Tarifario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TarifarioController extends Controller
{
    public function index()
    {
        return Tarifario::all();
    }

    public function show($id)
    {
        return Tarifario::findOrFail($id);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'archivo_adjunto' => 'required|string',
        ]);
        return Tarifario::create($validated);
    }

    public function update(Request $request, $id)
    {
        $tarifario = Tarifario::findOrFail($id);
        $validated = $request->validate([
            'archivo_adjunto' => 'sometimes|string',
        ]);
        $tarifario->update($validated);
        return $tarifario;
    }

    public function destroy($id)
    {
        $tarifario = Tarifario::findOrFail($id);
        $tarifario->delete();
        return response()->json(['message' => 'Eliminado correctamente']);
    }

    /**
     * Sube un archivo PDF y lo guarda en la tabla tarifario.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'tarifario' => 'required|file|mimes:pdf|max:10240', // 10MB máx
        ]);

        $file = $request->file('tarifario');
        $filename = 'tarifario_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('tarifarios', $filename, 'public');

        // Elimina registros anteriores (solo uno vigente)
        Tarifario::truncate();
        $tarifario = Tarifario::create([
            'archivo_adjunto' => $path,
        ]);

        return response()->json(['message' => 'Archivo subido correctamente', 'tarifario' => $tarifario]);
    }

    /**
     * Descarga el archivo PDF guardado en la tabla tarifario.
     */
    public function download()
    {
        $tarifario = Tarifario::latest()->first();
        if (!$tarifario || !Storage::disk('public')->exists($tarifario->archivo_adjunto)) {
            return response()->json(['message' => 'No existe archivo tarifario'], 404);
        }
        return response()->download(storage_path('app/public/' . $tarifario->archivo_adjunto), 'Tarifario.pdf');
    }
}
