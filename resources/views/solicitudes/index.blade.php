@extends('layouts.app')

@section('content')
@include('solicitudes.create')
@include('solicitudes.edit')
@include('solicitudes.detalle')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<div class="min-h-screen bg-[#fafbfb] p-8">
    <h1 class="text-2xl font-bold text-black mb-6">Solicitudes</h1>
    <div class="mb-6 flex items-center gap-4">
        <input type="text" id="searchInput" class="w-96 px-4 py-2 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Busca por cualquier campo...">
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-gray-700">Fecha:</label>
            <input type="date" id="fechaFilter" class="px-4 py-2 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button onclick="limpiarFecha()" class="px-3 py-2 text-sm bg-gray-200 rounded-lg hover:bg-gray-300 text-gray-700">Limpiar</button>
        </div>
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
        <select id="estadoFilterModal"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-4">
            <option value="Todos">Todos</option>
            <option value="Pendiente">Pendiente</option>
            <option value="En revisión">En revisión</option>
            <option value="Aprobada">Aprobada</option>
            <option value="Rechazada">Rechazada</option>
        </select>

        <label class="block text-sm text-gray-700 mb-2">Fecha:</label>
        <input type="date" id="fechaFilterModal" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-6">

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
    let fechaSeleccionada = '';

    document.addEventListener('DOMContentLoaded', () => {
        loadSolicitudes(currentPage, perPage);

        // Filtro de fecha en tiempo real
        document.getElementById('fechaFilter').addEventListener('change', function() {
            fechaSeleccionada = this.value;
            loadSolicitudes(1, perPage, document.getElementById('searchInput').value.trim(), estadoSeleccionado, fechaSeleccionada);
        });

        // Búsqueda en tiempo real
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadSolicitudes(1, perPage, this.value.trim(), estadoSeleccionado, fechaSeleccionada);
            }, 300);
        });

        document.getElementById('filterButton')?.addEventListener('click', () => {
            document.getElementById('filterModal').classList.remove('hidden');
            document.getElementById('estadoFilterModal').value = estadoSeleccionado;
            document.getElementById('fechaFilterModal').value = fechaSeleccionada;
        });

        document.getElementById('closeFilterModal').addEventListener('click', () => {
            document.getElementById('filterModal').classList.add('hidden');
        });

        document.getElementById('applyFilter').addEventListener('click', () => {
            estadoSeleccionado = document.getElementById('estadoFilterModal').value;
            fechaSeleccionada = document.getElementById('fechaFilterModal').value;
            document.getElementById('filterModal').classList.add('hidden');
            document.getElementById('fechaFilter').value = fechaSeleccionada;
            loadSolicitudes(1, perPage, document.getElementById('searchInput').value.trim(), estadoSeleccionado, fechaSeleccionada);
        });
    });

    async function loadSolicitudes(page = 1, pageSize = perPage, search = '', estado = estadoSeleccionado, fecha = fechaSeleccionada) {
        const tbody = document.getElementById('solicitudesTableBody');
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-gray-500">Cargando...</td></tr>`;

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            let url = `/api/solicitudes?page=${page}&per_page=${pageSize}&search=${search}&estado=${estado}`;
            if (fecha) {
                url += `&fecha=${fecha}`;
            }
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
    // Exponer función globalmente para que pueda ser llamada desde otros modales
    window.loadSolicitudes = loadSolicitudes;

    function renderSolicitudes(list) {
        const tbody = document.getElementById('solicitudesTableBody');
        if (!list.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay solicitudes registradas</td></tr>`;
            return;
        }
        tbody.innerHTML = '';
        list.forEach(solicitud => {
            let estado = solicitud.estado || 'Pendiente';
            let estadoClass = '';
            if (estado === 'Pendiente') {
                estadoClass = 'bg-gray-200 text-gray-700';
            } else if (estado === 'Aceptado') {
                estadoClass = 'bg-green-100 text-green-800';
            } else if (estado === 'Rechazado') {
                estadoClass = 'bg-red-100 text-red-800';
            } else {
                estadoClass = 'bg-gray-100 text-gray-800';
            }
            tbody.innerHTML += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4"><span class="font-medium px-2 py-1 rounded ${estadoClass}">${estado}</span></td>
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

    function limpiarFecha() {
        fechaSeleccionada = '';
        document.getElementById('fechaFilter').value = '';
        loadSolicitudes(1, perPage, document.getElementById('searchInput').value.trim(), estadoSeleccionado, '');
    }

    document.getElementById('btnAnterior').onclick = function() {
        if (currentPage > 1) loadSolicitudes(currentPage - 1, perPage, document.getElementById('searchInput').value.trim(), estadoSeleccionado, fechaSeleccionada);
    };
    document.getElementById('btnSiguiente').onclick = function() {
        if (currentPage < lastPage) loadSolicitudes(currentPage + 1, perPage, document.getElementById('searchInput').value.trim(), estadoSeleccionado, fechaSeleccionada);
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
        window.solicitudDetalleId = id;
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
            // Estado y color
            const estado = solicitud.estado || '';
            document.getElementById('detalleEstado').textContent = estado;
            if (estado === 'Pendiente') {
                document.getElementById('detalleEstado').className = 'font-medium bg-gray-200 text-gray-700 px-2 py-1 rounded';
            } else if (estado === 'Aceptado') {
                document.getElementById('detalleEstado').className = 'font-medium bg-green-100 text-green-800 px-2 py-1 rounded';
            } else if (estado === 'Rechazado') {
                document.getElementById('detalleEstado').className = 'font-medium bg-red-100 text-red-800 px-2 py-1 rounded';
            } else {
                document.getElementById('detalleEstado').className = 'font-medium bg-gray-100 text-gray-800 px-2 py-1 rounded';
            }
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

    document.getElementById('btnRechazarSolicitud').onclick = async function() {
        const id = window.solicitudDetalleId;
        if (!id) return;
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        try {
            const res = await fetch(`/api/solicitudes/${id}/estado`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ estado: 'Rechazado' })
            });
            if (!res.ok) throw new Error('No se pudo actualizar el estado');
            // Actualizar en UI
            document.getElementById('detalleEstado').textContent = 'Rechazado';
            document.getElementById('detalleEstado').className = 'font-medium bg-red-100 text-red-800 px-2 py-1 rounded';
            setTimeout(() => {
                document.getElementById('detalleSolicitudModal').classList.add('hidden');
                loadSolicitudes(currentPage, perPage);
            }, 800);
        } catch (e) {
            alert('Error al rechazar la solicitud');
        }
    };

    document.getElementById('btnAceptarSolicitud').onclick = async function() {
        const id = window.solicitudDetalleId;
        if (!id) return;
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        
        try {
            // Cambiar el estado a Aceptado inmediatamente

            
            console.log('Estado cambiado a Aceptado. ID guardado:', id);
            
            // Guardar el ID de la solicitud para saber que debe volver a Pendiente si se cancela
            window.solicitudPendienteAceptar = id;
            window.idSolicitud = id; // Guardar ID con nombre más descriptivo
            
            // Actualizar visualmente el estado en el modal
            document.getElementById('detalleEstado').textContent = 'Aceptado';
            document.getElementById('detalleEstado').className = 'font-medium bg-green-100 text-green-800 px-2 py-1 rounded';
            
            // Recargar la tabla para mostrar el cambio
            loadSolicitudes(currentPage, perPage);
            
            // Cerrar modal de detalle de solicitud
            document.getElementById('detalleSolicitudModal').classList.add('hidden');
            
            // Obtener datos de la solicitud para prellenar partícipes
            const detalle = await fetch(`/api/solicitudes/${id}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (detalle.ok) {
                const solicitud = await detalle.json();
                // Abrir modal expediente
                if (window.openCreateModal) {
                    window.openCreateModal();
                    setTimeout(() => {
                        // Prellenar partícipes usando los IDs reales de la solicitud
                        const container = document.getElementById('participesContainer');
                        container.innerHTML = '';
                        function crearParticipePorId(id, condicion) {
                            // Si el id es string (nombre), buscar el ID real en listaParticipes
                            let realId = id;
                            if (typeof id === 'string' && window.listaParticipes) {
                                const normalizar = s => (s||'').toLowerCase().trim();
                                const encontrado = window.listaParticipes.find(p => {
                                    const nombreCompleto = `${p.nombres ?? ''} ${p.apellidos ?? ''}`.trim();
                                    return normalizar(nombreCompleto) === normalizar(id);
                                });
                                if (encontrado) realId = encontrado.id;
                            }
                            const div = document.createElement('div');
                            div.className = 'flex items-center gap-2 mb-2';
                            div.innerHTML = `
                                <button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                                <select name="participes_id[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">Seleccionar partícipe</option>
                                </select>
                                <select name="participes_condicion[]" class="w-40 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">Condición</option>
                                    <option value="Demandante"${condicion==='Demandante'?' selected':''}>Demandante</option>
                                    <option value="Demandado"${condicion==='Demandado'?' selected':''}>Demandado</option>
                                </select>
                            `;
                            // Llenar el select con los partícipes y seleccionar el correcto
                            if (window.listaParticipes) {
                                const select = div.querySelector('select[name="participes_id[]"]');
                                select.innerHTML = '<option value="">Seleccionar partícipe</option>';
                                window.listaParticipes.forEach(p => {
                                    const nombreCompleto = `${p.nombres ?? ''} ${p.apellidos ?? ''}`.trim();
                                    const option = document.createElement('option');
                                    option.value = p.id;
                                    option.textContent = nombreCompleto || 'Partícipe sin nombre';
                                    if (p.id == realId) option.selected = true;
                                    select.appendChild(option);
                                });
                                // Integrar TomSelect si está disponible
                                if (window.TomSelect) {
                                    if (select.tomselect) select.tomselect.destroy();
                                    new TomSelect(select, {
                                        valueField: 'id',
                                        labelField: 'nombres',
                                        searchField: ['nombres', 'apellidos', 'email'],
                                        options: window.listaParticipes || [],
                                        create: false,
                                        placeholder: 'Buscar partícipe...'
                                    });
                                }
                            }
                            container.appendChild(div);
                        }
                        // Usar participe_id como Demandante y demandado como Demandado
                        let demandanteId = null;
                        if (solicitud.participe && solicitud.participe.id) {
                            demandanteId = String(solicitud.participe.id);
                            crearParticipePorId(demandanteId, 'Demandante');
                        } else if (solicitud.participe_id) {
                            demandanteId = String(solicitud.participe_id);
                            crearParticipePorId(demandanteId, 'Demandante');
                        }
                        // Demandado: siempre agregar si existe y no es igual al demandante
                        let demandadoId = null;
                        if (solicitud.demandado !== undefined && solicitud.demandado !== null && String(solicitud.demandado).trim() !== '') {
                            demandadoId = String(solicitud.demandado);
                        }
                        // Evitar duplicar si demandante y demandado son el mismo
                        if (demandadoId !== null && (!demandanteId || demandadoId !== demandanteId)) {
                            crearParticipePorId(demandadoId, 'Demandado');
                        }
                    }, 400);
                }
            }
        } catch (e) {
            alert('Error al aceptar la solicitud');
            // Revertir visual si hay error
            document.getElementById('detalleEstado').textContent = 'Pendiente';
            document.getElementById('detalleEstado').className = 'font-medium bg-gray-200 text-gray-700 px-2 py-1 rounded';
        }
    };
</script>
@endsection