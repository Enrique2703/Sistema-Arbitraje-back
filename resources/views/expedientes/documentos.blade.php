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
                        <th class="w-24 px-6 py-3 text-center text-xs font-medium text-white uppercase"></th>
                        <th class="w-24 px-6 py-3 text-center text-xs font-medium text-white uppercase">Habilitado</th>
                        <th class="w-32 px-6 py-3 text-center text-xs font-medium text-white uppercase">Fecha</th>
                        <th class="w-32 px-6 py-3 text-center text-xs font-medium text-white uppercase">Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Título</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase">Usuario</th>
                        <th class="w-32 px-6 py-3 text-left text-xs font-medium text-white uppercase">Rol</th>
                        <th class="w-48 px-6 py-3 text-center text-xs font-medium text-white uppercase"></th>
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
<div id="documentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Nuevo documento</h2>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form id="documentForm" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                <input type="text" name="titulo" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Placeholder">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios</label>
                <textarea name="comentarios" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Placeholder"></textarea>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Documento</label>
                <div class="border border-gray-300 rounded-lg p-4">
                    <div class="flex items-center justify-center">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="text-sm text-gray-600">Adjuntar archivos</span>
                            <input type="file" class="hidden" name="documento" required>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Cancelar
                </button>
                <button type="submit" 
                    class="px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800">
                    Crear
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
        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay documentos registrados</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    documentos.forEach(doc => {
        const fecha = new Date(doc.created_at).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        const hora = new Date(doc.created_at).toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        tbody.innerHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-center">
                    <span class="font-medium">${doc.cedula || 'Cédula'}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <input type="checkbox" ${doc.habilitado ? 'checked' : ''} class="form-checkbox h-5 w-5 text-gray-600" disabled>
                </td>
                <td class="px-6 py-4 text-center">${fecha}</td>
                <td class="px-6 py-4 text-center">${hora}</td>
                <td class="px-6 py-4">${doc.titulo}</td>
                <td class="px-6 py-4">${doc.usuario_nombre || 'Sistema'}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full ${doc.rol === 'Demandado' ? 'bg-gray-200' : 'bg-gray-200'}">
                        ${doc.rol || 'Demandante'}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex justify-end space-x-2">
                        <button onclick="revisarDocumento(${doc.id})" class="bg-black text-white px-3 py-1 rounded text-sm">Revisar</button>
                        <button onclick="verDocumento(${doc.id})" class="bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm">Ver</button>
                        <button onclick="generarDocumentos(${doc.id})" class="bg-black text-white px-3 py-1 rounded text-sm">Generados</button>
                    </div>
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

async function generarDocumentos(id) {
    // Implementar función para mostrar documentos generados
    console.log('Mostrar documentos generados:', id);
}

function actualizarPaginacion(meta) {
    const container = document.getElementById('paginationControls');
    if (!meta) return;

    const currentPage = meta.current_page;
    const lastPage = meta.last_page;
    const prevDisabled = currentPage <= 1;
    const nextDisabled = currentPage >= lastPage;

    let pagesHtml = '';
    const maxPages = 4;
    let start = Math.max(1, currentPage - 2);
    let end = Math.min(lastPage, start + maxPages - 1);

    if (start > 1) {
        pagesHtml += `<button onclick="irAPagina(1)" class="mx-1 text-sm text-gray-500">1</button>`;
        if (start > 2) pagesHtml += `<span class="mx-1 text-sm text-gray-400">...</span>`;
    }

    for (let i = start; i <= end; i++) {
        if (i === currentPage) {
            pagesHtml += `<button class="mx-1 px-2 py-1 text-sm bg-gray-100 rounded">${i}</button>`;
        } else {
            pagesHtml += `<button onclick="irAPagina(${i})" class="mx-1 text-sm text-gray-500">${i}</button>`;
        }
    }

    if (end < lastPage) {
        if (end < lastPage - 1) pagesHtml += `<span class="mx-1 text-sm text-gray-400">...</span>`;
        pagesHtml += `<button onclick="irAPagina(${lastPage})" class="mx-1 text-sm text-gray-500">${lastPage}</button>`;
    }

    container.innerHTML = `
        <div class="flex items-center justify-between w-full">
            <button ${prevDisabled ? 'disabled' : ''} onclick="irAPagina(${currentPage-1})" 
                class="px-3 py-2 text-sm ${prevDisabled ? 'text-gray-400' : 'text-gray-700'}">&larr; Anterior</button>
            <div class="flex items-center">${pagesHtml}</div>
            <button ${nextDisabled ? 'disabled' : ''} onclick="irAPagina(${currentPage+1})" 
                class="px-3 py-2 text-sm ${nextDisabled ? 'text-gray-400' : 'text-gray-700'}">Siguiente &rarr;</button>
        </div>
    `;
}

function irAPagina(pagina) {
    currentPage = pagina;
    cargarDocumentos();
}

async function deleteDocumento(id) {
    if (!confirm('¿Está seguro que desea eliminar este documento?')) return;

    try {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const response = await fetch(`/api/documentos/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Error al eliminar el documento');
        }

        alert('Documento eliminado correctamente');
        await cargarDocumentos();
    } catch (error) {
        console.error('Error:', error);
        alert('Error al eliminar el documento');
    }
}

// Cerrar modal al hacer clic fuera
document.getElementById('documentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Mostrar nombre del archivo seleccionado
document.querySelector('input[name="documento"]').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
        const fileLabel = this.parentElement.querySelector('span');
        fileLabel.textContent = fileName;
    }
});

document.getElementById('documentForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    try {
        const formData = new FormData();
        const expedienteId = new URLSearchParams(window.location.search).get('id');
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');

        // Agregar campos al FormData
        formData.append('titulo', this.titulo.value);
        formData.append('comentarios', this.comentarios.value);
        formData.append('documento', this.documento.files[0]);
        formData.append('expediente_id', expedienteId);

        const response = await fetch('/api/documentos', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                // No incluir Content-Type aquí, fetch lo establecerá automáticamente con el boundary correcto
            },
            body: formData
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Error al crear el documento');
        }

        // Mostrar mensaje de éxito
        alert('Documento creado exitosamente');
        closeModal();
        await cargarDocumentos(); // Recargar la lista de documentos
    } catch (error) {
        console.error('Error:', error);
        alert(error.message || 'Error al crear el documento');
    }
});
</script>
@endsection
