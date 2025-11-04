@extends('layouts.app')
@include('expedientes.create')
@include('expedientes.edit')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

<div class="min-h-screen bg-gray-100 flex">
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Expedientes</h1>
                <div class="flex items-center space-x-4">
                    <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
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
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="searchInput"
                            class="block w-80 pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Buscar">
                    </div>

                    <!-- Botón de filtro -->
                    <button id="filterButton"
                        class="p-2 bg-gray-200 hover:bg-gray-300 rounded-md border border-gray-300 flex items-center justify-center">
                        <i class="bi bi-funnel-fill text-black text-lg"></i>
                    </button>
                </div>

                <button onclick="openCreateModal()"
                    class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors font-medium">
                    Nuevo expediente
                </button>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead style="background-color: #737373;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Partícipes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Documentos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Inicio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Actualización</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="expedientesTableBody" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Cargando expedientes...</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Paginación -->
                <div id="paginationContainer" class="px-6 py-4 border-t bg-white">
                    <nav id="paginationControls" class="flex items-center justify-between" aria-label="Pagination"></nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 🔽 MODAL DE FILTRO -->
<div id="filterModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Filtrar expedientes</h2>

        <label class="block text-sm text-gray-700 mb-2">Estado:</label>
        <select id="estadoFilter"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-6">
            <option value="Todos">Todos</option>
            <option value="Archivado">Archivado</option>
            <option value="En tramite">En trámite</option>
            <option value="Suspendido">Suspendido</option>
            <option value="Concluido">Concluido</option>
        </select>

        <div class="flex justify-end space-x-3">
            <button id="closeFilterModal" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 text-gray-800">
                Cancelar
            </button>
            <button id="applyFilter"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Aplicar
            </button>
        </div>
    </div>
</div>

<script>
    function getEstadoClass(estado) {
        switch(estado.toLowerCase()) {
            case 'en trámite':
            case 'en tramite':
                return 'bg-yellow-100 text-yellow-800';
            case 'suspendido':
                return 'bg-red-100 text-red-800';
            case 'archivado':
                return 'bg-green-100 text-green-800';
            case 'concluido':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    function formatDate(dateString) {
        if (!dateString) return '—';
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    let currentPage = 1;
    let lastPage = 1;
    let perPage = 7;
    let allExpedientes = [];
    let estadoSeleccionado = 'Todos';

    document.addEventListener('DOMContentLoaded', () => {
        loadExpedientes(currentPage, perPage);

        document.getElementById('filterButton').addEventListener('click', () => {
            document.getElementById('filterModal').classList.remove('hidden');
        });

        document.getElementById('closeFilterModal').addEventListener('click', () => {
            document.getElementById('filterModal').classList.add('hidden');
        });

        document.getElementById('applyFilter').addEventListener('click', () => {
            estadoSeleccionado = document.getElementById('estadoFilter').value;
            document.getElementById('filterModal').classList.add('hidden');
            loadExpedientes(1, perPage, document.getElementById('searchInput').value.trim(), estadoSeleccionado);
        });
    });

    async function loadExpedientes(page = 1, pageSize = perPage, search = '', estado = estadoSeleccionado) {
        const tbody = document.getElementById('expedientesTableBody');
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-6 text-gray-500">Cargando...</td></tr>`;

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const url = `/api/expedientes?page=${page}&per_page=${pageSize}&search=${search}&estado=${estado}`;
            const res = await fetch(url, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error('Error al obtener expedientes');
            const data = await res.json();

            allExpedientes = data.registros || [];
            currentPage = data.meta.current_page;
            lastPage = data.meta.last_page;
            perPage = data.meta.per_page;

            renderExpedientes(allExpedientes);
            renderPagination();
        } catch (error) {
            console.error(error);
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-red-500 py-6">Error al cargar expedientes</td></tr>`;
        }
    }

    function renderExpedientes(list) {
        const tbody = document.getElementById('expedientesTableBody');
        if (!list.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-6 text-gray-500">No hay expedientes registrados</td></tr>`;
            return;
        }

        tbody.innerHTML = '';
        list.forEach(exp => {
            tbody.innerHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">${String(exp.id).padStart(4, '0')}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${getEstadoClass(exp.estado)}">
                            ${exp.estado}
                        </span>
                    </td>
                    <td class="px-6 py-4">${exp.cantidad_participes || 0}</td>
                    <td class="px-6 py-4">${exp.expediente || '0'}</td>
                    <td class="px-6 py-4">${formatDate(exp.fecha_creacion) || '—'}</td>
                    <td class="px-6 py-4">${formatDate(exp.fecha_actualizacion) || '—'}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-3">
                            <button onclick="openEditModal(${exp.id})" class="text-gray-900 font-medium hover:underline">Editar</button>
                            <button onclick="deleteExpediente(${exp.id})" class="text-red-500 hover:underline">Eliminar</button>
                        </div>
                    </td>
                </tr>`;
        });
    }

    function renderPagination() {
        const container = document.getElementById('paginationControls');
        container.innerHTML = '';

        const prevDisabled = currentPage <= 1;
        const nextDisabled = currentPage >= lastPage;

        const prevBtn = `<button ${prevDisabled ? 'disabled' : ''} onclick="goToPage(${currentPage-1})" class="px-3 py-2 text-sm ${prevDisabled ? 'text-gray-400' : 'text-gray-700'}">&larr; Anterior</button>`;
        const nextBtn = `<button ${nextDisabled ? 'disabled' : ''} onclick="goToPage(${currentPage+1})" class="px-3 py-2 text-sm ${nextDisabled ? 'text-gray-400' : 'text-gray-700'}">Siguiente &rarr;</button>`;

        let pagesHtml = '';
        const maxPages = 4;
        let start = Math.max(1, currentPage - 2);
        let end = Math.min(lastPage, start + maxPages - 1);

        for (let i = start; i <= end; i++) {
            pagesHtml += `<button onclick="goToPage(${i})" class="mx-1 px-2 py-1 text-sm ${i === currentPage ? 'bg-gray-100 rounded' : 'text-gray-500'}">${i}</button>`;
        }

        container.innerHTML = `
            <div class="flex items-center justify-between w-full">
                <div>${prevBtn}</div>
                <div class="flex items-center">${pagesHtml}</div>
                <div>${nextBtn}</div>
            </div>
        `;
    }

    function goToPage(page) {
        if (page < 1 || page > lastPage) return;
        loadExpedientes(page);
    }

    async function deleteExpediente(id) {
        if (!confirm('¿Seguro que deseas eliminar este expediente?')) return;

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const res = await fetch(`/api/expedientes/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                alert('Expediente eliminado correctamente');
                loadExpedientes(currentPage);
            } else {
                alert('Error al eliminar expediente');
            }
        } catch (error) {
            console.error(error);
            alert('Error al conectar con el servidor');
        }
    }

    // Buscar con debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadExpedientes(1, perPage, e.target.value.trim(), estadoSeleccionado);
        }, 300);
    });
</script>
@endsection