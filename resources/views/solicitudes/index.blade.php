@extends('layouts.app')
@include('solicitudes.create')
@include('solicitudes.edit')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<div class="min-h-screen bg-gray-100 flex">
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Solicitudes</h1>
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
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="searchInput"
                            class="block w-80 pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Buscar solicitud">
                    </div>

                    <button id="filterButton"
                        class="p-2 bg-gray-200 hover:bg-gray-300 rounded-md border border-gray-300 flex items-center justify-center">
                        <i class="bi bi-funnel-fill text-black text-lg"></i>
                    </button>
                </div>

                <button onclick="openCreateModal()"
                    class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors font-medium">
                    Nueva Solicitud
                </button>
            </div>

            <!-- Tabla -->
            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead style="background-color: #737373;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Ver</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Expediente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Inicio</th>
                        </tr>
                    </thead>
                    <tbody id="solicitudesTableBody" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">Cargando solicitudes...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

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

<!-- Modal de Filtro -->
<div id="filterModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Filtrar solicitudes</h2>

        <label class="block text-sm text-gray-700 mb-2">Estado:</label>
        <select id="estadoFilter"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-6">
            <option value="Todos">Todos</option>
            <option value="Pendiente">Pendiente</option>
            <option value="En revisión">En revisión</option>
            <option value="Aprobada">Aprobada</option>
            <option value="Rechazada">Rechazada</option>
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
        switch (estado.toLowerCase()) {
            case 'pendiente':
                return 'bg-yellow-100 text-yellow-800';
            case 'en revisión':
                return 'bg-blue-100 text-blue-800';
            case 'aprobada':
                return 'bg-green-100 text-green-800';
            case 'rechazada':
                return 'bg-red-100 text-red-800';
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
    let allSolicitudes = [];
    let estadoSeleccionado = 'Todos';

    document.addEventListener('DOMContentLoaded', () => {
        loadSolicitudes(currentPage, perPage);

        document.getElementById('filterButton').addEventListener('click', () => {
            document.getElementById('filterModal').classList.remove('hidden');
        });

        document.getElementById('closeFilterModal').addEventListener('click', () => {
            document.getElementById('filterModal').classList.add('hidden');
        });

        document.getElementById('applyFilter').addEventListener('click', () => {
            estadoSeleccionado = document.getElementById('estadoFilter').value;
            document.getElementById('filterModal').classList.add('hidden');
            loadSolicitudes(1, perPage, document.getElementById('searchInput').value.trim(), estadoSeleccionado);
        });
    });

    async function loadSolicitudes(page = 1, pageSize = perPage, search = '', estado = estadoSeleccionado) {
        const tbody = document.getElementById('solicitudesTableBody');
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-gray-500">Cargando...</td></tr>`;

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const url = `/api/solicitudes?page=${page}&per_page=${pageSize}&search=${search}&estado=${estado}`;
            const res = await fetch(url, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error('Error al obtener solicitudes');
            const data = await res.json();

            allSolicitudes = data.data || [];
            currentPage = data.meta.current_page;
            lastPage = data.meta.last_page;
            perPage = data.meta.per_page;

            renderSolicitudes(allSolicitudes);
            renderPagination();
        } catch (error) {
            console.error(error);
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-red-500 py-6">Error al cargar solicitudes</td></tr>`;
        }
    }

    function renderSolicitudes(list) {
        const tbody = document.getElementById('solicitudesTableBody');
        if (!list.length) {
            tbody.innerHTML = `<tr><td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay solicitudes registradas</td></tr>`;
            return;
        }

        tbody.innerHTML = '';
        list.forEach(solicitud => {
            tbody.innerHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <button onclick="verSolicitud(${solicitud.id})" class="bg-black text-white px-3 py-1 rounded text-xs">Ver</button>
                    </td>
                    <td class="px-6 py-4">${String(solicitud.numero).padStart(4, '0')} - ${formatDate(solicitud.fecha_creacion)}</td>
                    <td class="px-6 py-4">${formatDate(solicitud.fecha_creacion)}</td>
                </tr>`;
        });
    }

    function renderPagination() {
        const container = document.getElementById('paginationControls');
        const prevDisabled = currentPage === 1;
        const nextDisabled = currentPage === lastPage;

        let pagesHtml = '';
        const maxPages = 2;
        for (let i = currentPage; i < currentPage + maxPages && i <= lastPage; i++) {
            pagesHtml += `<span class="px-3 py-1 text-sm ${i === currentPage ? 'rounded-md bg-gray-200' : 'text-gray-600'}">${i}</span>`;
        }

        container.innerHTML = `
            <div class="flex items-center justify-between w-full">
                <button onclick="goToPage(${currentPage - 1})" 
                        ${prevDisabled ? 'disabled' : ''}
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                    &larr; Anterior
                </button>
                <div class="flex items-center space-x-2">
                    ${pagesHtml}
                </div>
                <button onclick="goToPage(${currentPage + 1})"
                        ${nextDisabled ? 'disabled' : ''}
                        class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500">
                    Siguiente &rarr;
                </button>
            </div>
        `;
    }

    function goToPage(page) {
        if (page < 1 || page > lastPage) return;
        loadSolicitudes(page);
    }

    async function deleteSolicitud(id) {
        if (!confirm('¿Seguro que deseas eliminar esta solicitud?')) return;

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const res = await fetch(`/api/solicitudes/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                alert('Solicitud eliminada correctamente');
                loadSolicitudes(currentPage);
            } else {
                alert('Error al eliminar solicitud');
            }
        } catch (error) {
            console.error(error);
            alert('Error al conectar con el servidor');
        }
    }

    async function exportToExcel() {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            console.log('Iniciando exportación...');

            const response = await fetch('/api/solicitudes/export', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                console.error('Error en la respuesta:', response.status);
                const errorData = await response.json();
                console.error('Detalle del error:', errorData);

                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                throw new Error(errorData.error || 'Error al exportar datos');
            }

            const data = await response.json();
            console.log('Datos recibidos:', data);

            // Transformar los datos para el Excel
            const excelData = data.map(sol => ({
                'ID': sol.id,
                'Nº SOLICITUD': String(sol.numero).padStart(4, '0'),
                'TIPO': sol.tipo,
                'ESTADO': sol.estado,
                'FECHA': formatDate(sol.fecha_creacion),
                'ACTUALIZACIÓN': formatDate(sol.fecha_actualizacion)
            }));

            // Crear libro de Excel
            const ws = XLSX.utils.json_to_sheet(excelData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Solicitudes");

            // Ajustar el ancho de las columnas
            const colWidths = [
                { wch: 8 },  // ID
                { wch: 15 }, // Nº SOLICITUD
                { wch: 20 }, // TIPO
                { wch: 15 }, // ESTADO
                { wch: 12 }, // FECHA
                { wch: 15 }  // ACTUALIZACIÓN
            ];
            ws['!cols'] = colWidths;

            // Descargar archivo
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            XLSX.writeFile(wb, `Solicitudes_${day}-${month}-${year}.xlsx`);
            console.log('Exportación completada');

        } catch (error) {
            console.error('Error detallado:', error);
            alert('Error al exportar los datos: ' + error.message);
        }
    }

    // Buscar con debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadSolicitudes(1, perPage, e.target.value.trim(), estadoSeleccionado);
        }, 300);
    });

    function verDocumentos(id) {
        window.location.href = `/solicitudes/documentos?id=${id}`;
    }

    function verHistorial(id) {
        window.location.href = `/solicitudes/historial?id=${id}`;
    }
</script>
@endsection