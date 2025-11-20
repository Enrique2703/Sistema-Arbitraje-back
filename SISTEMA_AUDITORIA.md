# Sistema de Auditoría - Guía de Implementación

## Descripción
El sistema de auditoría registra automáticamente todas las acciones importantes realizadas por usuarios en el sistema, incluyendo:
- Creación, edición y eliminación de expedientes, documentos y cédulas
- Subida y descarga de archivos
- Cambios de estado (aprobaciones, rechazos, etc.)
- Acciones administrativas

## Estructura de la Base de Datos

La tabla `auditorias` incluye los siguientes campos:
- `usuario_id`: ID del usuario que realizó la acción
- `usuario_nombre`: Nombre del usuario
- `expediente`: Identificador del expediente relacionado
- `accion`: Descripción breve de la acción realizada
- `detalle`: Información adicional sobre la acción
- `tipo_accion`: Tipo de operación (crear, editar, eliminar, subir, descargar, aprobar)
- `modulo`: Módulo del sistema (expedientes, documentos, cédulas, usuarios, partícipes)
- `ip`: Dirección IP desde donde se realizó la acción
- `datos_anteriores`: Estado anterior (JSON) - útil para ediciones
- `datos_nuevos`: Estado nuevo (JSON) - útil para creaciones y ediciones
- `created_at`: Fecha y hora de la acción

## Cómo Usar el Trait RegistraAuditoria

### 1. Importar el Trait en el Controlador

```php
use App\Traits\RegistraAuditoria;

class MiControlador extends Controller
{
    use RegistraAuditoria;
    
    // ... resto del código
}
```

### 2. Registrar Acciones

```php
self::registrarAuditoria(
    'Descripción de la acción',              // Acción
    'Detalle adicional',                      // Detalle (opcional)
    'tipo_accion',                            // crear, editar, eliminar, subir, descargar, aprobar
    'modulo',                                 // expedientes, documentos, cédulas, etc.
    $datosAnteriores,                         // Array con datos anteriores (opcional)
    $datosNuevos,                             // Array con datos nuevos (opcional)
    'Número de expediente'                    // Expediente relacionado (opcional)
);
```

### Ejemplos de Uso

#### Ejemplo 1: Crear un Documento
```php
self::registrarAuditoria(
    'Documento creado: ' . $request->sumilla,
    'Se creó un nuevo documento con 3 archivo(s)',
    'crear',
    'documentos',
    null,
    [
        'documento_id' => $documento->id,
        'sumilla' => $request->sumilla,
        'parte' => $request->parte,
    ],
    '0001 - 2025/ABC'
);
```

#### Ejemplo 2: Editar un Expediente
```php
$datosAnteriores = $expediente->toArray();
$expediente->update($request->validated());

self::registrarAuditoria(
    'Expediente actualizado: ' . $expediente->numero,
    'Se actualizó el estado del expediente',
    'editar',
    'expedientes',
    $datosAnteriores,
    $expediente->toArray(),
    "{$expediente->numero} - {$expediente->anio}/{$expediente->codigo}"
);
```

#### Ejemplo 3: Eliminar un Documento
```php
$datosAnteriores = $documento->toArray();

self::registrarAuditoria(
    'Documento eliminado: ' . $documento->sumilla,
    'Se eliminó el documento y sus archivos adjuntos',
    'eliminar',
    'documentos',
    $datosAnteriores,
    null,
    $expedienteNombre
);

$documento->delete();
```

#### Ejemplo 4: Descargar un Archivo
```php
self::registrarAuditoria(
    'Archivo descargado: documento.pdf',
    'Se descargó el archivo del documento: Demanda inicial',
    'descargar',
    'documentos',
    null,
    ['archivo' => 'documento.pdf', 'ruta' => 'storage/documentos/archivo.pdf'],
    '0001 - 2025/ABC'
);
```

#### Ejemplo 5: Aprobar un Documento
```php
self::registrarAuditoria(
    'Documento aprobado: ' . $documento->sumilla,
    'El documento fue aprobado y está listo para descarga',
    'aprobar',
    'documentos',
    ['revisado' => false],
    ['revisado' => true],
    $expedienteNombre
);
```

## Tipos de Acción Disponibles

- `crear`: Para creaciones de registros
- `editar`: Para actualizaciones de registros
- `eliminar`: Para eliminaciones de registros
- `subir`: Para subida de archivos
- `descargar`: Para descarga de archivos
- `aprobar`: Para aprobaciones o cambios de estado

## Módulos Disponibles

- `expedientes`: Acciones relacionadas con expedientes
- `documentos`: Acciones relacionadas con documentos
- `cédulas`: Acciones relacionadas con cédulas
- `usuarios`: Acciones relacionadas con usuarios
- `partícipes`: Acciones relacionadas con partícipes

## Vista de Auditoría

La vista de auditoría (`/auditoria`) incluye:
- Tabla con todos los registros de auditoría
- Búsqueda por usuario, acción, expediente, IP, etc.
- Filtros por módulo y tipo de acción
- Botón "Ver" para ver detalles completos de cada registro
- Exportación a Excel con formato
- Paginación

## Buenas Prácticas

1. **Siempre registrar antes de eliminar**: Guarda los datos antes de ejecutar `delete()`
2. **Incluir información contextual**: Añade detalles que ayuden a entender la acción
3. **Usar el módulo correcto**: Facilita el filtrado posterior
4. **Incluir expediente cuando aplique**: Ayuda a rastrear acciones por expediente
5. **Datos sensibles**: No incluir contraseñas o información confidencial en `datos_anteriores` o `datos_nuevos`

## Notas Adicionales

- El sistema detecta automáticamente el usuario autenticado
- La IP se registra automáticamente
- Los datos JSON se formatean automáticamente para su visualización
- El sistema funciona tanto para usuarios administrativos como partícipes
