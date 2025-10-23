<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Credencial;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);

        $query = Usuario::with('credencial');

        if ($search = $request->query('search')) {
            $query->where('nombres', 'like', "%$search%");
        }

        $ventasPaginated = $query->paginate($perPage);

        return response()->json([
            'registros' => $ventasPaginated->items(),
            'meta' => [
                'current_page' => $ventasPaginated->currentPage(),
                'last_page' => $ventasPaginated->lastPage(),
                'per_page' => $ventasPaginated->perPage(),
                'total' => $ventasPaginated->total(),
            ],
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'nivel_usuario' => 'required|string|max:50',
            'estado' => 'required|string|max:50',
            'email' => 'required|email|unique:credenciales,email',
            'password' => 'required|string|min:6',
        ]);

        $credencial = Credencial::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo_usuario' => $request->nivel_usuario,
        ]);

        $usuario = Usuario::create([
            'credencial_id' => $credencial->id,
            'nombres' => $request->nombres,
            'nivel_usuario' => $request->nivel_usuario,
            'estado' => $request->estado,
        ]);

        return response()->json(['mensaje' => 'Usuario creado exitosamente'], 201);
    }

    public function show($id)
    {
        $usuario = Usuario::with('credencial')->findOrFail($id);
        return response()->json($usuario);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombres' => 'sometimes|string|max:255',
            'nivel_usuario' => 'sometimes|string|max:50',
            'estado' => 'sometimes|string|max:50',
            'email' => 'sometimes|email|unique:credenciales,email,' . $usuario->credencial_id,
            'password' => 'sometimes|string|min:6',
        ]);

        $usuario->update($request->only(['nombres', 'nivel_usuario', 'estado']));

        if ($request->has('email') || $request->has('password')) {
            $usuario->credencial->update([
                'email' => $request->email ?? $usuario->credencial->email,
                'password' => $request->has('password')
                    ? Hash::make($request->password)
                    : $usuario->credencial->password,
            ]);
        }

        return response()->json($usuario->load('credencial'));
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);

        $usuario->credencial()->delete();
        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
