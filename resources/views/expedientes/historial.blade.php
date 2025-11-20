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

        <!-- Modal de Detalles -->
        <div id="detalleModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-3xl max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Detalles de Historial</h2>
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

    const prevDisabled = currentPage <= 1;
    const nextDisabled = currentPage >= lastPage;

    const prevBtn = `<button ${prevDisabled ? 'disabled' : ''} onclick="irAPagina(${currentPage-1})" class="px-3 py-2 text-sm ${prevDisabled ? 'text-gray-400' : 'text-gray-700'}">&larr; Anterior</button>`;
    const nextBtn = `<button ${nextDisabled ? 'disabled' : ''} onclick="irAPagina(${currentPage+1})" class="px-3 py-2 text-sm ${nextDisabled ? 'text-gray-400' : 'text-gray-700'}">Siguiente &rarr;</button>`;

    let pagesHtml = '';
    const maxPages = 4;
    let start = Math.max(1, currentPage - 2);
    let end = Math.min(lastPage, start + maxPages - 1);

    for (let i = start; i <= end; i++) {
        pagesHtml += `<button onclick="irAPagina(${i})" class="mx-1 px-2 py-1 text-sm ${i === currentPage ? 'bg-gray-100 rounded' : 'text-gray-500'}">${i}</button>`;
    }

    container.innerHTML = `
        <div class="flex items-center justify-between w-full">
            <div>${prevBtn}</div>
            <div class="flex items-center">${pagesHtml}</div>
            <div>${nextBtn}</div>
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
        // Pasar el array de acciones si existe
        if (data && data.registro) {
            data.registro.acciones = data.acciones || [];
            mostrarDetalleModal(data.registro);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cargar los detalles');
    }
}

function mostrarDetalleModal(historial) {
    const fecha = new Date(historial.created_at).toLocaleString('es-ES', {
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
                <p class="text-sm text-gray-900">${historial.usuario_nombre || 'Sistema'}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora</label>
                <p class="text-sm text-gray-900">${fecha}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
                <p class="text-sm text-gray-900">${historial.accion || '—'}</p>
            </div>
        </div>
    `;

    // Sección de detalles de acciones si existe un array historial.acciones o historial.detalles_acciones
    if (Array.isArray(historial.acciones) && historial.acciones.length > 0) {
        html += `
        <div class=\"mt-4\">
            <label class=\"block text-sm font-medium text-gray-700 mb-1\">Detalles de acciones realizadas</label>
            <ul class=\"list-disc pl-6 text-sm text-gray-800\">
                ${historial.acciones.map(a => `<li>${a}</li>`).join('')}
            </ul>
        </div>
        `;
    } else if (Array.isArray(historial.detalles_acciones) && historial.detalles_acciones.length > 0) {
        html += `
        <div class=\"mt-4\">
            <label class=\"block text-sm font-medium text-gray-700 mb-1\">Detalles de acciones realizadas</label>
            <ul class=\"list-disc pl-6 text-sm text-gray-800\">
                ${historial.detalles_acciones.map(a => `<li>${a}</li>`).join('')}
            </ul>
        </div>
        `;
    }

    if (historial.datos_anteriores) {
        html += `
        <div class=\"mt-4\">
            <label class=\"block text-sm font-medium text-gray-700 mb-1\">Datos anteriores</label>
            <pre class=\"text-xs text-gray-700 bg-gray-50 p-3 rounded border border-gray-200 overflow-x-auto\">${JSON.stringify(historial.datos_anteriores, null, 2)}</pre>
        </div>
        `;
    }
    if (historial.datos_nuevos) {
        html += `
        <div class=\"mt-4\">
            <label class=\"block text-sm font-medium text-gray-700 mb-1\">Datos nuevos</label>
            <pre class=\"text-xs text-gray-700 bg-gray-50 p-3 rounded border border-gray-200 overflow-x-auto\">${JSON.stringify(historial.datos_nuevos, null, 2)}</pre>
        </div>
        `;
    }

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
    const headers = ['FECHA', 'HORA', 'USUARIO', 'ACCION'];
    let data = [headers];

    rows.forEach(row => {
        if (row.querySelectorAll('td').length < 5) return;
        const cells = row.querySelectorAll('td');
        data.push([
            cells[0].innerText.trim(),
            cells[1].innerText.trim(),
            cells[2].innerText.trim(),
            cells[3].innerText.trim()
        ]);
    });

    // Crear hoja y libro con SheetJS
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
        // Aplicar estilo a encabezados
        ['A1','B1','C1','D1'].forEach(cell => {
            if (!ws[cell]) return;
            ws[cell].s = headerStyle;
        });
        // Ajustar ancho de columnas
        ws['!cols'] = [
            { wch: 12 }, // Fecha
            { wch: 18 }, // Hora
            { wch: 22 }, // Usuario
            { wch: 50 }  // Acción
        ];
        // Crear libro y exportar
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Historial');
        // Formato de fecha: dd-mm-yyyy
        const now = new Date();
        const dd = String(now.getDate()).padStart(2, '0');
        const mm = String(now.getMonth() + 1).padStart(2, '0');
        const yyyy = now.getFullYear();
        const fecha = `${dd}-${mm}-${yyyy}`;
        XLSX.writeFile(wb, `Historial_Expediente_${expedienteId}_${fecha}.xlsx`);
    }
    // Esperar a que cargue SheetJS si es necesario
    if (window.XLSX) {
        descargarXLSX();
    } else {
        script.onload = descargarXLSX;
    }
}
</script>
@endsection
