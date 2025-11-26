<?php

namespace App\Http\Controllers;


use App\Models\Tarifario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TarifarioController extends Controller
{
    public function index()
    {
        // Devuelve todos los registros ordenados por fecha de creación descendente
        return Tarifario::orderBy('created_at', 'desc')->get();
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
        $nombreOriginal = $file->getClientOriginalName();
        $filename = 'tarifario_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('tarifarios', $filename, 'public');

        // Eliminar todos los archivos físicos anteriores y sus registros en BD
        $anteriores = Tarifario::all();
        foreach ($anteriores as $anterior) {
            if ($anterior->archivo_adjunto && Storage::disk('public')->exists($anterior->archivo_adjunto)) {
                Storage::disk('public')->delete($anterior->archivo_adjunto);
            }
            $anterior->delete(); // Eliminar el registro de la BD
        }

        $datos = [
            'archivo_adjunto' => $path,
            'nombre_original' => $nombreOriginal,
        ];
        
        $tarifario = Tarifario::create($datos);

        return response()->json(['message' => 'Archivo subido correctamente', 'tarifario' => $tarifario]);
    }

    /**
     * Descarga el archivo PDF guardado en la tabla tarifario.
     */
    public function download()
    {
        // Buscar el registro más reciente
        $tarifario = Tarifario::orderBy('created_at', 'desc')->first();
        
        if (!$tarifario || !$tarifario->archivo_adjunto) {
            return response()->json(['error' => 'No existe archivo tarifario'], 404);
        }
        
        // Verificar que el archivo existe en el disco público
        if (!Storage::disk('public')->exists($tarifario->archivo_adjunto)) {
            // Si el archivo no existe, eliminar el registro huérfano
            $tarifario->delete();
            return response()->json(['error' => 'El archivo no existe en el servidor'], 404);
        }
        
        $nombreDescarga = $tarifario->nombre_original ?: 'Tarifario.pdf';
        $filePath = Storage::disk('public')->path($tarifario->archivo_adjunto);
        
        return response()->download($filePath, $nombreDescarga, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}
