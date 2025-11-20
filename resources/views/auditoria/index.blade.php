@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<div class="min-h-screen bg-gray-100 flex">
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Auditoría</h1>
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
                            placeholder="Buscar por usuario, acción, expediente...">
                    </div>

                    <button id="filterButton"
                        class="p-2 bg-gray-200 hover:bg-gray-300 rounded-md border border-gray-300 flex items-center justify-center">
                        <i class="bi bi-funnel-fill text-black text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead style="background-color: #737373;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nombre del Expediente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Acción</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Ver</th>
                        </tr>
                    </thead>
                    <tbody id="auditoriaTableBody" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Cargando registros...</td>
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

<!-- Modal de Filtros -->
<div id="filterModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Filtrar registros</h2>

        <label class="block text-sm text-gray-700 mb-2">Módulo:</label>
        <select id="moduloFilter"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4">
            <option value="Todos">Todos</option>
            <option value="expedientes">Expedientes</option>
            <option value="documentos">Documentos</option>
            <option value="cédulas">Cédulas</option>
            <option value="usuarios">Usuarios</option>
            <option value="partícipes">Partícipes</option>
        </select>

        <label class="block text-sm text-gray-700 mb-2">Tipo de acción:</label>
        <select id="tipoAccionFilter"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-6">
            <option value="Todos">Todos</option>
            <option value="crear">Crear</option>
            <option value="editar">Editar</option>
            <option value="eliminar">Eliminar</option>
            <option value="subir">Subir archivo</option>
            <option value="descargar">Descargar</option>
            <option value="aprobar">Aprobar</option>
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

<!-- Modal de Detalles -->
<div id="detalleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-3xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Detalles de Auditoría</h2>
            <button onclick="cerrarDetalleModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="detalleContenido" class="space-y-4">
            <!-- El contenido se llenará dinámicamente -->
        </div>

        <div class="flex justify-end mt-6">
            <button onclick="cerrarDetalleModal()"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let lastPage = 1;
let perPage = 7;
let moduloSeleccionado = 'Todos';
let tipoAccionSeleccionado = 'Todos';

document.addEventListener('DOMContentLoaded', () => {
    loadAuditoria();
    configurarBuscador();
    configurarFiltros();
});

function configurarBuscador() {
    const searchInput = document.getElementById('searchInput');
    let timeout = null;

    searchInput.addEventListener('input', function(e) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            loadAuditoria(1, e.target.value.trim());
        }, 300);
    });
}

function configurarFiltros() {
    document.getElementById('filterButton').addEventListener('click', () => {
        document.getElementById('filterModal').classList.remove('hidden');
    });

    document.getElementById('closeFilterModal').addEventListener('click', () => {
        document.getElementById('filterModal').classList.add('hidden');
    });

    document.getElementById('applyFilter').addEventListener('click', () => {
        moduloSeleccionado = document.getElementById('moduloFilter').value;
        tipoAccionSeleccionado = document.getElementById('tipoAccionFilter').value;
        document.getElementById('filterModal').classList.add('hidden');
        loadAuditoria(1);
    });
}

async function loadAuditoria(page = 1, search = '') {
    const tbody = document.getElementById('auditoriaTableBody');
    
    try {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-gray-500">Cargando...</td></tr>';

        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const url = `/api/auditoria?page=${page}&search=${search}&modulo=${moduloSeleccionado}&tipo_accion=${tipoAccionSeleccionado}`;
        const response = await fetch(url, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Error al cargar registros');
        
        const data = await response.json();
        currentPage = data.meta.current_page;
        lastPage = data.meta.last_page;
        perPage = data.meta.per_page;

        renderAuditoria(data.registros);
        renderPagination();

    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-red-500">Error al cargar los registros</td></tr>';
    }
}

function renderAuditoria(registros) {
    const tbody = document.getElementById('auditoriaTableBody');
    
    if (!registros || !registros.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-gray-500">No hay registros</td></tr>';
        return;
    }

    tbody.innerHTML = '';
    registros.forEach(registro => {
        const fecha = new Date(registro.created_at).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        const hora = new Date(registro.created_at).toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit'
        });

        tbody.innerHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">${registro.expediente || '—'}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${fecha}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${hora}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${registro.usuario || 'Sistema'}</td>
                <td class="px-6 py-4 text-sm text-gray-900">${registro.accion}</td>
                <td class="px-6 py-4 text-right">
                    <button onclick="verDetalle(${registro.id})" 
                            class="bg-black text-white px-3 py-1 rounded-lg text-sm hover:bg-gray-800">
                        Ver
                    </button>
                </td>
            </tr>
        `;
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
    const searchTerm = document.getElementById('searchInput').value.trim();
    loadAuditoria(page, searchTerm);
}

async function verDetalle(id) {
    try {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const response = await fetch(`/api/auditoria/${id}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Error al cargar detalles');
        
        const data = await response.json();
        mostrarDetalleModal(data);
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los detalles');
    }
}

