# Sistema de Historial para Administradores y Staff

## 📋 **¿Qué registra automáticamente?**

El sistema ahora registra **automáticamente** todas las acciones realizadas por usuarios con nivel **Administrador** o **Staff** en el historial de cada expediente.

### ✅ **Acciones registradas automáticamente:**

#### **Expedientes:**
- ✅ Creación de nuevos expedientes
- ✅ Actualización de expedientes existentes
- ✅ Eliminación de expedientes
- ✅ Asignación de árbitros, adjutadores y secretarios técnicos
- ✅ Cambios de estado
- ✅ Exportación de datos

#### **Documentos:**
- ✅ Presentación de nuevos documentos
- ✅ Modificación de documentos existentes
- ✅ Eliminación de documentos

#### **Cédulas:**
- ✅ Generación de nuevas cédulas
- ✅ Envío de cédulas a usuarios

#### **Usuarios y Partícipes:**
- ✅ Creación de usuarios
- ✅ Modificación de usuarios
- ✅ Eliminación de usuarios
- ✅ Registro de partícipes

## 🔧 **¿Cómo funciona?**

### **1. Middleware Automático**
- Se ejecuta automáticamente en todas las rutas API
- Solo registra acciones de usuarios Administrador/Staff
- Captura operaciones POST, PUT, DELETE exitosas
- Excluye login/logout y operaciones internas

### **2. Registros Detallados**
Los registros incluyen:
- **Fecha y hora** exacta
- **Usuario** que realizó la acción
- **Tipo de acción** (crear, modificar, eliminar)
- **Detalles específicos** de lo que se cambió
- **Etiqueta [ADMIN]** para identificar acciones administrativas

### **3. Ejemplos de Registros**

```
[ADMIN] Juan Pérez: Creó expediente completo - Número: 001, Año: 2025, Código: CA-RENA
[ADMIN] Ana García: Asignó árbitros: Usuario ID: 15, Usuario ID: 23
[ADMIN] Luis Martín: Cambió el estado del expediente de 'Activo' a 'Concluido'
[ADMIN] María López: Exportó listado de expedientes
```

## 📊 **Ver el Historial**

### **Acceso:**
1. Ve a cualquier expediente
2. Haz clic en "Historial" en el menú
3. O navega a: `/expedientes/historial?id={expedienteId}`

### **Funcionalidades:**
- 🔍 **Búsqueda**: Por nombre de usuario o tipo de acción
- 📑 **Paginación**: Navega por múltiples páginas
- 📊 **Exportación**: Descarga historial en CSV/Excel
- 👁️ **Detalles**: Ve información completa de cada acción

## 🛠️ **Para Desarrolladores**

### **Usar el Trait RegistraHistorial**

```php
use App\Traits\RegistraHistorial;

class MiController extends Controller 
{
    use RegistraHistorial;
    
    public function miMetodo($expedienteId) 
    {
        // Registrar acción específica
        $this->registrarAccionAdmin($expedienteId, 'Realizó acción especial');
        
        // Registrar cambio de estado
        $this->registrarCambioEstado($expedienteId, 'Activo', 'Suspendido');
        
        // Registrar asignación
        $this->registrarAsignacionUsuarios($expedienteId, 'revisores', ['Juan', 'Ana']);
        
        // Registrar exportación
        $this->registrarExportacion($expedienteId, 'documentos PDF');
    }
}
```

### **Métodos Disponibles:**

- `registrarAccionAdmin($expedienteId, $accion, $detalles = [])`
- `registrarCambioEstado($expedienteId, $estadoAnterior, $estadoNuevo)`
- `registrarAsignacionUsuarios($expedienteId, $tipo, $usuarios)`
- `registrarExportacion($expedienteId, $tipoExportacion)`
- `registrarAccionMasiva($expedienteIds, $accion)`
- `registrarAccesoSensible($expedienteId, $recurso)`

## ⚙️ **Configuración**

### **Niveles de Usuario que se Registran:**
- `administrador`
- `admin` 
- `staff`

### **Rutas Excluidas del Registro:**
- `/api/login`
- `/api/logout`
- `/api/historial/*`
- Cualquier ruta de historial

## 🚀 **Beneficios**

1. **Trazabilidad completa** de acciones administrativas
2. **Auditoría automática** sin intervención manual
3. **Transparencia** en las operaciones del sistema
4. **Cumplimiento** de normativas de trazabilidad
5. **Debug** facilitado para resolver problemas

## 📝 **Notas Importantes**

- ⚡ **Automático**: No requiere código adicional en cada controlador
- 🛡️ **Solo Admins**: Solo registra acciones de usuarios administrativos
- 📊 **Performance**: Optimizado para no afectar el rendimiento
- 🔒 **Seguro**: No registra información sensible en logs
- 💾 **Persistente**: Los registros se mantienen en base de datos

¡El sistema está completamente operativo y registrará automáticamente todas las acciones administrativas!