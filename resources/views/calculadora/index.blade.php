@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="flex-1">
        <!-- Título y controles principales -->
        <div class="flex justify-between items-center px-6 py-4">
            <h1 class="text-xl font-medium text-gray-900">Calculadora</h1>
            <div class="flex items-center space-x-3">
                <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <img src="{{ asset('img/folder.png') }}" alt="Folder Icon" class="w-6 h-6">
                </button>
            </div>
        </div>
        
        <!-- Barra de controles -->
        <div class="flex justify-between items-center px-6 py-2 bg-white border-y border-gray-200">
            <div class="flex items-center space-x-4">
                <button onclick="downloadTarifario()" class="px-4 py-2 bg-black text-white text-sm font-medium rounded hover:bg-gray-800">
                    Subir tarifario
                </button>
                <input type="text" id="tipoCambio" placeholder="Tipo de cambio (1$)" 
                        class="border border-gray-300 rounded px-3 py-2 text-sm w-44">
            </div>
            <button onclick="cambiarTipo('indeterminada')" class="px-4 py-2 bg-black text-white text-sm font-medium rounded hover:bg-gray-800">
                Indeterminada
            </button>
        </div>        <div class="flex-1 p-6">
            <div class="bg-white rounded-lg shadow overflow-hidden p-6">
                <!-- Contenido principal -->
                <div class="px-6 py-4">
                    <h2 class="text-lg font-medium text-gray-900 mb-6">Cuantía determinada</h2>
                </div>                <!-- Sección de Gastos Administrativos -->
                <!-- Sección Gastos Administrativos -->
                <div class="px-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-medium text-gray-900">Gastos administrativos</h3>
                        <button onclick="agregarRangoGastosAdmin()" class="flex items-center text-sm text-gray-600 hover:text-gray-900">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar cuantía
                        </button>
                    </div>
                    <div id="gastosAdminRangos" class="space-y-3">
                        <div class="grid grid-cols-6 gap-4 items-center">
                            <div class="flex items-center">
                                <button class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="ml-2 text-sm text-gray-500">2</span>
                            </div>
                            <input type="text" placeholder="Rango min" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                            <div class="flex items-center justify-center text-gray-400">-</div>
                            <input type="text" placeholder="Rango max" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                            <input type="text" placeholder="%" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" placeholder="# de regla" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                                <input type="text" placeholder="Monto máximo" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                            </div>
                        </div>
                        <div class="grid grid-cols-6 gap-4 items-center">
                            <div class="flex items-center">
                                <button class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="ml-2 text-sm text-gray-500">2</span>
                            </div>
                            <input type="text" placeholder="Rango min" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                            <div class="flex items-center justify-center text-gray-400">-</div>
                            <input type="text" placeholder="Rango max" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                            <input type="text" placeholder="%" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                            <div class="grid grid-cols-2 gap-4">
                                <input type="text" placeholder="# de regla" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                                <input type="text" placeholder="Monto máximo" class="border border-gray-300 rounded px-3 py-2 text-sm w-full">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección de Tribunal Arbitral -->
                <div class="px-6 mt-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-medium text-gray-900">Tribunal arbitral</h2>
                        <button onclick="agregarRangoTribunal()" class="flex items-center text-sm text-gray-600 hover:text-gray-900">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="bg-gray-50 px-6 py-4 mt-8">
                    <button onclick="guardarCambios()" 
                            class="px-4 py-2 bg-gray-200 text-sm font-medium text-gray-800 rounded">
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