@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<!-- Tom Select CSS y JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<!-- XLSX Library para exportar a Excel -->
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<div class="min-h-screen bg-gray-100 flex">
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="/expedientes" class="flex items-center text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="text-sm text-gray-600" id="fileNameLabel">Adjuntar archivos</span>
                                <input type="file" class="hidden" name="documento" id="documentoInput" required accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png">
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
        let rolSeleccionado = 'Todos';
        let allDocumentos = []; // Variable global para almacenar todos los documentos

        document.addEventListener('DOMContentLoaded', function() {
                        // Siempre limpiar y recargar la tabla al cargar la página
                        const tbody = document.getElementById('documentosTableBody');
                        if (tbody) tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Cargando...</td></tr>';
                        cargarDocumentos();

                        // Recargar la tabla cada vez que la página se muestre (incluso al regresar con el historial)
                        window.addEventListener('pageshow', function(event) {
                            if (tbody) tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Cargando...</td></tr>';
                            cargarDocumentos();
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        });
            // Si venimos de cedulas generadas y hay expediente_id, recargar la tabla y hacer scroll al inicio
            if (window.location.search.includes('expediente_id=')) {
                setTimeout(() => {
                    cargarDocumentos();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }, 100);
            }

            // Obtener el ID del expediente de múltiples fuentes (parámetros URL, variable global, inputs ocultos o ruta)
            const urlParams = new URLSearchParams(window.location.search);
            expedienteId = urlParams.get('id') || urlParams.get('expediente_id') || urlParams.get('expedienteId');

            // fallback a variable global si existe
            if (!expedienteId && typeof window.expedienteId !== 'undefined') {
                expedienteId = window.expedienteId;
            }

            // fallback a input hidden en la página
            if (!expedienteId) {
                const hiddenInput = document.getElementById('expediente_id') || document.querySelector('input[name="expediente_id"]');
                if (hiddenInput) expedienteId = hiddenInput.value || expedienteId;
            }

            // fallback a extracción desde la ruta (/expedientes/{id}/...)
            if (!expedienteId) {
                const pathMatch = window.location.pathname.match(/\/expedientes\/(\d+)/);
                if (pathMatch) expedienteId = pathMatch[1];
            }

            // Si no se encuentra expedienteId, redirigir a expedientes
            if (!expedienteId) {
                console.warn('No se encontró expedienteId, redirigiendo a /expedientes');
                window.location.href = '/expedientes';
                return;
            }
            console.log('ExpedienteId detectado:', expedienteId);


            // Configurar eventos del filtro
            document.getElementById('filterButton').addEventListener('click', () => {
                document.getElementById('filterModal').classList.remove('hidden');
            });

            document.getElementById('closeFilterModal').addEventListener('click', () => {
                document.getElementById('filterModal').classList.add('hidden');
            });

            document.getElementById('applyFilter').addEventListener('click', () => {
                rolSeleccionado = document.getElementById('rolFilter').value;
                document.getElementById('filterModal').classList.add('hidden');
                cargarDocumentos();
            });

            // Cerrar modal al hacer clic fuera
            document.getElementById('filterModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    document.getElementById('filterModal').classList.add('hidden');
                }
            });

            cargarDocumentos();
            configurarBuscador();
            // Cargar usuarios para los selects con Tom Select (buscador)
            cargarUsuarios();

            // Manejar el envío del formulario de cédula (enviar JSON a /api/cedulas)
            const formCedula = document.getElementById('formGenerarCedula');
            if (formCedula) {
                formCedula.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('🚀 Iniciando envío de cédula...');

                    try {
                        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                        console.log('Token encontrado:', token ? 'Sí' : 'No');

                        // Recopilar datos del formulario
                        const documentoId = document.getElementById('formGenerarCedula_documento_id')?.value || '';
                        const comentarios = this.querySelector('textarea[name="comentarios"]')?.value || '';

                        console.log('Documento ID:', documentoId);
                        console.log('Comentarios:', comentarios);

                        // Recolectar usuarios desde los selects name="usuarios[]"
                        const usuarios = [];
                        document.querySelectorAll('select[name="usuarios[]"]').forEach(s => {
                            const val = s.value;
                            console.log('Select value:', val);
                            if (val) {
                                const num = Number(val);
                                if (!Number.isNaN(num)) usuarios.push(num);
                            }
                        });

                        console.log('Usuarios seleccionados:', usuarios);

                        const payload = {
                            documento_id: Number(documentoId) || null,
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
                        console.log('📡 Response ok:', response.ok);

                        if (!response.ok) {
                            const err = await response.json().catch(() => null);
                            console.error('❌ Error response:', err);
                            throw new Error((err && err.message) || 'Error al generar la cédula');
                        }

                        // Leer la respuesta (backend devuelve { status, id, cedula })
                        const created = await response.json();
                        console.log('✅ Cédula creada:', created);

                        alert('Cédula generada exitosamente');
                        closeGenerarCedulaModal();

                        // Redirigir al listado de cédulas del expediente
                        const documentoIdForRedirect = documentoId || (created && created.id) || '';
                        const expedienteIdParam = expedienteId || new URLSearchParams(window.location.search).get('id');
                        
                        console.log('🔄 Redirigiendo con expedienteId:', expedienteIdParam, 'documentoId:', documentoIdForRedirect);
                        
                        if (expedienteIdParam) {
                            window.location.href = `/expedientes/cedulas?expediente_id=${expedienteIdParam}&documento_id=${documentoIdForRedirect}`;
                        } else {
                            await cargarDocumentos();
                        }
                    } catch (error) {
                        console.error('❌ Error completo:', error);
                        alert(error.message || 'Error al generar la cédula');
                    }
                });
            }
        });

        function configurarBuscador() {
            const searchInput = document.getElementById('searchInput');
            let searchTimeout;

            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.trim();
                clearTimeout(searchTimeout);

                // Usar un temporizador para evitar demasiadas peticiones mientras se escribe
                searchTimeout = setTimeout(() => {
                    currentPage = 1; // Resetear a la primera página cuando se busca
                    cargarDocumentos().then(() => {
                        console.log('Búsqueda completada para:', searchTerm);
                    }).catch(error => {
                        console.error('Error en la búsqueda:', error);
                    });
                }, 300);
            });
        }

        async function cargarDocumentos() {
    const tbody = document.getElementById('documentosTableBody');
    const searchTerm = document.getElementById('searchInput').value.trim();

    // SIEMPRE obtener expedienteId de la URL en cada llamada
    let urlParams = new URLSearchParams(window.location.search);
    let expId = urlParams.get('id') || urlParams.get('expediente_id') || urlParams.get('expedienteId');
    if (!expId) {
        const hiddenInput = document.getElementById('expediente_id') || document.querySelector('input[name="expediente_id"]');
        if (hiddenInput) expId = hiddenInput.value;
    }
    if (!expId) {
        const pathMatch = window.location.pathname.match(/\/expedientes\/(\d+)/);
        if (pathMatch) expId = pathMatch[1];
    }
    if (!expId) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-red-500">No se encontró el expediente</td></tr>';
        return;
    }

    try {
        tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-gray-500">Cargando...</td></tr>';

        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const params = new URLSearchParams({
            search: searchTerm,
            page: currentPage,
            rol: rolSeleccionado,
            searchFields: 'titulo,usuario_nombre'
        });

        const response = await fetch(`/api/expedientes/${expId}/documentos?${params.toString()}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Error al cargar documentos');

        const data = await response.json();
        allDocumentos = data.documentos; // Guardar en variable global
        renderizarDocumentos(data.documentos);
        actualizarPaginacion(data.meta);

    } catch (error) {
        console.error('Error:', error);
        tbody.innerHTML = '<tr><td colspan="8" class="px-6 py-4 text-center text-red-500">Error al cargar los documentos</td></tr>';
    }
        }

        function renderizarDocumentos(documentos) {
            const tbody = document.getElementById('documentosTableBody');
            const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();

            // Filtrar documentos según el rol seleccionado y el término de búsqueda
            const documentosFiltrados = documentos.filter(doc => {
                // Primero verificar el rol
                if (rolSeleccionado !== 'Todos' && doc.estado !== rolSeleccionado) {
                    return false;
                }

                // Si hay término de búsqueda, verificar título y usuario
                if (searchTerm) {
                    const tituloCoincide = doc.titulo.toLowerCase().includes(searchTerm);
                    const usuarioCoincide = (doc.usuario_nombre || '').toLowerCase().includes(searchTerm);
                    return tituloCoincide || usuarioCoincide;
                }

                return true;
            });


            tbody.innerHTML = '';
            documentosFiltrados.forEach(doc => {
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

                const isHabilitado = doc.habilitado || false;

                tbody.innerHTML += `
            <tr class="${!isHabilitado ? 'bg-opacity-40' : ''} hover:bg-gray-50">
                <td class="px-6 py-4 text-center">
                    <button onclick="openGenerarCedulaModal(${doc.id})" class="bg-black text-white px-3 py-1 rounded text-sm">
                        ${doc.cedula || 'Cédula'}
                    </button>
                </td>
                <td class="px-6 py-4 text-center">
                    <input type="checkbox" 
                            ${isHabilitado ? 'checked' : ''} 
                            onchange="toggleHabilitado(${doc.id}, this.checked)"
                            class="form-checkbox h-5 w-5 text-gray-600 cursor-pointer">
                </td>
                <td class="px-6 py-4 text-center text-gray-900">${fecha}</td>
                <td class="px-6 py-4 text-center text-gray-900">${hora}</td>
                <td class="px-6 py-4 text-gray-900">${doc.titulo}</td>
                <td class="px-6 py-4 text-gray-900">${doc.usuario_nombre || 'Sistema'}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-800">
                        ${doc.estado || 'Demandado'}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex justify-end space-x-2">
                    <button onclick="revisarDocumento(${doc.id})" class="bg-black text-white px-3 py-1 rounded text-sm">
                        Revisar
                    </button>
                    <button onclick="verCedulasGeneradas(${doc.id})" class="bg-black text-white px-3 py-1 rounded text-sm">
                        Generados
                    </button>                    
                    </div>
                </td>
            </tr>
        `;
            });
        }

        async function exportToFolder() {
            try {
                console.log('Iniciando exportación a Excel...');
                console.log('Total de documentos:', allDocumentos.length);

                // Verificar que XLSX esté disponible
                if (typeof XLSX === 'undefined') {
                    console.error('XLSX library no está cargada');
                    alert('Error: Biblioteca de Excel no disponible. Por favor, recarga la página.');
                    return;
                }

                // Verificar que hay documentos para exportar
                if (!allDocumentos || allDocumentos.length === 0) {
                    alert('No hay documentos para exportar');
                    return;
                }

                // Preparar datos para Excel con el formato especificado
                const excelData = allDocumentos.map(doc => {
                    const fecha = new Date(doc.created_at);
                    const fechaStr = fecha.toLocaleDateString('es-PE', { 
                        day: '2-digit', 
                        month: '2-digit', 
                        year: 'numeric' 
                    });
                    const horaStr = fecha.toLocaleTimeString('es-PE', { 
                        hour: '2-digit', 
                        minute: '2-digit', 
                        second: '2-digit',
                        hour12: false 
                    });

                    return {
                        FECHA: fechaStr,
                        HORA: horaStr,
                        TITULO: doc.titulo || '',
                        USUARIO: doc.usuario_nombre || 'Sistema',
                        ROL: 'Demandado' // Ajustar según la lógica de negocio
                    };
                });

                console.log('Datos preparados para Excel:', excelData);

                // Crear libro y hoja de trabajo
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.json_to_sheet(excelData);

                // Ajustar ancho de columnas
                ws['!cols'] = [
                    { wch: 12 }, // FECHA
                    { wch: 10 }, // HORA
                    { wch: 30 }, // TITULO
                    { wch: 20 }, // USUARIO
                    { wch: 15 }  // ROL
                ];

                XLSX.utils.book_append_sheet(wb, ws, 'Documentos');

                // Descargar archivo
                const expedienteId = new URLSearchParams(window.location.search).get('expediente_id') || 
                                    document.querySelector('input[name="expediente_id"]')?.value ||
                                    'expediente';
                const filename = `Documentos_${expedienteId}_${new Date().toISOString().split('T')[0]}.xlsx`;
                
                console.log('Descargando archivo:', filename);
                XLSX.writeFile(wb, filename);
                
                console.log('Exportación completada exitosamente');
            } catch (error) {
                console.error('Error al exportar:', error);
                alert('Error al exportar los documentos a Excel: ' + error.message);
            }
        }

        function openCreateModal() {
            document.getElementById('documentModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('documentModal').classList.add('hidden');
            document.getElementById('documentForm').reset();
            // Resetear el label del archivo
            const fileLabel = document.getElementById('fileNameLabel');
            if (fileLabel) {
                fileLabel.textContent = 'Adjuntar archivos';
                fileLabel.classList.remove('text-blue-600', 'font-medium');
                fileLabel.classList.add('text-gray-600');
            }
        }

        async function revisarDocumento(id) {
            document.getElementById('documentoIdRevision').value = id;
            document.getElementById('revisionModal').classList.remove('hidden');
        }

        function closeRevisionModal() {
            document.getElementById('revisionModal').classList.add('hidden');
            document.getElementById('revisionForm').reset();
        }

        async function rechazarDocumento() {
            const documentoId = document.getElementById('documentoIdRevision').value;
            const formData = new FormData(document.getElementById('revisionForm'));
            formData.append('estado', 'rechazado');

            try {
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                const response = await fetch(`/api/documentos/${documentoId}/revisar`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Error al rechazar el documento');
                }

                alert('Documento rechazado correctamente');
                closeRevisionModal();
                await cargarDocumentos();
            } catch (error) {
                console.error('Error:', error);
                alert('Error al rechazar el documento');
            }
        }

        // Manejar el envío del formulario de revisión (Aprobar)
        document.getElementById('revisionForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const documentoId = document.getElementById('documentoIdRevision').value;
            const formData = new FormData(this);
            formData.append('estado', 'aprobado');

            try {
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                const response = await fetch(`/api/documentos/${documentoId}/revisar`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Error al aprobar el documento');
                }

                alert('Documento aprobado correctamente');
                closeRevisionModal();
                await cargarDocumentos();
            } catch (error) {
                console.error('Error:', error);
                alert('Error al aprobar el documento');
            }
        });

        // Cerrar modal al hacer clic fuera
        document.getElementById('revisionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRevisionModal();
            }
        });

        async function verDocumento(id) {
            // Implementar función para ver documento
            console.log('Ver documento:', id);
        }

        function verCedulasGeneradas(id) {
            // Obtener expedienteId de forma robusta (igual que en cargarDocumentos)
            let urlParams = new URLSearchParams(window.location.search);
            let expId = urlParams.get('id') || urlParams.get('expediente_id') || urlParams.get('expedienteId');
            if (!expId) {
                const hiddenInput = document.getElementById('expediente_id') || document.querySelector('input[name="expediente_id"]');
                if (hiddenInput) expId = hiddenInput.value;
            }
            if (!expId) {
                const pathMatch = window.location.pathname.match(/\/expedientes\/(\d+)/);
                if (pathMatch) expId = pathMatch[1];
            }
            
            console.log('Redirigiendo a cédulas con expedienteId:', expId, 'documentoId:', id);
            
            // Redirigir a la página de cédulas con el ID del documento y del expediente
            window.location.href = `/expedientes/cedulas?expediente_id=${expId}&documento_id=${id}`;
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

        async function toggleHabilitado(id, estado) {
            try {
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                const response = await fetch(`/api/documentos/${id}/habilitar`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        habilitado: estado
                    })
                });

                if (!response.ok) {
                    throw new Error('Error al actualizar el estado del documento');
                }

                // Recargar los documentos para reflejar los cambios
                await cargarDocumentos();
            } catch (error) {
                console.error('Error:', error);
                alert('Error al actualizar el estado del documento');
                // Revertir el estado del checkbox si hubo error
                const checkbox = event.target;
                checkbox.checked = !checkbox.checked;
            }
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

        // Mostrar nombre del archivo seleccionado con validación mejorada
        document.getElementById('documentoInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileLabel = document.getElementById('fileNameLabel');
            
            if (file) {
                // Validar tamaño del archivo (máximo 10MB)
                const maxSize = 10 * 1024 * 1024; // 10MB en bytes
                if (file.size > maxSize) {
                    alert('El archivo es demasiado grande. El tamaño máximo permitido es 10MB.');
                    this.value = ''; // Limpiar el input
                    fileLabel.textContent = 'Adjuntar archivos';
                    fileLabel.classList.remove('text-blue-600', 'font-medium');
                    fileLabel.classList.add('text-gray-600');
                    return;
                }
                
                // Mostrar nombre del archivo con estilo diferente
                fileLabel.textContent = file.name;
                fileLabel.classList.remove('text-gray-600');
                fileLabel.classList.add('text-blue-600', 'font-medium');
                console.log('Archivo seleccionado:', file.name, 'Tamaño:', (file.size / 1024).toFixed(2) + 'KB');
            } else {
                // Restaurar texto original si no hay archivo
                fileLabel.textContent = 'Adjuntar archivos';
                fileLabel.classList.remove('text-blue-600', 'font-medium');
                fileLabel.classList.add('text-gray-600');
            }
        });

        document.getElementById('documentForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    try {
        // Validar que se haya seleccionado un archivo
        const documentoInput = document.getElementById('documentoInput');
        if (!documentoInput.files || !documentoInput.files[0]) {
            alert('Por favor, seleccione un archivo para subir.');
            return;
        }

        const formData = new FormData();
        // Refuerzo: obtener expedienteId igual que en cargarDocumentos
        let urlParams = new URLSearchParams(window.location.search);
        let expId = urlParams.get('id') || urlParams.get('expediente_id') || urlParams.get('expedienteId');
        if (!expId) {
            const hiddenInput = document.getElementById('expediente_id') || document.querySelector('input[name="expediente_id"]');
            if (hiddenInput) expId = hiddenInput.value;
        }
        if (!expId) {
            const pathMatch = window.location.pathname.match(/\/expedientes\/(\d+)/);
            if (pathMatch) expId = pathMatch[1];
        }
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');

        // Agregar campos al FormData
        formData.append('titulo', this.titulo.value);
        formData.append('comentarios', this.comentarios.value);
        formData.append('documento', documentoInput.files[0]);
        formData.append('expediente_id', expId);
        
        console.log('📤 Enviando documento:', documentoInput.files[0].name);

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

        // Funciones para cargar usuarios y configurar Tom Select
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

                // Inicializar Tom Select en los selects existentes (si los hay)
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
                        },
                        loadingClass: 'loading',
                        load: async function(query, callback) {
                            // Si el query está vacío, devolver la lista ya cargada (mapeada)
                            if (!query) {
                                return callback(usuarios.map(u => ({
                                    id: u.id,
                                    nombres: u.nombres,
                                    email: u.credencial?.email || ''
                                })));
                            }
                            try {
                                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                                const response = await fetch(`/api/usuarios?search=${encodeURIComponent(query)}`, {
                                    headers: {
                                        'Authorization': `Bearer ${token}`,
                                        'Accept': 'application/json'
                                    }
                                });
                                if (!response.ok) return callback();
                                const json = await response.json();
                                const registros = json.registros || [];
                                // Mapear los registros a la forma esperada por Tom Select
                                const mapped = registros.map(u => ({
                                    id: u.id,
                                    nombres: u.nombres,
                                    email: u.credencial?.email || ''
                                }));
                                callback(mapped);
                            } catch (e) {
                                console.error('Error cargando usuarios:', e);
                                callback();
                            }
                        }
                    });
                } catch (e) {
                    console.error('Error inicializando TomSelect:', e);
                }
            });
        }

        // Modal para generar cédula
        const generarCedulaModal = document.getElementById('generarCedulaModal');

        // Abrir modal de generar cédula y asignar documento
        function openGenerarCedulaModal(documentoId) {
            // Setear el id del documento en el input hidden
            const hidden = document.getElementById('formGenerarCedula_documento_id');
            if (hidden) hidden.value = documentoId || '';
            document.getElementById('generarCedulaModal').classList.remove('hidden');
        }

        function closeGenerarCedulaModal() {
            document.getElementById('generarCedulaModal').classList.add('hidden');
            document.getElementById('formGenerarCedula').reset();
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
            if (item && !item.classList.contains('hidden')) {
                item.remove();
            }
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('generarCedulaModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeGenerarCedulaModal();
            }
        });
    </script>

    <!-- Modal de Revisión de Documento -->
    <div id="revisionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-lg">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">¿Quieres aprobar el documento?</h2>
                <button onclick="closeRevisionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="revisionForm" class="space-y-6">
                <input type="hidden" id="documentoIdRevision" name="documentoId">

                <div class="border-t border-gray-200 pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Resolución</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                        <div class="flex items-center justify-center">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="text-sm text-gray-600">Adjuntar archivos</span>
                                <input type="file" multiple class="hidden" name="archivos">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between space-x-3 pt-4">
                    <button type="button" onclick="rechazarDocumento()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 flex-1">
                        Rechazar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-800 flex-1">
                        Aprobar
                    </button>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="button" onclick="closeRevisionModal()"
                        class="text-sm text-gray-600 hover:text-gray-800">
                        Cerrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Filtro -->
    <div id="filterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-96 p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">Filtrar documentos</h2>

            <label class="block text-sm text-gray-700 mb-2">Rol:</label>
            <select id="rolFilter"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-6">
                <option value="Todos">Todos</option>
                <option value="Demandado">Demandado</option>
                <option value="Demandante">Demandante</option>
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
                <input type="hidden" name="documento_id" id="formGenerarCedula_documento_id" value="">
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
                            <button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>

                            <select name="usuarios[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="" disabled selected>Seleccione un usuario</option>
                                    <!-- Opciones se llenarán dinámicamente -->
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