function mostrarDetalleModal(auditoria) {
    const fecha = new Date(auditoria.created_at).toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    let html = `
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                <p class="text-sm text-gray-900">${auditoria.usuario_nombre || 'Sistema'}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora</label>
                <p class="text-sm text-gray-900">${fecha}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Módulo</label>
                <p class="text-sm text-gray-900">${auditoria.modulo || '—'}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de acción</label>
                <p class="text-sm text-gray-900">${auditoria.tipo_accion || '—'}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expediente</label>
                <p class="text-sm text-gray-900">${auditoria.expediente || 'N/A'}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección IP</label>
                <p class="text-sm text-gray-900">${auditoria.ip || '—'}</p>
            </div>
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
            <p class="text-sm text-gray-900">${auditoria.accion}</p>
        </div>
        
        ${auditoria.detalle ? `
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Detalle</label>
            <p class="text-sm text-gray-900">${auditoria.detalle}</p>
        </div>
        ` : ''}
        
        ${auditoria.datos_anteriores ? `
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Datos anteriores</label>
            <pre class="text-xs text-gray-700 bg-gray-50 p-3 rounded border border-gray-200 overflow-x-auto">${JSON.stringify(auditoria.datos_anteriores, null, 2)}</pre>
        </div>
        ` : ''}
        
        ${auditoria.datos_nuevos ? `
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Datos nuevos</label>
            <pre class="text-xs text-gray-700 bg-gray-50 p-3 rounded border border-gray-200 overflow-x-auto">${JSON.stringify(auditoria.datos_nuevos, null, 2)}</pre>
        </div>
        ` : ''}
    `;

    document.getElementById('detalleContenido').innerHTML = html;
    document.getElementById('detalleModal').classList.remove('hidden');
}

function cerrarDetalleModal() {
    document.getElementById('detalleModal').classList.add('hidden');
}

// SheetJS CDN para exportar a .xlsx real
if (!window.XLSX) {
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
    document.head.appendChild(script);
}

function exportToExcel() {
    // Obtener los datos de la tabla
    const table = document.querySelector('table');
    const rows = Array.from(table.querySelectorAll('tbody tr'));
    const headers = ['MODULO', 'EXPEDIENTE', 'FECHA', 'HORA', 'USUARIO', 'ACCION', 'TIPO'];
    let data = [headers];

    rows.forEach(row => {
        if (row.querySelectorAll('td').length < 8) return;
        const cells = row.querySelectorAll('td');
        data.push([
            cells[0].innerText.trim(), // Módulo
            cells[1].innerText.trim(), // Expediente
            cells[2].innerText.trim(), // Fecha
            cells[3].innerText.trim(), // Hora
            cells[4].innerText.trim(), // Usuario
            cells[5].innerText.trim(), // Acción
            cells[6].innerText.trim()  // Tipo
        ]);
    });

    function descargarXLSX() {
        const ws = XLSX.utils.aoa_to_sheet(data);
        // Encabezados en negrita y fondo gris
        const headerStyle = {
            font: { bold: true },
            fill: { patternType: 'solid', fgColor: { rgb: 'D9D9D9' } },
            alignment: { horizontal: 'center' },
            border: {
                top: { style: 'thin', color: { rgb: '000000' } },
                left: { style: 'thin', color: { rgb: '000000' } },
                bottom: { style: 'thin', color: { rgb: '000000' } },
                right: { style: 'thin', color: { rgb: '000000' } }
            }
        };
        ['A1','B1','C1','D1','E1','F1','G1'].forEach(cell => {
            if (!ws[cell]) return;
            ws[cell].s = headerStyle;
        });
        ws['!cols'] = [
            { wch: 16 }, // Módulo
            { wch: 14 }, // Expediente
            { wch: 12 }, // Fecha
            { wch: 10 }, // Hora
            { wch: 22 }, // Usuario
            { wch: 50 }, // Acción
            { wch: 12 }  // Tipo
        ];
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Auditoria');
        // Formato de fecha: dd-mm-yyyy
        const now = new Date();
        const dd = String(now.getDate()).padStart(2, '0');
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const yyyy = now.getFullYear();
        const fecha = `${dd}-${mm}-${yyyy}`;
        XLSX.writeFile(wb, `Auditoria_${fecha}.xlsx`);
    }
    if (window.XLSX) {
        descargarXLSX();
    } else {
        script.onload = descargarXLSX;
    }
}
</script>
@endsection