@extends('layouts.app')

@section('content')
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
                            placeholder="Buscar">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Expediente</th>
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
<script>
let currentPage = 1;
let lastPage = 1;
let perPage = 7;

document.addEventListener('DOMContentLoaded', () => {
    loadAuditoria();
    configurarBuscador();
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

async function loadAuditoria(page = 1, search = '') {
    const tbody = document.getElementById('auditoriaTableBody');
    
    try {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-gray-500">Cargando...</td></tr>';

        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const response = await fetch(`/api/auditoria?page=${page}&search=${search}`, {
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
        const fecha = new Date(registro.created_at).toLocaleDateString();
        const hora = new Date(registro.created_at).toLocaleTimeString();

        tbody.innerHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 text-sm text-gray-900">${registro.expediente}</td>
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
        // Aquí puedes mostrar los detalles en un modal o en otra vista
        console.log('Detalles:', data);
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los detalles');
    }
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
    const headers = ['EXPEDIENTE', 'FECHA', 'HORA', 'USUARIO', 'ACCION'];
    let data = [headers];

    rows.forEach(row => {
        if (row.querySelectorAll('td').length < 6) return;
        const cells = row.querySelectorAll('td');
        data.push([
            cells[0].innerText.trim(), // Expediente
            cells[1].innerText.trim(), // Fecha
            cells[2].innerText.trim(), // Hora
            cells[3].innerText.trim(), // Usuario
            cells[4].innerText.trim()  // Acción
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
        ['A1','B1','C1','D1','E1'].forEach(cell => {
            if (!ws[cell]) return;
            ws[cell].s = headerStyle;
        });
        ws['!cols'] = [
            { wch: 14 }, // Expediente
            { wch: 12 }, // Fecha
            { wch: 10 }, // Hora
            { wch: 22 }, // Usuario
            { wch: 50 }  // Acción
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