@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex">
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Calculadora</h1>
                <div class="flex items-center space-x-4">
                    <button onclick="downloadTarifario()" class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800">
                        Subir tarifario
                    </button>
                    <input type="text" id="tipoCambio" placeholder="Tipo de cambio ($)" 
                            class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </header>

        <div class="flex-1 p-6">
            <div class="bg-white rounded-lg shadow overflow-hidden p-6">
                <!-- Selector de tipo -->
                <div class="flex justify-end space-x-4 mb-6">
                    <button id="btnDeterminada" onclick="cambiarTipo('determinada')"
                            class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800">
                        Cuantía determinada
                    </button>
                    <button id="btnIndeterminada" onclick="cambiarTipo('indeterminada')"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                        Indeterminada
                    </button>
                </div>

                <!-- Sección de Gastos Administrativos -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Gastos administrativos</h2>
                        <button onclick="agregarRangoGastosAdmin()" class="flex items-center text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar cuantía
                        </button>
                    </div>
                    <div id="gastosAdminRangos">
                        <div class="grid grid-cols-6 gap-4 mb-4">
                            <button class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="text" placeholder="Rango min" class="border rounded-lg px-3 py-2">
                            <span class="flex items-center justify-center">-</span>
                            <input type="text" placeholder="Rango max" class="border rounded-lg px-3 py-2">
                            <input type="text" placeholder="%" class="border rounded-lg px-3 py-2">
                            <input type="text" placeholder="# de regla" class="border rounded-lg px-3 py-2">
                            <input type="text" placeholder="Monto máximo" class="border rounded-lg px-3 py-2">
                        </div>
                    </div>
                </div>

                <!-- Sección de Tribunal Arbitral -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Tribunal arbitral</h2>
                        <button onclick="agregarRangoTribunal()" class="flex items-center text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar cuantía
                        </button>
                    </div>
                    <div id="tribunalRangos">
                        <div class="grid grid-cols-6 gap-4 mb-4">
                            <button class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <input type="text" placeholder="Rango min" class="border rounded-lg px-3 py-2">
                            <span class="flex items-center justify-center">-</span>
                            <input type="text" placeholder="Rango max" class="border rounded-lg px-3 py-2">
                            <input type="text" placeholder="%" class="border rounded-lg px-3 py-2">
                            <input type="text" placeholder="# de regla" class="border rounded-lg px-3 py-2">
                            <input type="text" placeholder="Monto máximo" class="border rounded-lg px-3 py-2">
                        </div>
                    </div>
                </div>

                <!-- Botón de Editar -->
                <div class="flex justify-end">
                    <button onclick="guardarCambios()" 
                            class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Editar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let tipoCalculadora = 'determinada';
let contadorGastosAdmin = 1;
let contadorTribunal = 1;

function cambiarTipo(tipo) {
    tipoCalculadora = tipo;
    document.getElementById('btnDeterminada').classList.toggle('bg-black', tipo === 'determinada');
    document.getElementById('btnDeterminada').classList.toggle('text-white', tipo === 'determinada');
    document.getElementById('btnDeterminada').classList.toggle('bg-gray-200', tipo !== 'determinada');
    document.getElementById('btnDeterminada').classList.toggle('text-gray-700', tipo !== 'determinada');

    document.getElementById('btnIndeterminada').classList.toggle('bg-black', tipo === 'indeterminada');
    document.getElementById('btnIndeterminada').classList.toggle('text-white', tipo === 'indeterminada');
    document.getElementById('btnIndeterminada').classList.toggle('bg-gray-200', tipo !== 'indeterminada');
    document.getElementById('btnIndeterminada').classList.toggle('text-gray-700', tipo !== 'indeterminada');
}

function agregarRangoGastosAdmin() {
    const container = document.getElementById('gastosAdminRangos');
    const nuevoRango = document.createElement('div');
    nuevoRango.className = 'grid grid-cols-6 gap-4 mb-4';
    nuevoRango.innerHTML = `
        <button onclick="eliminarRango(this)" class="text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
            </svg>
        </button>
        <input type="text" placeholder="Rango min" class="border rounded-lg px-3 py-2">
        <span class="flex items-center justify-center">-</span>
        <input type="text" placeholder="Rango max" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="%" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="# de regla" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="Monto máximo" class="border rounded-lg px-3 py-2">
    `;
    container.appendChild(nuevoRango);
    contadorGastosAdmin++;
}

