<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red Nacional de Arbitraje - Seguimiento de expediente</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* === HEADER === */
        header {
            background-color: #6f6f6f;
            padding: 15px 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            height: 30px;
            width: auto;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 0;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 4px;
            overflow: hidden;
        }

        .user-box {
            padding: 8px 20px;
            color: white;
            font-size: 14px;
            flex: 1;
        }

        .logout-btn {
            background: transparent;
            border: none;
            border-left: 1px solid rgba(255, 255, 255, 0.5);
            color: white;
            font-size: 18px;
            padding: 8px 15px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        /* === MODAL CONFIRMAR LOGOUT === */
        .modal-logout-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal-logout-overlay.active,
        .modal-overlay.active {
            display: flex;
        }

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

        .modal-nuevo-documento {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 600px;
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
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .file-upload-button {
            position: relative;
            display: inline-block;
        }

        .file-upload-button span {
            display: inline-block;
            padding: 8px 16px;
            background: black;
            color: white;
            border-radius: 4px;
            cursor: pointer;
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
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }

        .selected-files {
            margin-top: 15px;
            text-align: left;
        }

        .selected-file {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px;
            background: #f5f5f5;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        .selected-file-name {
            font-size: 13px;
            color: #333;
            margin-right: 10px;
        }

        .remove-file {
            background: #ff4444;
            color: white;
            border: none;
            border-radius: 3px;
            padding: 2px 6px;
            cursor: pointer;
            font-size: 12px;
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

        .modal-logout-overlay.active,
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease;
            z-index: 2000;
        }

        .modal-logout {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .modal-logout h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #333;
        }


        .modal-logout-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .btn-confirmar-logout {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-cancelar-logout {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        /* === MAIN CONTENT === */
        .main-content {
            flex: 1;
            padding: 40px 80px;
        }

        .back-button {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #333;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 30px;
            cursor: pointer;
        }

        .seguimiento-container {
            background-color: #e5e5e5;
            padding: 25px;
        }

        .seguimiento-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .seguimiento-title {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .presentar-btn {
            background-color: white;
            border: 1px solid #999;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #333;
            transition: background-color 0.2s ease;
        }

        .presentar-btn:hover {
            background-color: #f9f9f9;
        }

        /* === DOCUMENTO CARD === */
        .documento-card {
            background: white;
            padding: 0;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            display: flex;
            flex-direction: row;
            gap: 0;
        }

        .documento-contenido {
            flex: 1;
            padding: 20px;
            display: flex;
            gap: 20px;
        }

        .documento-info {
            flex: 1;
        }

        .documento-info p {
            margin: 6px 0;
            color: #333;
            font-size: 14px;
            line-height: 1.5;
        }

        .documento-info .label {
            font-weight: 600;
            color: #333;
            display: inline-block;
            min-width: 140px;
        }

        .documento-info .label::after {
            content: ': ';
        }

        .botones-accion {
            margin-top: 12px;
            display: flex;
            gap: 15px;
        }

        .documento-acciones {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-accion-gris {
            background: #e9e9e9;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            color: #333;
            cursor: pointer;
            font-size: 13px;
            font-weight: 400;
            transition: background-color 0.2s ease;
        }

        .btn-accion-gris:hover {
            background: #dedede;
        }

        /* === PREVIEW SECTION === */
        .documento-preview {
            background: #f9f9f9;
            border-left: 1px solid #ddd;
            padding: 20px;
            width: 280px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .file-icon {
            width: 50px;
            height: 50px;
            margin-bottom: 12px;
            opacity: 0.7;
        }

        .file-name {
            color: #666;
            font-size: 13px;
            word-break: break-word;
            margin-bottom: 15px;
            line-height: 1.3;
        }

        .preview-buttons {
            display: flex;
            gap: 10px;
            width: 100%;
        }

        .btn-ver,
        .btn-descargar {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .btn-ver {
            background-color: #000;
            color: white;
        }

        .btn-ver:hover {
            background-color: #333;
        }

        .btn-descargar {
            background-color: #ddd;
            color: #333;
        }

        .btn-descargar:hover {
            background-color: #ccc;
        }

        .btn-disabled {
            background-color: #e8e8e8;
            color: #999;
            cursor: not-allowed;
        }

        .btn-disabled:hover {
            background-color: #e8e8e8;
        }

        /* === EMPTY STATE === */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            font-size: 15px;
        }

        /* === ERROR STATE === */
        .error-state {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 4px;
            border: 1px solid #f5c6cb;
        }

        /* === MODAL VER ARCHIVOS === */
        .modal-ver-archivos {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 600px;
        }

        .modal-ver-archivos h2 {
            font-size: 24px;
            margin: 0 0 25px 0;
            color: #333;
        }

        .archivos-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .archivo-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 4px;
            gap: 15px;
        }

        .archivo-icon {
            width: 24px;
            height: 24px;
            opacity: 0.7;
        }

        .archivo-nombre {
            flex: 1;
            font-size: 14px;
            color: #333;
        }

        .btn-abrir-archivo {
            padding: 8px 20px;
            background: #000;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }

        .btn-abrir-archivo:hover {
            background: #333;
        }

        @media (max-width: 1200px) {
            .main-content {
                padding: 40px 40px;
            }

            header {
                padding: 15px 40px;
            }

            .documento-preview {
                width: 250px;
            }
        }

        @media (max-width: 768px) {
            .documento-contenido {
                flex-direction: column;
            }

            .documento-preview {
                border-left: none;
                border-top: 1px solid #ddd;
                width: 100%;
            }

            .seguimiento-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .presentar-btn {
                width: 100%;
            }

            header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }

            .main-content {
                padding: 20px;
            }
        }

        .apago {
            width: 20px;
            height: auto;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo-section">
            <img src="/img/logo2.png" alt="Red Nacional de Arbitraje" class="logo">
            <div class="logo-text">
            </div>
        </div>
        <div class="user-section">
            <div class="user-box">
                <span id="nombreUsuario">Nombre de usuario</span>
            </div>
            <button class="logout-btn" title="Cerrar sesión"> <img src="/img/apago.png" alt="" class="apago"></button>
        </div>
    </header>

    <!-- MODAL LOGOUT -->
    <div class="modal-logout-overlay" id="modalLogoutOverlay">
        <div class="modal-logout">
            <h3>Cerrar sesión</h3>
            <p>¿Estás seguro de que deseas cerrar sesión?</p>
            <div class="modal-logout-buttons">
                <button class="btn-confirmar-logout" id="btnConfirmarLogout">Cerrar sesión</button>
                <button class="btn-cancelar-logout" id="btnCancelarLogout">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- MODAL VER ARCHIVOS -->
    <div class="modal-overlay" id="modalVerArchivos">
        <div class="modal-ver-archivos">
            <h2>Archivos del documento</h2>
            <div class="archivos-list" id="archivosDocumentoList">
                <!-- Los archivos se cargarán aquí dinámicamente -->
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancelar" onclick="cerrarModalVerArchivos()">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- MODAL NUEVO DOCUMENTO -->
    <div class="modal-overlay" id="modalNuevoDocumento">
        <div class="modal-nuevo-documento">
            <h2>Nuevo documento</h2>
            <form id="formNuevoDocumento" class="form-nuevo-documento">
                <input type="hidden" name="expediente_id" id="expediente_id">

                <div class="form-group">
                    <label>Parte</label>
                    <input type="text" name="parte" id="parte" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Sumilla</label>
                    <input type="text" name="sumilla" id="sumilla" placeholder="Ingrese la sumilla del documento" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Cargar del escritorio</label>
                    <div class="file-upload-area">
                        <div class="file-upload-button">
                            <span>Adjuntar archivos (PDF, PNG, JPG)</span>
                            <input type="file" accept=".pdf,.png,.jpg,.jpeg" name="archivos[]" class="file-input" id="archivos" multiple>
                        </div>
                        <div id="selectedFiles" class="selected-files"></div>
                    </div>
                    <p class="file-size-note">Si el archivo no supera los 10mb adjuntar en el siguiente recuadro en formato pdf, en caso superar el límite configurar el link de descarga</p>
                </div>

                <div class="form-group">
                    <label>Insertar enlace de descarga</label>
                    <input type="text" name="enlace_descarga" id="enlace_descarga" placeholder="Enlace para archivos que superan 10mb" class="form-control">
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancelar" onclick="cerrarModalNuevoDocumento()">Cancelar</button>
                    <button type="submit" class="btn-presentar">Presentar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="main-content">
        <a href="#" class="back-button" onclick="history.back(); return false;">
            ← <span id="nombreExpediente">Nombre del expediente</span>
        </a>

        <div class="seguimiento-container">
            <div class="seguimiento-header">
                <h1 class="seguimiento-title">Seguimiento de expediente</h1>
                <button class="presentar-btn" onclick="presentarDocumento()">Presentar documento</button>
            </div>

            <div id="documentos-list">
                <!-- Los documentos se cargarán aquí dinámicamente -->
            </div>
        </div>
    </div>

    <script>
        async function cargarDetallesExpediente(expedienteId) {
            if (!expedienteId) return;

            try {
                const token = getToken();
                if (!token) {
                    throw new Error('No se encontró el token de autenticación');
                }

                const response = await fetch(`/api/expedientes/${expedienteId}`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Error al cargar el expediente');

                const result = await response.json();
                if (result.status && result.data) {
                    const expediente = result.data;
                    const nombreFormateado = `${expediente.numero || 'Expediente'} - ${expediente.anio || '—'}/${expediente.codigo || '—'}`;
                    document.getElementById('nombreExpediente').textContent = nombreFormateado;
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('nombreExpediente').textContent = 'Expediente';
            }
        }

        // Verificar autenticación y mostrar nombre
        window.addEventListener('load', function() {
            const usuario = getUsuario();
            if (!usuario) {
                window.location.href = '/login';
                return;
            }

            if (usuario.nombre) {
                document.getElementById('nombreUsuario').textContent = usuario.nombre;
            }

            // Obtener y mostrar el nombre del expediente
            const params = new URLSearchParams(window.location.search);
            const expedienteId = params.get('id');

            // Cargar detalles del expediente
            cargarDetallesExpediente(expedienteId);
            cargarDocumentos();

            // Configurar handlers para el modal de nuevo documento
            window.presentarDocumento = async function() {
                const modal = document.getElementById('modalNuevoDocumento');
                const expedienteId = new URLSearchParams(window.location.search).get('id');
                
                try {
                    const token = getToken();
                    if (!token) {
                        throw new Error('No se encontró el token de autenticación');
                    }

                    // Obtener el expediente actual
                    const response = await fetch(`/api/expedientes/${expedienteId}`, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Error al cargar el expediente');
                    }

                    const result = await response.json();
                    
                    // Obtener la condición del primer participe
                    if (result.status && result.data && result.data.participes && result.data.participes.length > 0) {
                        const primerParticipe = result.data.participes[0];
                        document.getElementById('parte').value = primerParticipe.condicion;
                        console.log('Condición establecida:', primerParticipe.condicion);
                    }

                    modal.classList.add('active');
                } catch (error) {
                    console.error('Error al obtener el rol:', error);
                    modal.classList.add('active');
                }
            };

            window.cerrarModalNuevoDocumento = function() {
                const modal = document.getElementById('modalNuevoDocumento');
                modal.classList.remove('active');
                document.getElementById('formNuevoDocumento').reset();
            };

            // Cerrar modal al hacer clic fuera
            document.getElementById('modalNuevoDocumento').addEventListener('click', (e) => {
                if (e.target.id === 'modalNuevoDocumento') {
                    cerrarModalNuevoDocumento();
                }
            });

            // Configurar el input de archivos
            const archivosInput = document.getElementById('archivos');
            const selectedFilesDiv = document.getElementById('selectedFiles');

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
                        const {
                            files
                        } = archivosInput;
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

            // Manejar el envío del formulario
            document.getElementById('formNuevoDocumento').addEventListener('submit', async (e) => {
                e.preventDefault();
                
                try {
                    const token = getToken();
                    if (!token) {
                        throw new Error('No se encontró el token de autenticación');
                    }

                    const formData = new FormData();
                    
                    // Obtener los valores de los campos
                    const expedienteId = new URLSearchParams(window.location.search).get('id');
                    const parte = document.getElementById('parte').value;
                    const sumilla = document.getElementById('sumilla').value;
                    const enlaceDescarga = document.getElementById('enlace_descarga').value;
                    
                    console.log('Valores a enviar:', {
                        expediente_id: expedienteId,
                        parte: parte,
                        sumilla: sumilla,
                        enlace_descarga: enlaceDescarga
                    });

                    // Agregar los campos al FormData
                    formData.append('expediente_id', expedienteId);
                    formData.append('parte', parte);
                    formData.append('sumilla', sumilla);
                    formData.append('enlace_descarga', enlaceDescarga);
                    
                    // Agregar los archivos
                    const archivosInput = document.getElementById('archivos');
                    for (let i = 0; i < archivosInput.files.length; i++) {
                        formData.append('archivos[]', archivosInput.files[i]);
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

                    const result = await response.json();
                    console.log('Respuesta del servidor:', result);

                    if (!response.ok) {
                        throw new Error(result.message || 'Error al enviar el documento');
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
        });

        function presentarDocumento() {
            document.getElementById('modalNuevoDocumento').classList.add('active');
        }

        function cerrarModalNuevoDocumento() {
            document.getElementById('modalNuevoDocumento').classList.remove('active');
            document.getElementById('formNuevoDocumento').reset();
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modalNuevoDocumento').addEventListener('click', (e) => {
            if (e.target.id === 'modalNuevoDocumento') {
                cerrarModalNuevoDocumento();
            }
        });

        // Manejar el envío del formulario
        function getUsuario() {
            const usuarioStr = localStorage.getItem('usuario') || sessionStorage.getItem('usuario');
            return usuarioStr ? JSON.parse(usuarioStr) : null;
        }

        function getToken() {
            return localStorage.getItem('token') || sessionStorage.getItem('token');
        }

        // Configuración del modal de logout
        const modalLogoutOverlay = document.getElementById('modalLogoutOverlay');
        const logoutBtn = document.querySelector('.logout-btn');

        // Mostrar modal al hacer clic en el botón de logout
        logoutBtn.addEventListener('click', () => modalLogoutOverlay.classList.add('active'));

        // Botón cancelar cierre de sesión
        document.getElementById('btnCancelarLogout').addEventListener('click', () => {
            modalLogoutOverlay.classList.remove('active');
        });

        // Botón confirmar cierre de sesión
        document.getElementById('btnConfirmarLogout').addEventListener('click', () => {
            localStorage.removeItem('token');
            localStorage.removeItem('usuario');
            sessionStorage.removeItem('token');
            sessionStorage.removeItem('usuario');
            window.location.href = '/login';
        });

        // Cerrar modal al hacer clic fuera
        modalLogoutOverlay.addEventListener('click', (e) => {
            if (e.target === modalLogoutOverlay) modalLogoutOverlay.classList.remove('active');
        });

        function formatearFecha(fecha) {
            if (!fecha) return '—';
            const date = new Date(fecha);
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        async function cargarDocumentos() {
            const expedienteId = new URLSearchParams(window.location.search).get('id');
            if (!expedienteId) {
                console.error('ID de expediente no proporcionado');
                return;
            }

            try {
                const token = getToken();
                if (!token) {
                    throw new Error('No se encontró el token de autenticación');
                }

                const url = new URL('/api/participe-documentos', window.location.origin);
                url.searchParams.append('expediente_id', expedienteId);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al cargar documentos');
                }

                const data = await response.json();
                renderDocumentos(data.registros || []);
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('documentos-list').innerHTML = `
                    <div class="documento-card">
                        <div class="documento-contenido">
                            <div class="documento-info">
                                <p><span class="label">Presentado por</span>Nombre Apellido</p>
                                <p><span class="label">Condición</span>Demandante</p>
                                <p><span class="label">Asunto</span>Demanda A B C D</p>
                                <p><span class="label">Fecha y hora</span>DD/MM/AAAA, 00:00:00</p>
                                <p><span class="label">Proveído</span>Resolución 22</p>
                                <p><span class="label">Fecha de proveído</span>DD/MM/AAAA, 00:00:00</p>
                                <div class="documento-acciones">
                                    <button class="btn-accion-gris">Ver resolución</button>
                                    <button class="btn-accion-gris">Ver cédula</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('documentos-list').innerHTML =
                    `<div class="error-state">${error.message || 'Error al cargar los documentos'}</div>`;
            }
        }

        function renderDocumentos(documentos) {
            const container = document.getElementById('documentos-list');

            if (!Array.isArray(documentos) || documentos.length === 0) {
                container.innerHTML = `<div class="empty-state">No hay documentos presentados</div>`;
                return;
            }

            container.innerHTML = documentos.map(doc => `
                <div class="documento-card">
                    <div class="documento-contenido">
                        <div class="documento-info">
                            <p><span class="label">Presentado por</span> ${doc.participe?.nombres || doc.nombre_participe || 'Nombre Apellido'}</p>
                            <p><span class="label">Condición</span> ${doc.parte || '-'}</p>
                            <p><span class="label">Asunto</span> ${doc.sumilla || '-'}</p>
                            <p><span class="label">Fecha y hora</span> ${formatearFecha(doc.fecha_presentacion) || 'DD/MM/AAAA, 00:00:00'}</p>
                            <p><span class="label">Proveído</span> ${doc.proveido || '—'}</p>
                            <p><span class="label">Fecha de proveído</span> ${formatearFecha(doc.fecha_proveido) || '—'}</p>
                            
                            <div class="botones-accion">
                                ${doc.resolucion ? `<button class="btn-accion" onclick="verResolucion('${doc.id}')">Ver resolución</button>` : ''}
                                ${doc.cedula ? `<button class="btn-accion" onclick="verCedula('${doc.id}')">Ver cédula</button>` : ''}
                            </div>
                        </div>
                    </div>
                    
                    <div class="documento-preview">
                        <svg class="file-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                            <polyline points="13 2 13 9 20 9"></polyline>
                        </svg>
                        <p class="file-name">${doc.nombre_archivo || 'documento.pdf'}</p>
                        <div class="preview-buttons">
                            <button class="btn-ver" onclick="verDocumento('${doc.id}')">Ver</button>
                            <button class="btn-descargar" onclick="descargarDocumento('${doc.id}')">Descargar</button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function presentarDocumento() {
            // Esta función es reemplazada por la versión en window.presentarDocumento
            window.presentarDocumento();
        }

        async function verDocumento(id) {
            try {
                const token = getToken();
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
                <div class="archivo-item">
                    <svg class="archivo-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                        <polyline points="13 2 13 9 20 9"></polyline>
                    </svg>
                    <span class="archivo-nombre">${archivo.archivo_adjunto.split('/').pop()}</span>
                    <button class="btn-abrir-archivo" onclick="abrirArchivo('${archivo.archivo_adjunto}')">Abrir</button>
                </div>
            `).join('');

            document.getElementById('modalVerArchivos').classList.add('active');
        }

        function abrirArchivo(ruta) {
            window.open('/storage/' + ruta, '_blank');
        }

        function cerrarModalVerArchivos() {
            document.getElementById('modalVerArchivos').classList.remove('active');
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modalVerArchivos').addEventListener('click', (e) => {
            if (e.target.id === 'modalVerArchivos') {
                cerrarModalVerArchivos();
            }
        });

        function descargarDocumento(id) {
            console.log('Descargar documento:', id);
        }

        function verResolucion(id) {
            console.log('Ver resolución:', id);
        }

        function verCedula(id) {
            console.log('Ver cédula:', id);
        }
    </script>
</body>

</html>