@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex">
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="/expedientes" class="flex items-center text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-semibold text-gray-900">Historial</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="exportToExcel()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <img src="{{ asset('img/folder.png') }}" alt="Folder Icon" class="w-6 h-6">
                    </button>
                </div>
            </div>
        </header>

        <div class="flex-1 p-6">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" id="searchInput"
                            class="block w-80 pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Buscar por usuario o acción..."">
                    </div>

                    <button id="filterButton"
                        class="p-2 bg-gray-200 hover:bg-gray-300 rounded-md border border-gray-300 flex items-center justify-center">
                        <i class="bi bi-funnel-fill text-black text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Tabla de historial -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead style="background-color: #737373;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Acción</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Ver</th>
                        </tr>
                    </thead>
                    <tbody id="historialTableBody" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Cargando historial...</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Paginación -->
                <div id="paginationContainer" class="px-6 py-4 border-t bg-white">
                    <nav id="paginationControls" class="flex items-center justify-between" aria-label="Pagination">
                        <div class="flex items-center justify-between w-full">
                            <button class="px-3 py-2 text-sm text-gray-700">&larr; Anterior</button>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 text-sm rounded-md bg-gray-200">1</span>
                                <span class="px-3 py-1 text-sm text-gray-600">2</span>
                            </div>
                            <button class="px-3 py-2 text-sm text-gray-700">Siguiente &rarr;</button>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let expedienteId = null;
let currentPage = 1;
let lastPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Obtener el ID del expediente de la URL
    const urlParams = new URLSearchParams(window.location.search);
    expedienteId = urlParams.get('id');
    
    if (!expedienteId) {
        alert('No se proporcionó ID del expediente');
        window.location.href = '/expedientes';
        return;
    }

    cargarHistorial();
    configurarBuscador();
});

function configurarBuscador() {
    const searchInput = document.getElementById('searchInput');
    let timeout = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            cargarHistorial();
        }, 300);
    });
}

async function cargarHistorial() {
    const tbody = document.getElementById('historialTableBody');
    const searchTerm = document.getElementById('searchInput').value;

    try {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-500">Cargando...</td></tr>';

        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const response = await fetch(`/api/expedientes/${expedienteId}/historial?search=${searchTerm}&page=${currentPage}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Error al cargar historial');
        
        const data = await response.json();
        renderizarHistorial(data.registros);
        actualizarPaginacion(data.meta);

    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Error al cargar el historial</td></tr>';
    }
}

function renderizarHistorial(registros) {
    const tbody = document.getElementById('historialTableBody');
    
    if (!registros || !registros.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-500">No hay registros en el historial</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    registros.forEach(registro => {
        const fecha = new Date(registro.created_at).toLocaleDateString();
        const hora = new Date(registro.created_at).toLocaleTimeString();

        tbody.innerHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">${fecha}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${hora}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${registro.usuario_nombre || 'Sistema'}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${registro.accion}</td>
                <td class="px-6 py-4 text-right">
                    <button onclick="verDetalles(${registro.id})" 
                            class="bg-black text-white px-3 py-1 rounded-lg text-sm hover:bg-gray-800">
                        Ver
                    </button>
                </td>
            </tr>
        `;
    });
}

function actualizarPaginacion(meta) {
    if (!meta) return;

    const container = document.getElementById('paginationControls');
    currentPage = meta.current_page;
    lastPage = meta.last_page;

    const prevDisabled = currentPage === 1;
    const nextDisabled = currentPage === lastPage;

    let pagesHtml = '';
    const maxPages = 2; // Solo mostrar 2 páginas como en el diseño
    for (let i = currentPage; i < currentPage + maxPages && i <= lastPage; i++) {
        pagesHtml += `<span class="px-3 py-1 text-sm ${i === currentPage ? 'rounded-md bg-gray-200' : 'text-gray-600'}">${i}</span>`;
    }

    container.innerHTML = `
        <div class="flex items-center justify-between w-full">
            <button onclick="irAPagina(${currentPage - 1})" 
                    ${prevDisabled ? 'disabled' : ''}
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                &larr; Anterior
            </button>
            <div class="flex items-center space-x-2">
                ${pagesHtml}
            </div>
            <button onclick="irAPagina(${currentPage + 1})"
                    ${nextDisabled ? 'disabled' : ''}
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                Siguiente &rarr;
            </button>
        </div>
    `;
}

function irAPagina(pagina) {
    if (pagina < 1 || pagina > lastPage) return;
    currentPage = pagina;
    cargarHistorial();
}

async function verDetalles(id) {
    try {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const response = await fetch(`/api/expedientes/${expedienteId}/historial/${id}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Error al cargar detalles');
        
        const data = await response.json();
        // Aquí puedes mostrar los detalles en un modal o en otra vista
        console.log('Detalles:', data);
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los detalles');
    }
}

async function exportToExcel() {
    try {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const response = await fetch(`/api/expedientes/${expedienteId}/historial/export`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Error al exportar historial');

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Historial_Expediente_${expedienteId}_${new Date().toISOString().split('T')[0]}.xlsx`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    } catch (error) {
        console.error('Error:', error);
        alert('Error al exportar el historial');
    }
}
</script>
@endsection
