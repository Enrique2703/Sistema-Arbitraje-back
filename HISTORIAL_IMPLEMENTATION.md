# Implementación del Sistema de Historial

## ¿Cómo usar el sistema de historial en cualquier controlador?

El sistema de historial permite registrar todas las acciones que realizan los usuarios en los expedientes. Para implementarlo en cualquier controlador, sigue estos pasos:

### 1. Importar el HistorialController

En la parte superior de tu controlador, asegúrate de que esté disponible:

```php
use App\Http\Controllers\HistorialController;
```

### 2. Registrar acciones

Para registrar una acción en el historial, usa el método estático `registrar()`:

```php
HistorialController::registrar($expedienteId, $descripcionAccion, $usuarioId);
```

Donde:
- `$expedienteId`: ID del expediente relacionado (requerido)
- `$descripcionAccion`: Texto descriptivo de la acción (requerido)
- `$usuarioId`: ID del usuario (opcional, por defecto usa Auth::id())

## Ejemplos de implementación

### Crear registro
```php
public function store(Request $request)
{
    // Validación y creación del modelo
    $modelo = MiModelo::create($validated);

    // Registrar en historial
    HistorialController::registrar($expedienteId, 'Creó un nuevo elemento');

    return response()->json(['mensaje' => 'Creado exitosamente']);
}
```

### Actualizar registro
```php
public function update(Request $request, $id)
{
    $modelo = MiModelo::findOrFail($id);
    $modelo->update($validated);

    // Registrar en historial
    HistorialController::registrar($modelo->expediente_id, 'Actualizó el elemento: ' . $modelo->nombre);

    return response()->json(['mensaje' => 'Actualizado exitosamente']);
}
```

### Eliminar registro
```php
public function destroy($id)
{
    $modelo = MiModelo::findOrFail($id);
    
    // Registrar ANTES de eliminar para conservar datos
    HistorialController::registrar($modelo->expediente_id, 'Eliminó el elemento: ' . $modelo->nombre);
    
    $modelo->delete();

    return response()->json(['mensaje' => 'Eliminado exitosamente']);
}
```

### Exportar datos
```php
public function export()
{
    $datos = MiModelo::all();
    
    // Si la exportación es específica de un expediente
    if ($expedienteId) {
        HistorialController::registrar($expedienteId, 'Exportó listado de elementos');
    }

    return response()->json($datos);
}
```

## Acciones ya implementadas

### ExpedienteController
- ✅ Creación de expedientes
- ✅ Actualización de expedientes
- ✅ Eliminación de expedientes
- ✅ Exportación de expedientes

### ParticipeDocumentoController
- ✅ Presentación de documentos
- ✅ Actualización de documentos
- ✅ Eliminación de documentos

### CedulaController
- ✅ Generación de cédulas

## Vista del historial

Para ver el historial de un expediente, accede a:
- **Ruta**: `/expedientes/historial?id={expedienteId}`
- **API**: `GET /api/expedientes/{expedienteId}/historial`

### Funcionalidades de la vista:
- Lista cronológica de acciones
- Búsqueda por usuario o acción
- Paginación
- Exportación a Excel/CSV
- Detalles completos de cada acción

## Notas importantes

1. **Error handling**: El sistema registra errores en logs pero no interrumpe el flujo principal
2. **Transacciones**: Si usas DB transactions, registra el historial DESPUÉS del commit
3. **Usuario automático**: Si no especificas usuario_id, se usa Auth::id() automáticamente
4. **Flexibilidad**: Puedes registrar cualquier acción descriptiva que sea relevante

## Próximos pasos sugeridos

1. Implementar historial en UsuarioController
2. Implementar historial en ParticipeController  
3. Agregar historial para login/logout de usuarios
4. Implementar filtros avanzados en la vista del historial
5. Agregar notificaciones en tiempo real de acciones

¡El sistema está listo para usarse! Solo agrega `HistorialController::registrar()` en cada acción que quieras trackear.