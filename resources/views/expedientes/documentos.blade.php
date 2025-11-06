@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

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
                    <h1 class="text-2xl font-semibold text-gray-900">Documentos</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="exportToFolder()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
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
                            placeholder="Buscar">
                    </div>

                    <button id="filterButton"
                        class="p-2 bg-gray-200 hover:bg-gray-300 rounded-md border border-gray-300 flex items-center justify-center">
                        <i class="bi bi-funnel-fill text-black text-lg"></i>
                    </button>
                </div>

                <button onclick="openCreateModal()"
                    class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors font-medium">
                    Nuevo documento
                </button>
            </div>

        <!-- Tabla de documentos -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead style="background-color: #737373;">
                    <tr>
                        <th class="w-16 px-6 py-3">
                            <div class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </div>
                        </th>
                        <th class="w-24 px-6 py-3 text-left text-xs font-medium text-white uppercase">Habilitado</th>
                        <th class="w-32 px-6 py-3 text-left text-xs font-medium text-white uppercase">Fecha</th>
                        <th class="w-32 px-6 py-3 text-left text-xs font-medium text-white uppercase">Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Título</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Usuario</th>
                        <th class="w-32 px-6 py-3 text-left text-xs font-medium text-white uppercase">Rol</th>
                        <th class="w-48 px-6 py-3 text-right text-xs font-medium text-white uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="documentosTableBody">
                    <!-- Las filas se generarán dinámicamente -->
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

<!-- Modal de Nuevo Documento -->
<div id="documentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Nuevo Documento</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form id="documentForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Título del documento</label>
                <input type="text" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Archivo</label>
                <input type="file" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-black rounded-md hover:bg-gray-800">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let expedienteId = null;
let currentPage = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Obtener el ID del expediente de la URL
    const urlParams = new URLSearchParams(window.location.search);
    expedienteId = urlParams.get('id');
    
    if (!expedienteId) {
        alert('No se proporcionó ID del expediente');
        window.location.href = '/expedientes';
        return;
    }

    cargarDocumentos();
    configurarBuscador();
});

function configurarBuscador() {
    const searchInput = document.getElementById('searchInput');
    let timeout = null;

    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            cargarDocumentos();
        }, 300);
    });
}

async function cargarDocumentos() {
    const tbody = document.getElementById('documentosTableBody');
    const searchTerm = document.getElementById('searchInput').value;

    try {
        tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Cargando...</td></tr>';

        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const response = await fetch(`/api/expedientes/${expedienteId}/documentos?search=${searchTerm}&page=${currentPage}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Error al cargar documentos');
        
        const data = await response.json();
        renderizarDocumentos(data.documentos);
        actualizarPaginacion(data.meta);

    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-red-500">Error al cargar los documentos</td></tr>';
    }
}

function renderizarDocumentos(documentos) {
    const tbody = document.getElementById('documentosTableBody');
    
    if (!documentos.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">No hay documentos registrados</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    documentos.forEach(doc => {
        const fecha = new Date(doc.created_at).toLocaleDateString();
        const hora = new Date(doc.created_at).toLocaleTimeString();

        tbody.innerHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">
                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${doc.habilitado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${doc.habilitado ? '✓' : '✗'}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">${fecha}</td>
                <td class="px-6 py-4 text-sm text-gray-500">${hora}</td>
                <td class="px-6 py-4">${doc.nombre}</td>
                <td class="px-6 py-4">${doc.usuario_nombre}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full ${doc.rol === 'Demandante' ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800'}">
                        ${doc.rol}
                    </span>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <button onclick="revisarDocumento(${doc.id})" class="bg-black text-white px-2 py-1 rounded text-sm">Revisar</button>
                    <button onclick="verDocumento(${doc.id})" class="bg-black text-white px-2 py-1 rounded text-sm">Ver</button>
                    <button onclick="generarDocumento(${doc.id})" class="bg-black text-white px-2 py-1 rounded text-sm">Generados</button>
                </td>
            </tr>
        `;
    });
}

async function exportToFolder() {
    try {
        const expedienteId = new URLSearchParams(window.location.search).get('id');
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        
        const response = await fetch(`/api/expedientes/${expedienteId}/documentos/export`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Error al exportar documentos');
        }

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `Documentos_Expediente_${expedienteId}_${new Date().toISOString().split('T')[0]}.zip`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
    } catch (error) {
        console.error('Error:', error);
        alert('Error al exportar los documentos');
    }
}

function openCreateModal() {
    document.getElementById('documentModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('documentModal').classList.add('hidden');
    document.getElementById('documentForm').reset();
}

async function revisarDocumento(id) {
    // Implementar función de revisión
    console.log('Revisar documento:', id);
}

async function verDocumento(id) {
    // Implementar función para ver documento
    console.log('Ver documento:', id);
}

async function generarDocumento(id) {
    // Implementar función para documentos generados
    console.log('Documentos generados:', id);
}

// Cerrar modal al hacer clic fuera
document.getElementById('documentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

document.getElementById('documentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    // Implementar lógica de guardado
    closeModal();
    await cargarDocumentos();
});
</script>
@endsection