function agregarRangoTribunal() {
    const container = document.getElementById('tribunalRangos');
    const nuevoRango = document.createElement('div');
    nuevoRango.className = 'grid grid-cols-6 gap-4 mb-4';
    nuevoRango.innerHTML = `
        <button onclick="eliminarRango(this)" class="text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
            </svg>
        </button>
        <input type="text" placeholder="Rango min" class="border rounded-lg px-3 py-2">
        <span class="flex items-center justify-center">-</span>
        <input type="text" placeholder="Rango max" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="%" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="# de regla" class="border rounded-lg px-3 py-2">
        <input type="text" placeholder="Monto máximo" class="border rounded-lg px-3 py-2">
    `;
    container.appendChild(nuevoRango);
    contadorTribunal++;
}

function eliminarRango(elemento) {
    elemento.parentElement.remove();
}

function guardarCambios() {
    // Recolectar datos de gastos administrativos
    const gastosAdmin = [];
    document.querySelectorAll('#gastosAdminRangos .grid').forEach(rango => {
        const inputs = rango.querySelectorAll('input');
        gastosAdmin.push({
            rangoMin: inputs[0].value,
            rangoMax: inputs[1].value,
            porcentaje: inputs[2].value,
            regla: inputs[3].value,
            montoMaximo: inputs[4].value
        });
    });

    // Recolectar datos del tribunal
    const tribunal = [];
    document.querySelectorAll('#tribunalRangos .grid').forEach(rango => {
        const inputs = rango.querySelectorAll('input');
        tribunal.push({
            rangoMin: inputs[0].value,
            rangoMax: inputs[1].value,
            porcentaje: inputs[2].value,
            regla: inputs[3].value,
            montoMaximo: inputs[4].value
        });
    });

    const datos = {
        tipo: tipoCalculadora,
        tipoCambio: document.getElementById('tipoCambio').value,
        gastosAdministrativos: gastosAdmin,
        tribunalArbitral: tribunal
    };

    // Enviar datos al servidor
    guardarDatosCalculadora(datos);
}

async function guardarDatosCalculadora(datos) {
    try {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const response = await fetch('/api/calculadora/configuracion', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(datos)
        });

        if (!response.ok) throw new Error('Error al guardar configuración');
        
        alert('Configuración guardada exitosamente');
    } catch (error) {
        console.error('Error:', error);
        alert('Error al guardar la configuración');
    }
}

async function downloadTarifario() {
    try {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const response = await fetch('/api/calculadora/export', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Error al descargar tarifario');

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Tarifario_${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    } catch (error) {
        console.error('Error:', error);
        alert('Error al descargar el tarifario');
    }
}

// Cargar configuración inicial al cargar la página
document.addEventListener('DOMContentLoaded', async () => {
    try {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const response = await fetch('/api/calculadora/configuracion', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Error al cargar configuración');
        
        const config = await response.json();
        // Cargar configuración en la interfaz
        cargarConfiguracion(config);
    } catch (error) {
        console.error('Error:', error);
    }
});

function cargarConfiguracion(config) {
    if (config.tipo) {
        cambiarTipo(config.tipo);
    }
    if (config.tipoCambio) {
        document.getElementById('tipoCambio').value = config.tipoCambio;
    }
    
    // Cargar gastos administrativos
    if (config.gastosAdministrativos && config.gastosAdministrativos.length > 0) {
        const container = document.getElementById('gastosAdminRangos');
        container.innerHTML = ''; // Limpiar rangos existentes
        config.gastosAdministrativos.forEach(gasto => {
            const nuevoRango = document.createElement('div');
            nuevoRango.className = 'grid grid-cols-6 gap-4 mb-4';
            nuevoRango.innerHTML = `
                <button onclick="eliminarRango(this)" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>
                <input type="text" value="${gasto.rangoMin}" class="border rounded-lg px-3 py-2">
                <span class="flex items-center justify-center">-</span>
                <input type="text" value="${gasto.rangoMax}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${gasto.porcentaje}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${gasto.regla}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${gasto.montoMaximo}" class="border rounded-lg px-3 py-2">
            `;
            container.appendChild(nuevoRango);
        });
    }

    // Cargar tribunal arbitral
    if (config.tribunalArbitral && config.tribunalArbitral.length > 0) {
        const container = document.getElementById('tribunalRangos');
        container.innerHTML = ''; // Limpiar rangos existentes
        config.tribunalArbitral.forEach(tribunal => {
            const nuevoRango = document.createElement('div');
            nuevoRango.className = 'grid grid-cols-6 gap-4 mb-4';
            nuevoRango.innerHTML = `
                <button onclick="eliminarRango(this)" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </button>
                <input type="text" value="${tribunal.rangoMin}" class="border rounded-lg px-3 py-2">
                <span class="flex items-center justify-center">-</span>
                <input type="text" value="${tribunal.rangoMax}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${tribunal.porcentaje}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${tribunal.regla}" class="border rounded-lg px-3 py-2">
                <input type="text" value="${tribunal.montoMaximo}" class="border rounded-lg px-3 py-2">
            `;
            container.appendChild(nuevoRango);
        });
    }
}
</script>
@endsection