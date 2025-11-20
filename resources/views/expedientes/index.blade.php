@extends('layouts.app')
@include('expedientes.create')
@include('expedientes.edit')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<!-- Tom Select CSS y JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<div class="min-h-screen bg-gray-100 flex">
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Expedientes</h1>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider"></th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nombre de Expediente</th>
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
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">Cargando expedientes...</td>
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
        switch (estado.toLowerCase()) {
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
        tbody.innerHTML = `<tr><td colspan="8" class="text-center py-6 text-gray-500">Cargando...</td></tr>`;

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
            tbody.innerHTML = `<tr><td colspan="8" class="text-center text-red-500 py-6">Error al cargar expedientes</td></tr>`;
        }
    }

    function renderExpedientes(list) {
        const tbody = document.getElementById('expedientesTableBody');
        if (!list.length) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-6 text-gray-500">No hay expedientes registrados</td></tr>`;
            return;
        }

        tbody.innerHTML = '';
        list.forEach(exp => {
            const rowId = `exp-row-${exp.id}`;
            tbody.innerHTML += `
                <tr class="hover:bg-gray-50" id="${rowId}">
                    <td class="px-6 py-4">
                        <button id="btn-cedula-${exp.id}" class="bg-gray-400 text-white px-2 py-1 rounded text-sm cursor-not-allowed opacity-60" disabled>Cédula</button>
                    </td>
                    <td class="px-6 py-4">${String(exp.numero).padStart(4, '0')} - ${exp.anio}/${exp.codigo || ''}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${getEstadoClass(exp.estado)}">
                            ${exp.estado}
                        </span>
                    </td>
                    <td class="px-6 py-4">${exp.cantidad_participes || 0}</td>
                    <td class="px-6 py-4">${exp.documentos || '0'}</td>
                    <td class="px-6 py-4">${formatDate(exp.fecha_creacion) || '—'}</td>
                    <td class="px-6 py-4">${formatDate(exp.fecha_actualizacion) || '—'}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-3">
                            <button onclick="verDocumentos(${exp.id})" class="bg-black text-white px-2 py-1 rounded text-sm">Documentos</button>
                            <button onclick="verHistorial(${exp.id})" class="bg-black text-white px-2 py-1 rounded text-sm">Historial</button>
                            <button onclick="openEditModal(${exp.id})" class="text-gray-900 font-medium hover:underline">Editar</button>
                            <button onclick="deleteExpediente(${exp.id})" class="text-red-500 hover:underline">Eliminar</button>
                        </div>
                    </td>
                </tr>`;

            // Verificar si hay algún documento aprobado (revisado) para este expediente
            verificarDocumentoAprobado(exp.id);
        });

        // Función auxiliar para verificar documentos aprobados y si ya existe cédula
        async function verificarDocumentoAprobado(expedienteId) {
            try {
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                
                // Verificar si existe cédula para este expediente
                const cedulaResponse = await fetch(`/api/cedulas?expedientes_id=${expedienteId}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                
                if (cedulaResponse.ok) {
                    const cedulaData = await cedulaResponse.json();
                    const tieneCedula = cedulaData.registros && cedulaData.registros.length > 0;
                    
                    // Si ya tiene cédula, mostrar botón "Cerrar"
                    if (tieneCedula) {
                        const btn = document.getElementById(`btn-cedula-${expedienteId}`);
                        if (btn) {
                            btn.textContent = 'Cerrar';
                            btn.disabled = false;
                            btn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-60');
                            btn.classList.add('bg-black', 'hover:bg-gray-800');
                            btn.onclick = function() { 
                                // Redirigir a la vista de expedientes o cerrar la vista actual
                                window.location.href = '/expedientes';
                            };
                        }
                        return; // Salir, ya no necesitamos verificar documentos
                    }
                }
                
                // Si no hay cédula, verificar documentos aprobados
                const response = await fetch(`/api/expedientes/${expedienteId}/documentos`, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) return;
                const data = await response.json();
                const docs = data.documentos || [];
                const tieneAprobado = docs.some(doc => doc.revisado === true || doc.revisado === 1);
                if (tieneAprobado) {
                    const btn = document.getElementById(`btn-cedula-${expedienteId}`);
                    if (btn) {
                        btn.disabled = false;
                        btn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-60');
                        btn.classList.add('bg-black', 'hover:bg-gray-800');
                        btn.onclick = function() { openGenerarCedulaModal(expedienteId); };
                    }
                }
            } catch (e) {
                // Silenciar error
            }
        }
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

    async function exportToExcel() {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            console.log('Iniciando exportación...');

            const response = await fetch('/api/expedientes/export', {
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
            const excelData = data.map(exp => ({
                'ID': exp.id,
                'NOMBRE DE EXPEDIENTE': `${String(exp.numero).padStart(4, '0')} - ${exp.anio}/${exp.codigo || ''}`,
                'ESTADO': exp.estado,
                'PARTÍCIPES': exp.cantidad_participes || 0,
                'DOCUMENTOS': exp.documentos || 0,
                'INICIO': formatDate(exp.fecha_creacion),
                'ACTUALIZACIÓN': formatDate(exp.fecha_actualizacion)
            }));

            // Crear libro de Excel
            const ws = XLSX.utils.json_to_sheet(excelData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Expedientes");

            // Ajustar el ancho de las columnas
            const colWidths = [{
                    wch: 8
                }, // ID
                {
                    wch: 35
                }, // NOMBRE DE EXPEDIENTE
                {
                    wch: 15
                }, // ESTADO
                {
                    wch: 12
                }, // PARTÍCIPES
                {
                    wch: 12
                }, // DOCUMENTOS
                {
                    wch: 12
                }, // INICIO
                {
                    wch: 15
                } // ACTUALIZACIÓN
            ];
            ws['!cols'] = colWidths;

            // Descargar archivo
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            XLSX.writeFile(wb, `Expedientes_${day}-${month}-${year}.xlsx`);
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
            loadExpedientes(1, perPage, e.target.value.trim(), estadoSeleccionado);
        }, 300);
    });

    function verDocumentos(id) {
        // Redirigir a la página de documentos del expediente
        window.location.href = `/expedientes/documentos?id=${id}`;
    }

    function verHistorial(id) {
        // Por implementar - Redirigir a la página de historial del expediente
        window.location.href = `/expedientes/historial?id=${id}`;
    }

    // Variables globales para la modal de cédula
    let expedienteIdForCedula = null;

    // Abrir modal de generar cédula y asignar expediente
    function openGenerarCedulaModal(expedienteId) {
        expedienteIdForCedula = expedienteId;
        document.getElementById('generarCedulaModal').classList.remove('hidden');
        cargarUsuarios();
    }

    // Cerrar modal de generar cédula
    function closeGenerarCedulaModal() {
        document.getElementById('generarCedulaModal').classList.add('hidden');
        document.getElementById('formGenerarCedula').reset();
        // Limpiar la lista de usuarios dejando solo el primero
        const listaUsuarios = document.getElementById('listaUsuarios');
        listaUsuarios.innerHTML = `
            <div class="usuario-item flex items-center space-x-2">
                <button type="button" onclick="eliminarUsuario(this)" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                <select name="usuarios[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled selected>Seleccione un usuario</option>
                </select>
            </div>
        `;
    }

    // Agregar usuario a la lista (clona plantilla y convierte el select en Tom Select)
    function agregarUsuario() {
        const listaUsuarios = document.getElementById('listaUsuarios');
        
        // Crear un nuevo elemento en lugar de clonar
        const nuevoItem = document.createElement('div');
        nuevoItem.className = 'usuario-item flex items-center space-x-2';
        nuevoItem.innerHTML = `
            <button type="button" onclick="eliminarUsuario(this)" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
            <select name="usuarios[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="" disabled selected>Seleccione un usuario</option>
            </select>
        `;
        
        listaUsuarios.appendChild(nuevoItem);

        // Inicializar Tom Select en el nuevo select
        const nuevoSelect = nuevoItem.querySelector('select[name="usuarios[]"]');
        if (nuevoSelect) {
            inicializarTomSelect([nuevoSelect], window.listaUsuarios || []);
        }
    }

    // Eliminar usuario de la lista
    function eliminarUsuario(btn) {
        const item = btn.closest('.usuario-item');
        if (item) {
            item.remove();
        }
    }

    // Cargar usuarios con Tom Select
    async function cargarUsuarios() {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const res = await fetch('/api/usuarios', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error('Error al obtener usuarios');

            const data = await res.json();
            const usuarios = data.registros || [];

            // Inicializar Tom Select en los selects existentes
            inicializarTomSelect(document.querySelectorAll('select[name="usuarios[]"]'), usuarios);

            // Guardar los usuarios globalmente por si se agregan nuevos selects
            window.listaUsuarios = usuarios;
            return usuarios;
        } catch (error) {
            console.error('Error cargando usuarios:', error);
            return [];
        }
    }

    function inicializarTomSelect(selects, usuarios) {
        selects.forEach(select => {
            try {
                // Destruir instancia anterior si existe
                if (select.tomselect) {
                    select.tomselect.destroy();
                }

                new TomSelect(select, {
                    valueField: 'id',
                    labelField: 'email', // Mostrar el correo en el select
                    searchField: ['nombres', 'email'],
                    options: usuarios.map(u => ({
                        id: u.id,
                        nombres: u.nombres,
                        email: u.credencial?.email || ''
                    })),
                    create: false,
                    placeholder: 'Buscar usuario...',
                    render: {
                        option: function(item, escape) {
                            return `<div class="py-2 px-3">
                                <div class="font-medium">${escape(item.nombres)}</div>
                                <div class="text-sm text-gray-600">${escape(item.email || '')}</div>
                            </div>`;
                        },
                        item: function(item, escape) {
                            return `<div>${escape(item.email || '')}</div>`;
                        }
                    }
                });
            } catch (error) {
                console.error('Error inicializando TomSelect:', error);
            }
        });
    }

    // Manejar envío del formulario de cédula
    document.addEventListener('DOMContentLoaded', function() {
        const formCedula = document.getElementById('formGenerarCedula');
        if (formCedula) {
            formCedula.addEventListener('submit', async function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('🚀 Iniciando envío de cédula desde expedientes...');

                try {
                    const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                    
                    // Recopilar datos del formulario
                    const comentarios = this.querySelector('textarea[name="comentarios"]')?.value || '';

                    console.log('💬 Comentarios:', comentarios);
                    console.log('📂 Expediente ID:', expedienteIdForCedula);

                    // Recolectar usuarios desde los selects name="usuarios[]"
                    const usuarios = [];
                    document.querySelectorAll('select[name="usuarios[]"]').forEach(s => {
                        const val = s.value;
                        if (val) {
                            const num = Number(val);
                            if (!Number.isNaN(num)) usuarios.push(num);
                        }
                    });

                    console.log('👥 Usuarios seleccionados:', usuarios);

                    if (usuarios.length === 0) {
                        alert('Debe seleccionar al menos un usuario');
                        return;
                    }

                    const payload = {
                        expediente_id: Number(expedienteIdForCedula),
                        comentarios: comentarios || null,
                        usuarios: usuarios
                    };

                    console.log('📦 Payload a enviar:', JSON.stringify(payload, null, 2));

                    const response = await fetch('/api/cedulas', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    console.log('📡 Response status:', response.status);

                    if (!response.ok) {
                        const err = await response.json().catch(() => null);
                        console.error('❌ Error response:', err);
                        throw new Error((err && err.message) || 'Error al generar la cédula');
                    }

                    const created = await response.json();
                    console.log('✅ Cédula creada:', created);

                    alert('Cédula generada exitosamente');
                    closeGenerarCedulaModal();
                    
                    // Actualizar el botón de cédula a "Cerrar" después de crear la cédula
                    const btn = document.getElementById(`btn-cedula-${expedienteIdForCedula}`);
                    if (btn) {
                        btn.textContent = 'Cerrar';
                        btn.disabled = false;
                        btn.classList.remove('bg-gray-400', 'cursor-not-allowed', 'opacity-60');
                        btn.classList.add('bg-black', 'hover:bg-gray-800');
                        btn.onclick = function() { 
                            // Redirigir a la vista de expedientes o cerrar la vista actual
                            window.location.href = '/expedientes';
                        };
                    }

                } catch (error) {
                    console.error('❌ Error completo:', error);
                    alert(error.message || 'Error al generar la cédula');
                }
            });
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('generarCedulaModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeGenerarCedulaModal();
            }
        });
    });
</script>

<!-- Modal para Generar Cédula -->
<div id="generarCedulaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Generar cédula</h2>
            <button onclick="closeGenerarCedulaModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="formGenerarCedula" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios</label>
                <textarea name="comentarios" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Ingrese sus comentarios"></textarea>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <label class="block text-sm font-medium text-gray-700">Enviar por correo</label>
                    <button type="button" onclick="agregarUsuario()" class="flex items-center text-sm text-black-600 hover:text-black-700">
                        <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                        Agregar usuario
                    </button>
                </div>

                <div id="listaUsuarios" class="space-y-2">
                    <div class="usuario-item flex items-center space-x-2">
                        <button type="button" onclick="eliminarUsuario(this)" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                        <select name="usuarios[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="" disabled selected>Seleccione un usuario</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeGenerarCedulaModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800">
                    Enviar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection