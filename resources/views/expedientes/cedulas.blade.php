@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<div class="min-h-screen bg-gray-100 flex">
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="/expedientes/documentos" class="flex items-center text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-2xl font-semibold text-gray-900">Cédulas generadas</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="exportToExcel()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <img src="{{ asset('img/folder.png') }}" alt="Folder Icon" class="w-6 h-6">
                    </button>
                </div>
            </div>
        </header>

        <div class="flex-1 p-6">
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

                <button id="filterButton"
                    class="p-2 bg-gray-200 hover:bg-gray-300 rounded-md border border-gray-300 flex items-center justify-center">
                    <i class="bi bi-funnel-fill text-black text-lg"></i>
                </button>
            </div>
            <br>
            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead style="background-color: #737373;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Enviado a</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cedulasTableBody" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Cargando cédulas...</td>
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

<script>
    let currentPage = 1;
    let lastPage = 1;
    let perPage = 7;
    let allCedulas = [];

    let expedienteId = null;
    let documentoId = null;

    document.addEventListener('DOMContentLoaded', () => {
        // Obtener IDs de la URL
        const urlParams = new URLSearchParams(window.location.search);
        expedienteId = urlParams.get('expediente_id');
        documentoId = urlParams.get('documento_id');

        if (!expedienteId) {
            alert('No se proporcionó ID del expediente');
            window.location.href = '/expedientes';
            return;
        }

        cargarCedulas(currentPage, perPage);
        configurarBuscador();
    });

    function configurarBuscador() {
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;

        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.trim();
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                currentPage = 1;
                cargarCedulas().then(() => {
                    console.log('Búsqueda completada para:', searchTerm);
                }).catch(error => {
                    console.error('Error en la búsqueda:', error);
                });
            }, 300);
        });
    }

    async function cargarCedulas(page = 1, pageSize = perPage) {
        const tbody = document.getElementById('cedulasTableBody');
        const searchTerm = document.getElementById('searchInput').value.trim();

        try {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Cargando...</td></tr>';

            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const params = new URLSearchParams({
                search: searchTerm,
                page: page,
                per_page: pageSize
            });

            const response = await fetch(`/api/cedulas/?documentos_id=${documentoId}&${params.toString()}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Error al obtener cédulas');
            const data = await response.json();

            console.log('Datos recibidos:', data);

            allCedulas = data.registros || [];
            currentPage = data.meta.current_page;
            lastPage = data.meta.last_page;
            perPage = data.meta.per_page;

            console.log('Cédulas cargadas:', allCedulas.length);

            renderizarCedulas(allCedulas);
            renderizarPaginacion(data.meta);

        } catch (error) {
            console.error('Error:', error);
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error al cargar las cédulas</td></tr>';
        }
    }

    function renderizarCedulas(cedulas) {
        const tbody = document.getElementById('cedulasTableBody');
        const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();

        console.log(' Renderizando cédulas:', cedulas.length);

        // Filtrar cédulas según el término de búsqueda
        const cedulasFiltradas = cedulas.filter(cedula => {
            if (searchTerm) {
                const usuarioNombre = cedula.usuario?.nombres || cedula.usuario_nombre || '';
                const usuarioCoincide = usuarioNombre.toLowerCase().includes(searchTerm);
                const destinatariosCoinciden = (cedula.enviado_a || '').toLowerCase().includes(searchTerm);
                return usuarioCoincide || destinatariosCoinciden;
            }
            return true;
        });

        console.log(' Cédulas filtradas:', cedulasFiltradas.length);

        if (!cedulasFiltradas.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No se encontraron cédulas</td></tr>';
            return;
        }

        tbody.innerHTML = '';
        cedulasFiltradas.forEach(cedula => {
            const fecha = new Date(cedula.created_at).toLocaleDateString('es-ES');
            const hora = new Date(cedula.created_at).toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const usuarioNombre = cedula.usuario?.nombres || cedula.usuario_nombre || 'Sistema';
            const enviadoANombres = cedula.enviado_a_nombres || cedula.enviado_a || '—';

            tbody.innerHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${fecha}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${hora}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${usuarioNombre}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${enviadoANombres}</td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="verCedula(${cedula.id})" class="bg-black text-white px-3 py-1 rounded text-sm">Ver</button>
                    </td>
                </tr>`;
        });
    }

    function renderizarPaginacion(meta) {
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
        if (pagina < 1 || pagina > lastPage) return;
        currentPage = pagina;
        cargarCedulas(pagina);
    }

    async function verCedula(id) {
        // Implementar la lógica para ver la cédula
        console.log('Ver cédula:', id);
    }

    async function exportToExcel() {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');

            const response = await fetch('/api/cedulas/export', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Error al exportar cédulas');
            }

            const data = await response.json();

            // Transformar los datos para el Excel
            const excelData = data.map(cedula => ({
                'FECHA': new Date(cedula.created_at).toLocaleDateString('es-ES'),
                'HORA': new Date(cedula.created_at).toLocaleTimeString('es-ES'),
                'USUARIO': cedula.usuario_nombre || 'Sistema',
                'ENVIADO A': cedula.enviado_a || '—'
            }));

            // Crear libro de Excel
            const ws = XLSX.utils.json_to_sheet(excelData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Cédulas");

            // Ajustar el ancho de las columnas
            const colWidths = [{
                    wch: 12
                }, // FECHA
                {
                    wch: 10
                }, // HORA
                {
                    wch: 30
                }, // USUARIO
                {
                    wch: 40
                } // ENVIADO A
            ];
            ws['!cols'] = colWidths;

            // Descargar archivo
            const today = new Date();
            const fileName = `Cedulas_${today.toISOString().split('T')[0]}.xlsx`;
            XLSX.writeFile(wb, fileName);

        } catch (error) {
            console.error('Error:', error);
            alert('Error al exportar las cédulas');
        }
    }
</script>
@endsection