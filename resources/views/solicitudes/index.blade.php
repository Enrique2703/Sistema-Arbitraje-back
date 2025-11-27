@extends('layouts.app')
@include('solicitudes.create')
@include('solicitudes.edit')
@include('solicitudes.detalle')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<div class="min-h-screen bg-[#fafbfb] p-8">
    <h1 class="text-2xl font-bold text-black mb-6">Solicitudes</h1>
    <div class="mb-6">
        <input type="text" id="searchInput" class="w-96 px-4 py-2 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4" placeholder="Busca por cualquier campo...">
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-xl">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Demandante</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Demandado</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">N° Documentos</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fecha Inicio</th>
                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700"> </th>
                </tr>
            </thead>
            <tbody id="solicitudesTableBody" class="bg-white">
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Cargando solicitudes...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="flex justify-between items-center mt-4">
        <div></div>
        <div class="flex items-center space-x-2 text-sm">
            <span id="paginaActual">Página 1 de 1</span>
            <button id="btnAnterior" class="px-3 py-1 rounded bg-gray-100 text-gray-500" disabled>Anterior</button>
            <button id="btnSiguiente" class="px-3 py-1 rounded bg-gray-100 text-gray-500" disabled>Siguiente</button>
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
            tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay solicitudes registradas</td></tr>`;
            return;
        }
        tbody.innerHTML = '';
        list.forEach(solicitud => {
            tbody.innerHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">${solicitud.estado || 'Pendiente'}</td>
                    <td class="px-6 py-4">${solicitud.demandante || ''}</td>
                    <td class="px-6 py-4">${solicitud.demandado || ''}</td>
                    <td class="px-6 py-4 text-center">${solicitud.numero_documentos || 1}</td>
                    <td class="px-6 py-4">${formatDate(solicitud.fecha_inicio)}</td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="verSolicitud(${solicitud.id})" class="text-gray-700 hover:text-black"><i class="bi bi-eye" style="font-size: 1.3rem;"></i></button>
                    </td>
                </tr>`;
        });
    }

    function renderPagination() {
        document.getElementById('paginaActual').textContent = `Página ${currentPage} de ${lastPage}`;
        document.getElementById('btnAnterior').disabled = currentPage === 1;
        document.getElementById('btnSiguiente').disabled = currentPage === lastPage;
    }

    document.getElementById('btnAnterior').onclick = function() {
        if (currentPage > 1) loadSolicitudes(currentPage - 1);
    };
    document.getElementById('btnSiguiente').onclick = function() {
        if (currentPage < lastPage) loadSolicitudes(currentPage + 1);
    };

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


    // Modal Detalle Solicitud
    async function verSolicitud(id) {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        try {
            const res = await fetch(`/api/solicitudes/${id}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) throw new Error('No se pudo cargar la solicitud');
            const solicitud = await res.json();

            // Llenar datos en el modal
            document.getElementById('detalleEstado').textContent = solicitud.estado || '';
            if (solicitud.created_at) {
                const fecha = new Date(solicitud.created_at);
                const dia = String(fecha.getDate()).padStart(2, '0');
                const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                const anio = fecha.getFullYear();
                document.getElementById('detalleFechaInicio').textContent = `${dia}-${mes}-${anio}`;
            } else {
                document.getElementById('detalleFechaInicio').textContent = '';
            }


            // Demandante
            let demandanteNombre = solicitud.demandante;
            let demandanteCorreo = solicitud.demandante_correo || '';
            // Demandado
            let demandadoNombre = solicitud.demandado;
            let demandadoCorreo = solicitud.demandado_correo || '';

            document.getElementById('detalleDemandante').innerHTML = `${demandanteNombre}${demandanteCorreo ? ' <span class=\"text-xs text-gray-500\">(' + demandanteCorreo + ')</span>' : ''}`;
            document.getElementById('detalleDemandado').innerHTML = `${demandadoNombre}${demandadoCorreo ? ' <span class=\"text-xs text-gray-500\">(' + demandadoCorreo + ')</span>' : ''}`;

            // Documentos
            const docList = document.getElementById('detalleDocumentos');
            docList.innerHTML = '';
            if (solicitud.archivos && solicitud.archivos.length > 0) {
                solicitud.archivos.forEach(doc => {
                    docList.innerHTML += `<li class="flex items-center justify-between border-b py-1">${doc.archivo_adjunto.split('/').pop()} <a href="/storage/${doc.archivo_adjunto}" target="_blank" class="ml-2 text-gray-500 hover:text-black"><i class="bi bi-paperclip"></i></a></li>`;
                });
            } else {
                docList.innerHTML = '<li class="text-gray-400">Sin documentos</li>';
            }

            document.getElementById('detalleSolicitudModal').classList.remove('hidden');
        } catch (e) {
            alert('Error al cargar detalles de la solicitud');
        }
    }

    document.getElementById('btnCerrarDetalleSolicitud').onclick = function() {
        document.getElementById('detalleSolicitudModal').classList.add('hidden');
    };
</script>
@endsection