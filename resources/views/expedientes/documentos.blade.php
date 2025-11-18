@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<!-- Tom Select CSS y JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<!-- XLSX Library para exportar a Excel -->
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>

<style>
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-nuevo-documento {
        background: white;
        padding: 30px;
        border-radius: 8px;
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-nuevo-documento h2 {
        font-size: 24px;
        margin: 0 0 25px 0;
        color: #333;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }

    .file-upload-area {
        border: 1px solid #d0d0d0;
        padding: 30px 20px;
        border-radius: 4px;
        background: #f5f5f5;
        min-height: 120px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .file-upload-button {
        position: relative;
        display: inline-block;
    }

    .file-upload-button span {
        display: inline-block;
        padding: 10px 28px;
        background: #000;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 400;
    }

    .file-upload-button span:hover {
        background: #2c2c2c;
    }

    .file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-size-note {
        font-size: 11px;
        color: #666;
        margin-top: 8px;
        line-height: 1.4;
    }

    .selected-files {
        margin-top: 10px;
        text-align: left;
    }

    .selected-file {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: white;
        border: 1px solid #e5e5e5;
        border-radius: 3px;
        margin-bottom: 8px;
    }

    .selected-file-name {
        font-size: 13px;
        color: #0066cc;
        margin-right: 10px;
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .remove-file {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        padding: 0;
    }

    .remove-file:hover {
        background: #c82333;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 30px;
    }

    .btn-cancelar {
        padding: 8px 20px;
        border: none;
        background: #ccc;
        color: #333;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-presentar {
        padding: 8px 20px;
        border: none;
        background: black;
        color: white;
        border-radius: 4px;
        cursor: pointer;
    }
</style>

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

                            <th class="w-20 px-3 py-3 text-center text-xs font-medium text-white uppercase">Habilitado</th>
                            <th class="w-24 px-3 py-3 text-center text-xs font-medium text-white uppercase">Fecha</th>
                            <th class="w-20 px-3 py-3 text-center text-xs font-medium text-white uppercase">Hora</th>
                            <th class="w-48 px-3 py-3 text-left text-xs font-medium text-white uppercase">Título</th>
                            <th class="w-32 px-3 py-3 text-left text-xs font-medium text-white uppercase">Usuario</th>
                            <th class="w-24 px-3 py-3 text-left text-xs font-medium text-white uppercase">Rol</th>
                            <th class="w-24 px-3 py-3 text-center text-xs font-medium text-white uppercase">Tamaño</th>
                            <th class="w-40 px-3 py-3 text-center text-xs font-medium text-white uppercase"></th>
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
    <div class="modal-overlay" id="modalNuevoDocumento">
        <div class="modal-nuevo-documento">
            <h2>Nuevo documento</h2>
            <form id="formNuevoDocumento" class="form-nuevo-documento">
                <input type="hidden" name="expediente_id" id="expediente_id">

                <div class="form-group">
                    <label>Título</label>
                    <input type="text" name="titulo" id="titulo" placeholder="Ingrese el título del documento" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Comentarios</label>
                    <textarea name="comentarios" id="comentarios" rows="4" placeholder="Ingrese comentarios adicionales" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label>Cargar del escritorio</label>
                    <div class="file-upload-area">
                        <div class="file-upload-button">
                            <span>Adjuntar archivos (PDF, PNG, JPG)</span>
                            <input type="file" accept=".pdf,.png,.jpg,.jpeg" name="archivos[]" class="file-input" id="archivosDocumento" multiple>
                        </div>
                    </div>
                    <div id="selectedFilesDocumento" class="selected-files"></div>
                    <p class="file-size-note">Si el archivo no supera los 10mb adjuntar en el siguiente recuadro en formato pdf, en caso superar el límite configurar el link de descarga</p>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancelar" onclick="cerrarModalNuevoDocumento()">Cancelar</button>
                    <button type="submit" class="btn-presentar">Presentar</button>
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
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
            // Si venimos de cedulas generadas y hay expediente_id, recargar la tabla y hacer scroll al inicio
            if (window.location.search.includes('expediente_id=')) {
                setTimeout(() => {
                    cargarDocumentos();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
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

            // Configurar manejador de archivos para el modal
            const archivosInput = document.getElementById('archivosDocumento');
            const selectedFilesDiv = document.getElementById('selectedFilesDocumento');

            if (archivosInput && selectedFilesDiv) {
                archivosInput.addEventListener('change', function(e) {
                    selectedFilesDiv.innerHTML = '';
                    Array.from(this.files).forEach((file, index) => {
                        const fileDiv = document.createElement('div');
                        fileDiv.className = 'selected-file';
                        fileDiv.innerHTML = `
                            <span class="selected-file-name">${file.name}</span>
                            <button type="button" class="remove-file" data-index="${index}">×</button>
                        `;
                        selectedFilesDiv.appendChild(fileDiv);
                    });

                    // Agregar event listeners para los botones de eliminar
                    selectedFilesDiv.querySelectorAll('.remove-file').forEach(button => {
                        button.addEventListener('click', function() {
                            const dt = new DataTransfer();
                            const { files } = archivosInput;
                            const index = parseInt(this.dataset.index);

                            for (let i = 0; i < files.length; i++) {
                                if (i !== index) dt.items.add(files[i]);
                            }

                            archivosInput.files = dt.files;
                            this.closest('.selected-file').remove();

                            // Actualizar los índices de los botones restantes
                            selectedFilesDiv.querySelectorAll('.remove-file').forEach((btn, idx) => {
                                btn.dataset.index = idx;
                            });
                        });
                    });
                });
            }

            // Manejar el envío del formulario de nuevo documento
            const formNuevoDocumento = document.getElementById('formNuevoDocumento');
            if (formNuevoDocumento) {
                formNuevoDocumento.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    try {
                        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                        if (!token) {
                            throw new Error('No se encontró el token de autenticación');
                        }

                        const formData = new FormData();
                        
                        // Obtener los valores de los campos
                        const expedienteId = document.getElementById('expediente_id').value;
                        const titulo = document.getElementById('titulo').value;
                        const comentarios = document.getElementById('comentarios').value;
                        
                        console.log('Valores a enviar:', {
                            expediente_id: expedienteId,
                            titulo: titulo,
                            comentarios: comentarios
                        });

                        // Agregar los campos al FormData
                        formData.append('expediente_id', expedienteId);
                        formData.append('parte', 'Árbitro'); // Siempre será Árbitro para admin/staff
                        formData.append('sumilla', titulo); // El título se guarda como sumilla
                        formData.append('comentarios', comentarios);
                        
                        // Agregar los archivos
                        const archivosInputForm = document.getElementById('archivosDocumento');
                        for (let i = 0; i < archivosInputForm.files.length; i++) {
                            formData.append('archivos[]', archivosInputForm.files[i]);
                        }

                        console.log('Enviando petición...');
                        
                        const response = await fetch('/api/participe-documentos', {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        console.log('Response status:', response.status);
                        console.log('Response ok:', response.ok);

                        const result = await response.json();
                        console.log('Respuesta del servidor:', result);

                        if (!response.ok) {
                            console.error('Error completo:', result);
                            if (result.errores) {
                                console.error('Errores de validación:', result.errores);
                                const erroresTexto = Object.entries(result.errores)
                                    .map(([campo, mensajes]) => `${campo}: ${mensajes.join(', ')}`)
                                    .join('\n');
                                throw new Error(`Error de validación:\n${erroresTexto}`);
                            }
                            throw new Error(result.mensaje || result.message || 'Error al enviar el documento');
                        }

                        alert('Documento presentado exitosamente');
                        cerrarModalNuevoDocumento();
                        cargarDocumentos(); // Recargar la lista de documentos
                    } catch (error) {
                        console.error('Error:', error);
                        console.log('Detalles del error:', error);
                        alert(error.message || 'Error al presentar el documento');
                    }
                });
            }

            // Manejar el envío del formulario de cédula (enviar JSON a /api/cedulas)
            const formCedula = document.getElementById('formGenerarCedula');
            if (formCedula) {
                formCedula.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('Iniciando envío de cédula...');

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

                        console.log('Payload a enviar:', JSON.stringify(payload, null, 2));

                        const response = await fetch('/api/cedulas', {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        console.log('Response status:', response.status);
                        console.log('Response ok:', response.ok);

                        if (!response.ok) {
                            const err = await response.json().catch(() => null);
                            console.error('Error response:', err);
                            throw new Error((err && err.message) || 'Error al generar la cédula');
                        }

                        // Leer la respuesta (backend devuelve { status, id, cedula })
                        const created = await response.json();
                        console.log(' Cédula creada:', created);

                        alert('Cédula generada exitosamente');
                        closeGenerarCedulaModal();

                        // Redirigir al listado de cédulas del expediente
                        const documentoIdForRedirect = documentoId || (created && created.id) || '';
                        const expedienteIdParam = expedienteId || new URLSearchParams(window.location.search).get('id');

                        console.log('Redirigiendo con expedienteId:', expedienteIdParam, 'documentoId:', documentoIdForRedirect);

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

                // Convertir habilitado a boolean explícitamente
                const isHabilitado = doc.habilitado === true || doc.habilitado === 1 || doc.habilitado === '1';

                // Calcular tamaño total de archivos en bytes
                const tamanoBytes = doc.archivos && doc.archivos.length > 0 
                    ? doc.archivos.reduce((total, archivo) => total + (archivo.tamano || 0), 0)
                    : 0;
                
                // Formatear según el tamaño: KB si es menor a 1MB, MB si es mayor
                let tamanoFormateado = '0.00kb';
                if (tamanoBytes > 0) {
                    if (tamanoBytes < 1024 * 1024) {
                        // Mostrar en KB con 2 decimales
                        const tamanoKB = tamanoBytes / 1024;
                        tamanoFormateado = `${tamanoKB.toFixed(2)}kb`;
                    } else {
                        // Mostrar en MB con 2 decimales
                        const tamanoMB = tamanoBytes / (1024 * 1024);
                        tamanoFormateado = `${tamanoMB.toFixed(2)}mb`;
                    }
                }

                tbody.innerHTML += `
                <tr class="${!isHabilitado ? 'bg-opacity-40' : ''} hover:bg-gray-50" data-doc-id="${doc.id}">
                    <td class="px-6 py-4 text-center">
                        <input type="checkbox" 
                                ${isHabilitado ? 'checked' : ''} 
                                onchange="toggleHabilitado(${doc.id}, this.checked, this)"
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
                <td class="px-6 py-4 text-center text-gray-900">${tamanoFormateado}</td>
                <td class="px-6 py-4">
                    <div class="flex justify-end space-x-2">
                    <button onclick="revisarDocumento(${doc.id})" class="bg-black text-white px-3 py-1 rounded text-sm">
                        Revisar
                    </button>
                    <button onclick="verDocumento(${doc.id})" class="bg-gray-600 text-white px-3 py-1 rounded text-sm">
                        Ver
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
                ws['!cols'] = [{
                        wch: 12
                    }, // FECHA
                    {
                        wch: 10
                    }, // HORA
                    {
                        wch: 30
                    }, // TITULO
                    {
                        wch: 20
                    }, // USUARIO
                    {
                        wch: 15
                    } // ROL
                ];

                XLSX.utils.book_append_sheet(wb, ws, 'Documentos');

                // Descargar archivo
                const fecha = new Date();
                const dia = String(fecha.getDate()).padStart(2, '0');
                const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                const anio = fecha.getFullYear();
                const filename = `Documentos_${dia}_${mes}_${anio}.xlsx`;

                console.log('Descargando archivo:', filename);
                XLSX.writeFile(wb, filename);

                console.log('Exportación completada exitosamente');
            } catch (error) {
                console.error('Error al exportar:', error);
                alert('Error al exportar los documentos a Excel: ' + error.message);
            }
        }

        function openCreateModal() {
            const modal = document.getElementById('modalNuevoDocumento');
            const expedienteId = new URLSearchParams(window.location.search).get('id') || 
                                 new URLSearchParams(window.location.search).get('expediente_id');
            
            // Establecer el expediente_id
            document.getElementById('expediente_id').value = expedienteId;
            
            // Limpiar formulario
            document.getElementById('titulo').value = '';
            document.getElementById('comentarios').value = '';
            document.getElementById('archivosDocumento').value = '';
            document.getElementById('selectedFilesDocumento').innerHTML = '';
            
            modal.classList.add('active');
        }

        function cerrarModalNuevoDocumento() {
            const modal = document.getElementById('modalNuevoDocumento');
            modal.classList.remove('active');
            document.getElementById('formNuevoDocumento').reset();
            document.getElementById('selectedFilesDocumento').innerHTML = '';
        }

        async function revisarDocumento(id) {
            try {
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                if (!token) {
                    throw new Error('No se encontró el token de autenticación');
                }

                const response = await fetch(`/api/participe-documentos/${id}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar el documento');
                }

                const result = await response.json();
                
                // Establecer el ID del documento
                document.getElementById('documentoIdRevision').value = id;
                
                // Mostrar los archivos adjuntos en el modal
                const archivosContainer = document.getElementById('archivosRevisionList');
                if (result.registro && result.registro.archivos && result.registro.archivos.length > 0) {
                    archivosContainer.innerHTML = result.registro.archivos.map(archivo => `
                        <div style="display: flex; align-items: center; padding: 12px; background: #f5f5f5; border-radius: 4px; margin-bottom: 10px; gap: 12px;">
                            <svg style="width: 20px; height: 20px; min-width: 20px; opacity: 0.7;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                <polyline points="13 2 13 9 20 9"></polyline>
                            </svg>
                            <span style="flex: 1; font-size: 13px; color: #333; word-break: break-word;">${archivo.archivo_adjunto.split('/').pop()}</span>
                            <button type="button" onclick="abrirArchivo('${archivo.archivo_adjunto}')" style="padding: 6px 16px; background: #000; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; white-space: nowrap;">Ver</button>
                        </div>
                    `).join('');
                } else {
                    archivosContainer.innerHTML = '<p style="color: #666; font-size: 13px; text-align: center; padding: 10px;">No hay archivos adjuntos</p>';
                }
                
                // Mostrar el modal
                document.getElementById('revisionModal').classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Error al cargar el documento');
            }
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
            try {
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                if (!token) {
                    throw new Error('No se encontró el token de autenticación');
                }

                const response = await fetch(`/api/participe-documentos/${id}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Error al cargar el documento');
                }

                const result = await response.json();
                if (result.registro && result.registro.archivos) {
                    mostrarArchivosEnModal(result.registro.archivos);
                } else {
                    alert('No se encontraron archivos adjuntos');
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'Error al cargar los archivos');
            }
        }

        function mostrarArchivosEnModal(archivos) {
            const container = document.getElementById('archivosDocumentoList');
            container.innerHTML = archivos.map(archivo => `
                <div style="display: flex; align-items: center; padding: 15px; background: #f5f5f5; border-radius: 4px; margin-bottom: 12px; gap: 15px;">
                    <svg style="width: 24px; height: 24px; min-width: 24px; opacity: 0.7;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                        <polyline points="13 2 13 9 20 9"></polyline>
                    </svg>
                    <span style="flex: 1; font-size: 14px; color: #333; word-break: break-word;">${archivo.archivo_adjunto.split('/').pop()}</span>
                    <button onclick="abrirArchivo('${archivo.archivo_adjunto}')" style="padding: 8px 20px; background: #000; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; white-space: nowrap; font-weight: 500;">Abrir</button>
                </div>
            `).join('');
            const modal = document.getElementById('modalVerArchivos');
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
        }

        function abrirArchivo(ruta) {
            window.open('/storage/' + ruta, '_blank');
        }

        function cerrarModalVerArchivos() {
            const modal = document.getElementById('modalVerArchivos');
            modal.style.display = 'none';
            modal.classList.add('hidden');
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

        async function toggleHabilitado(id, nuevoEstado, checkboxElement) {
            // Guardar el estado anterior por si necesitamos revertir
            const estadoAnterior = !nuevoEstado;

            try {
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                const response = await fetch(`/api/participe-documentos/${id}/toggle-habilitado`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.error || 'Error al actualizar el estado del documento');
                }

                const result = await response.json();

                // Actualizar visualmente con el estado real del servidor
                checkboxElement.checked = result.habilitado === true || result.habilitado === 1;

                // Actualizar el estilo de la fila
                const row = checkboxElement.closest('tr');
                if (row) {
                    if (result.habilitado) {
                        row.classList.remove('bg-opacity-40');
                    } else {
                        row.classList.add('bg-opacity-40');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert(error.message);
                // Revertir el estado del checkbox si hubo error
                checkboxElement.checked = estadoAnterior;
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
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl" style="max-height: 90vh; overflow: hidden; margin: 0 auto;">
            <div style="padding: 24px 30px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 20px; font-weight: 600; color: #333; margin: 0;">¿Quieres aprobar el documento?</h2>
                <button onclick="closeRevisionModal()" style="color: #999; cursor: pointer; background: none; border: none;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="revisionForm" style="display: flex; flex-direction: column;">
                <input type="hidden" id="documentoIdRevision" name="documentoId">

                <div style="padding: 20px 30px; max-height: 50vh; overflow-y: auto;">
                    <!-- Documentos adjuntos -->
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #333; margin-bottom: 10px;">Documentos adjuntos:</label>
                        <div id="archivosRevisionList">
                            <!-- Los archivos se cargarán dinámicamente -->
                        </div>
                    </div>

                    <!-- Resolución -->
                    <div>
                        <label style="display: block; font-size: 14px; font-weight: 500; color: #333; margin-bottom: 8px;">Resolución (opcional)</label>
                        <div style="border: 2px dashed #d1d5db; border-radius: 6px; padding: 16px;">
                            <div style="display: flex; align-items: center; justify-content: center;">
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <svg style="width: 20px; height: 20px; color: #999;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    <span style="font-size: 13px; color: #666;">Adjuntar archivos</span>
                                    <input type="file" multiple class="hidden" name="archivos">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="padding: 16px 30px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                    <button type="button" onclick="closeRevisionModal()" style="padding: 8px 16px; background: transparent; color: #666; border: none; cursor: pointer; font-size: 13px;">
                        Cerrar
                    </button>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" onclick="rechazarDocumento()" style="padding: 8px 20px; background: #e5e7eb; color: #333; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;">
                            Rechazar
                        </button>
                        <button type="submit" style="padding: 8px 20px; background: #000; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;">
                            Aprobar
                        </button>
                    </div>
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

    <!-- Modal Ver Archivos del Documento -->
    <div id="modalVerArchivos" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-xl" style="margin: 0 auto; max-height: 90vh; overflow: hidden;">
            <div style="padding: 24px 30px; border-bottom: 1px solid #e5e7eb;">
                <h2 style="font-size: 20px; font-weight: 600; color: #333; margin: 0;">Archivos del documento</h2>
            </div>
            <div id="archivosDocumentoList" style="padding: 20px 30px; max-height: 400px; overflow-y: auto;">
                <!-- Los archivos se llenarán dinámicamente -->
            </div>
            <div style="padding: 16px 30px; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end;">
                <button type="button" onclick="cerrarModalVerArchivos()" style="padding: 8px 20px; background: #d1d5db; color: #333; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        // Cerrar modal de ver archivos al hacer clic fuera
        document.addEventListener('DOMContentLoaded', function() {
            const modalVerArchivos = document.getElementById('modalVerArchivos');
            if (modalVerArchivos) {
                modalVerArchivos.addEventListener('click', function(e) {
                    if (e.target === this) {
                        cerrarModalVerArchivos();
                    }
                });
            }
        });
    </script>
    @endsection