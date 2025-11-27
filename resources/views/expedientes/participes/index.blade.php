        <style>
        /* Mejoras drag&drop y lista archivos en modal Solicitudes */
        .modal-solicitudes-lista {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .modal-solicitudes-file {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1.5px solid #e5e7eb;
            border-radius: 7px;
            padding: 8px 14px;
            font-size: 15px;
            color: #222;
            justify-content: space-between;
            transition: border 0.2s;
        }
        .modal-solicitudes-file .modal-solicitudes-remove {
            background: none;
            border: none;
            color: #e11d48;
            font-size: 1.3em;
            margin-left: 10px;
            cursor: pointer;
            font-weight: bold;
        }
        #dropzoneSolicitud.dragover {
            border-color: #6366f1;
            background: #f1f5ff;
        }
        </style>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red Nacional de Arbitraje - Consulta de expedientes</title>
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #f5f5f5;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
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

        .logo-text {
            color: white;
            font-size: 12px;
            line-height: 1.3;
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

        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.15);
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

        .modal-logout-overlay.active {
            display: flex;
        }

        .modal-logout {
            background-color: white;
            border-radius: 4px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            padding: 30px;
            text-align: center;
        }

        .modal-logout h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
        }

        .modal-logout p {
            color: #333;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .modal-logout-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn-confirmar-logout {
            background-color: #c33;
            color: white;
            border: none;
            padding: 10px 30px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 3px;
        }

        .btn-confirmar-logout:hover {
            background-color: #a22;
        }

        .btn-cancelar-logout {
            background-color: #ccc;
            color: #333;
            border: none;
            padding: 10px 30px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 3px;
        }

        .btn-cancelar-logout:hover {
            background-color: #aaa;
        }

        /* === MAIN === */
        main {
            padding: 40px 80px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 30px;
            font-weight: 400;
        }

        .table-scroll-container {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
            margin: 20px 0;
            background: white;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-section {
            display: flex;
            gap: 30px;
            margin-bottom: 40px;
        }

        .search-input {
            flex: 1;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 15px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 15px center;
        }

        .search-btn {
            background-color: #000;
            color: white;
            border: none;
            padding: 12px 130px;
            font-size: 15px;
            cursor: pointer;
            border-radius: 3px;
        }

        .search-btn:hover {
            background-color: #333;
        }

        table {
            width: 100%;
            background-color: white;
            border-collapse: collapse;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        thead {
            background-color: #6b6b6b;
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 500;
            font-size: 14px;
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        tbody tr:hover {
            background-color: #f9f9f9;
        }

        .btn-ver {
            background-color: #000;
            color: white;
            border: none;
            padding: 8px 25px;
            cursor: pointer;
            font-size: 13px;
            border-radius: 3px;
        }

        .btn-ver:hover {
            background-color: #333;
        }

        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge.en-tramite {
            background-color: #FFF3CD;
            color: #856404;
        }

        .badge.suspendido {
            background-color: #F8D7DA;
            color: #721C24;
        }

        .badge.archivado {
            background-color: #D4EDDA;
            color: #155724;
        }

        .badge.concluido {
            background-color: #CCE5FF;
            color: #004085;
        }

        .btn-seguir {
            background-color: #000;
            color: white;
            border: none;
            padding: 8px 20px;
            cursor: pointer;
            font-size: 13px;
            border-radius: 3px;
        }

        .btn-seguir:hover {
            background-color: #333;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 15px;
            background-color: white;
        }

        .pagination-btn {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .pagination-btn:hover {
            color: #000;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-numbers {
            display: flex;
            gap: 8px;
        }

        .page-number {
            background: none;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 14px;
            color: #666;
            border-radius: 3px;
            text-decoration: none;
        }

        .page-number:hover {
            background-color: #f0f0f0;
        }

        .page-number.active {
            background-color: #e0e0e0;
            color: #000;
        }

        /* === MODAL === */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background-color: white;
            border-radius: 4px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 25px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            font-size: 24px;
            font-weight: 400;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #666;
        }

        .modal-close:hover {
            color: #000;
        }

        .modal-body {
            padding: 30px;
            overflow-y: auto;
            max-height: 70vh;
        }

        .modal-section {
            margin-bottom: 30px;
        }

        .expediente-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .expediente-nombre {
            font-size: 16px;
            color: #333;
        }

        .expediente-detalles {
            display: flex;
            gap: 15px;
            font-size: 14px;
            color: #666;
            margin-top: 4px;
            padding-left: 2px;
        }

        .expediente-detalles span {
            display: inline-flex;
            align-items: center;
        }

        .modal-section h3 {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 15px;
            color: #333;
        }

        .modal-field {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }

        .modal-field label {
            font-weight: 500;
            color: #666;
        }

        .modal-field value {
            color: #333;
            text-align: right;
        }

        .modal-field:last-child {
            border-bottom: none;
        }

        .participes-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .participes-table th {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: left;
            font-weight: 500;
            color: #333;
            border-bottom: 1px solid #ddd;
        }

        .participes-table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid #e0e0e0;
            display: flex;
            justify-content: flex-end;
        }

        .btn-cerrar {
            background-color: #ccc;
            color: #333;
            border: none;
            padding: 10px 30px;
            cursor: pointer;
            font-size: 14px;
            border-radius: 3px;
        }

        .btn-cerrar:hover {
            background-color: #aaa;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .error {
            background-color: #fde4e4;
            color: #c33;
            padding: 15px;
            border-radius: 3px;
            margin-bottom: 15px;
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

    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <main>
        <h1>Consulta de expedientes</h1>

        <div class="search-section">
            <div style="display: flex; gap: 12px; flex: 1;">
                <input type="text" class="search-input" placeholder="Buscar">
                <button class="search-btn" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">Buscar</button>
            </div>
            <button class="search-btn" id="btnSolicitudes" style="background-color: #6b6b6b; color: #fff; margin-left: 30px;">Solicitudes</button>
            <!-- MODAL SOLICITUDES -->
            <div class="modal-overlay" id="modalSolicitudes" style="display:none;">
                <div class="modal">
                    <div class="modal modal-solicitudes-custom">
                        <div class="modal-solicitudes-header">
                            <h2>Nueva Solicitud</h2>
                            <button class="modal-close" id="closeSolicitudes">×</button>
                        </div>
                        <form id="formSolicitudes" class="modal-solicitudes-form">
                            <div class="modal-body">
                                <div class="modal-solicitudes-fields">
                                    <div class="modal-solicitudes-row">
                                        <div>
                                            <label for="demandadoSolicitud">DEMANDADO:</label>
                                            <select id="demandadoSolicitud" name="demandado" required></select>

                                        </div>
                                    </div>
                                    <div>
                                        <label for="documentosSolicitud">DOCUMENTOS:</label>
                                        <div id="dropzoneSolicitud">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                            <div class="modal-solicitudes-drop">Arrastra archivos aquí o haz clic para seleccionar</div>
                                            <div class="modal-solicitudes-help">Formatos permitidos: PDF, DOC, DOCX, JPG, PNG (Máx. 10 MB por archivo)</div>
                                            <input type="file" id="documentosSolicitud" name="documentos[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none;">
                                        </div>
                                        <div id="listaArchivos" class="modal-solicitudes-lista"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer modal-solicitudes-footer">
                                <button type="button" class="btn-cerrar" id="cancelarSolicitudes">CANCELAR</button>
                                <button type="submit" class="btn-seguir">GUARDAR</button>
                            </div>
                        </form>
                    </div>
                        <style>
                        /* MODAL SOLICITUDES AISLADO */
                        .modal-solicitudes-custom {
                            max-width: 600px;
                            width: 96vw;
                            margin: 0 auto;
                            border-radius: 10px;
                            background: #fff;
                            box-shadow: 0 4px 32px rgba(0,0,0,0.18);
                            position: relative;
                            overflow: hidden;
                        }
                        .modal-solicitudes-header {
                            background: #fff;
                            padding: 28px 32px 4px 32px;
                            border-radius: 10px 10px 0 0;
                            position: relative;
                        }
                        .modal-solicitudes-header h2 {
                            color: #181818;
                            font-size: 2rem;
                            font-weight: 500;
                            margin: 0;
                            letter-spacing: -1px;
                        }
                        .modal-solicitudes-header .modal-close {
                            position: absolute;
                            top: 24px;
                            right: 32px;
                            background: none;
                            border: none;
                            color: #181818;
                            font-size: 2rem;
                            cursor: pointer;
                        }
                        .modal-solicitudes-form {
                            background: #fff;
                            border-radius: 0 0 10px 10px;
                            padding: 32px 32px 0 32px;
                        }
                        .modal-solicitudes-fields {
                            display: flex;
                            flex-direction: column;
                            gap: 28px;
                        }
                        .modal-solicitudes-fields label {
                            display: block;
                            font-weight: bold;
                            text-transform: uppercase;
                            color: #222;
                            margin-bottom: 8px;
                        }
                        .modal-solicitudes-required {
                            color: #e11d48;
                        }
                        .modal-solicitudes-fields select {
                            width: 100%;
                            padding: 14px 16px;
                            border: 1.5px solid #d1d5db;
                            border-radius: 8px;
                            font-size: 1rem;
                            background: #fafbfc;
                            outline: none;
                        }
                        .modal-solicitudes-row {
                            display: flex;
                            gap: 20px;
                        }
                        .modal-solicitudes-row > div {
                            flex: 1;
                        }
                        .modal-solicitudes-help {
                            color: #6b7280;
                            font-size: 0.95em;
                            margin-top: 4px;
                        }
                        #dropzoneSolicitud {
                            border: 1.5px dashed #cbd5e1;
                            border-radius: 10px;
                            background: #fafbfc;
                            padding: 32px 0;
                            text-align: center;
                            cursor: pointer;
                            transition: border-color 0.2s;
                        }
                        .modal-solicitudes-clip {
                            font-size: 2.2rem;
                            color: #6b7280;
                            margin-bottom: 8px;
                        }
                        .modal-solicitudes-drop {
                            font-weight: 600;
                            color: #222;
                        }
                        .modal-solicitudes-lista {
                            margin-top:12px;
                            font-size:14px;
                            color:#333;
                        }
                        .modal-solicitudes-footer {
                            display: flex;
                            justify-content: flex-end;
                            gap: 16px;
                            padding: 32px 0 24px 0;
                            background: #fff;
                            border-radius: 0 0 10px 10px;
                        }
                        .modal-solicitudes-footer .btn-cerrar,
                        .modal-solicitudes-footer .btn-seguir {
                            padding: 12px 36px;
                            font-size: 1.1rem;
                            font-weight: 600;
                            border-radius: 8px;
                            border: none;
                        }
                        .modal-solicitudes-footer .btn-cerrar {
                            background: #f3f4f6;
                            color: #222;
                        }
                        .modal-solicitudes-footer .btn-seguir {
                            background: #181818;
                            color: #fff;
                        }
                        @media (max-width: 600px) {
                            .modal-solicitudes-custom { padding: 0; }
                            .modal-solicitudes-header, .modal-solicitudes-form { padding-left: 10px; padding-right: 10px; }
                        }
                        </style>
                </div>
            </div>
            <script>
            // Modal Solicitudes
            document.getElementById('btnSolicitudes').addEventListener('click', async function() {
                document.getElementById('modalSolicitudes').style.display = 'flex';
                await cargarParticipesSolicitudes();
            });
            document.getElementById('closeSolicitudes').addEventListener('click', function() {
                document.getElementById('modalSolicitudes').style.display = 'none';
            });
            document.getElementById('cancelarSolicitudes').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('modalSolicitudes').style.display = 'none';
            });
            document.getElementById('modalSolicitudes').addEventListener('click', function(e) {
                if (e.target === this) this.style.display = 'none';
            });
            

            // Drag & drop y lista de archivos para Solicitudes
            const dropzone = document.getElementById('dropzoneSolicitud');
            const inputArchivos = document.getElementById('documentosSolicitud');
            const listaDiv = document.getElementById('listaArchivos');
            let archivosSeleccionados = [];

            function renderListaArchivos() {
                listaDiv.innerHTML = '';
                if (archivosSeleccionados.length === 0) return;
                archivosSeleccionados.forEach((file, idx) => {
                    const div = document.createElement('div');
                    div.className = 'modal-solicitudes-file';
                    div.innerHTML = `${file.name} <span style="color:#6b7280; font-size:0.97em;">(${(file.size/1024).toFixed(1)} KB)</span> <button type="button" class="modal-solicitudes-remove" title="Eliminar archivo">&times;</button>`;
                    div.querySelector('button').onclick = () => {
                        archivosSeleccionados.splice(idx, 1);
                        renderListaArchivos();
                    };
                    listaDiv.appendChild(div);
                });
            }

            dropzone.addEventListener('click', () => inputArchivos.click());
            dropzone.addEventListener('dragover', e => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });
            dropzone.addEventListener('dragleave', e => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            });
            dropzone.addEventListener('drop', e => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
                const files = Array.from(e.dataTransfer.files);
                files.forEach(f => {
                    if (!archivosSeleccionados.some(a => a.name === f.name && a.size === f.size)) {
                        archivosSeleccionados.push(f);
                    }
                });
                renderListaArchivos();
            });
            inputArchivos.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                files.forEach(f => {
                    if (!archivosSeleccionados.some(a => a.name === f.name && a.size === f.size)) {
                        archivosSeleccionados.push(f);
                    }
                });
                renderListaArchivos();
                // Limpiar input para permitir volver a seleccionar el mismo archivo si se elimina
                inputArchivos.value = '';
            });

            // Al abrir el modal, limpiar archivos
            document.getElementById('btnSolicitudes').addEventListener('click', function() {
                archivosSeleccionados = [];
                renderListaArchivos();
            });
            
            document.getElementById('formSolicitudes').addEventListener('submit', async function(e) {
                e.preventDefault();
                // Eliminar mensajes de error previos
                let errorDiv = document.getElementById('solicitudErrorMsg');
                if (errorDiv) errorDiv.remove();

                const formData = new FormData();
                // Obtener valores
                let estado = 'Pendiente';
                const estadoInput = document.getElementById('estadoSolicitud');
                if (estadoInput) {
                    estado = estadoInput.value || 'Pendiente';
                }
                // Obtener el partícipe logueado desde el usuario
                const usuario = getUsuario();
                let participeId = null;
                let errorParticipe = false;
                try {
                    const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                    const res = await fetch('/api/participes', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        const participes = data.registros || [];
                        if (usuario && usuario.credencial_id) {
                            const miParticipe = participes.find(p => String(p.credencial_id) === String(usuario.credencial_id));
                            if (miParticipe) {
                                participeId = miParticipe.id;
                            } else {
                                errorParticipe = true;
                            }
                        } else {
                            errorParticipe = true;
                        }
                    } else {
                        errorParticipe = true;
                    }
                } catch (e) {
                    errorParticipe = true;
                }
                const demandado = document.getElementById('demandadoSolicitud').value;

                if (!demandado) {
                    mostrarError('Debe seleccionar un demandado.');
                    return;
                }
                formData.append('participe_id', participeId);
                formData.append('estado', estado);
                formData.append('demandante', participeId);
                formData.append('demandado', demandado);
                archivosSeleccionados.forEach(f => formData.append('archivos[]', f));

                // Debug: mostrar en consola el payload
                for (let pair of formData.entries()) {
                    console.log('FORMDATA', pair[0]+':', pair[1]);
                }

                const mostrarError = (msg) => {
                    let err = document.createElement('div');
                    err.className = 'error';
                    err.id = 'solicitudErrorMsg';
                    err.textContent = msg;
                    // Insertar error arriba del footer
                    const modalBody = this.querySelector('.modal-body') || this.parentElement.querySelector('.modal-body');
                    if (modalBody) {
                        modalBody.insertBefore(err, modalBody.firstChild);
                    }
                };

                try {
                    const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                    const res = await fetch('/api/solicitudes', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`
                        },
                        body: formData
                    });
                    let responseText = await res.text();
                    let responseJson = null;
                    try {
                        responseJson = JSON.parse(responseText);
                    } catch (e) {}
                    // Debug: mostrar en consola la respuesta
                    console.log('RESPUESTA API', responseJson || responseText);
                    if (!res.ok) {
                        let msg = 'Error al guardar la solicitud';
                        if (responseJson) {
                            if (responseJson.errors) {
                                msg += ': ' + Object.values(responseJson.errors).flat().join(' ');
                            } else if (responseJson.message) {
                                msg += ': ' + responseJson.message;
                            } else {
                                msg += ` (HTTP ${res.status})`;
                            }
                        } else {
                            msg += ` (HTTP ${res.status})\nRespuesta: ${responseText}`;
                        }
                        mostrarError(msg);
                        return;
                    }
                    // Éxito: mostrar la respuesta completa de la API
                    let msg = 'Solicitud guardada correctamente.';
                    mostrarToast(msg);
                    document.getElementById('modalSolicitudes').style.display = 'none';
                    this.reset();
                    archivosSeleccionados = [];
                    renderListaArchivos();
                    if (typeof cargarExpedientes === 'function') {
                        cargarExpedientes(1);
                    }
                    function mostrarToast(mensaje) {
                        let toast = document.getElementById('toastExito');
                        if (!toast) {
                            toast = document.createElement('div');
                            toast.id = 'toastExito';
                            toast.style.position = 'fixed';
                            toast.style.top = '30px';
                            toast.style.left = '50%';
                            toast.style.transform = 'translateX(-50%)';
                            toast.style.background = '#38c172';
                            toast.style.color = '#fff';
                            toast.style.padding = '16px 32px';
                            toast.style.borderRadius = '8px';
                            toast.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
                            toast.style.fontSize = '1.1em';
                            toast.style.zIndex = '3000';
                            toast.style.opacity = '0';
                            toast.style.transition = 'opacity 0.3s';
                            document.body.appendChild(toast);
                        }
                        toast.textContent = mensaje;
                        toast.style.opacity = '1';
                        setTimeout(() => {
                            toast.style.opacity = '0';
                        }, 5000);
                    }
                } catch (err) {
                    mostrarError('Error de red al guardar la solicitud: ' + (err.message || err));
                }
            });

            // Cargar partícipes y poblar selects con Tom Select
            async function cargarParticipesSolicitudes() {
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                try {
                    const res = await fetch('/api/participes', {
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) throw new Error('Error al obtener partícipes');
                    const data = await res.json();
                    const participes = data.registros || [];
                    const usuario = getUsuario();
                    // Filtrar demandados: todos menos el usuario logueado
                    const demandados = participes.filter(p => String(p.id) !== String(usuario?.id));
                    if (document.getElementById('demandadoSolicitud')) {
                        inicializarTomSelectParticipes(document.getElementById('demandadoSolicitud'), demandados);
                    }
                } catch (error) {
                    console.error('Error cargando partícipes:', error);
                }
            }

            function inicializarTomSelectParticipes(select, participes) {
                // Destruir instancia anterior si existe
                if (select.tomselect) {
                    select.tomselect.destroy();
                }
                
                // Limpiar y poblar opciones
                const selectedValue = select.value;
                select.innerHTML = `<option value="">Seleccionar partícipe</option>`;
                participes.forEach(p => {
                    const nombreCompleto = `${p.nombres ?? ''} ${p.apellidos ?? ''}`.trim();
                    const option = document.createElement('option');
                    option.value = p.id;
                    option.textContent = nombreCompleto || 'Partícipe sin nombre';
                    if (p.id == selectedValue) option.selected = true;
                    select.appendChild(option);
                });
                
                // Inicializar Tom Select
                new TomSelect(select, {
                    valueField: 'id',
                    labelField: 'nombreCompleto',
                    searchField: ['nombreCompleto'],
                    options: participes.map(p => ({
                        id: p.id,
                        nombreCompleto: `${p.nombres ?? ''} ${p.apellidos ?? ''}`.trim() || 'Partícipe sin nombre'
                    })),
                    create: false,
                    placeholder: 'Buscar partícipe...',
                    render: {
                        option: function(item, escape) {
                            return `<div class="py-2 px-3">${escape(item.nombreCompleto)}</div>`;
                        },
                        item: function(item, escape) {
                            return `<div>${escape(item.nombreCompleto)}</div>`;
                        }
                    },
                    loadingClass: 'loading'
                });
            }
            </script>
        </div>

        <div class="table-scroll-container">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Nombre de Expediente</th>
                        <th>Estado</th>
                        <th>Mi rol</th>
                        <th>Documentos</th>
                        <th>Actualización</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="expedientesTableBody">
                    <tr>
                        <td colspan="7" class="loading">Cargando expedientes...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <button class="pagination-btn" id="btnAnterior">← Anterior</button>
            <div class="pagination-numbers" id="paginationNumbers">
                <button class="page-number active">1</button>
            </div>
            <button class="pagination-btn" id="btnSiguiente">Siguiente →</button>
        </div>
    </main>

    <!-- MODAL -->
    <div class="modal-overlay" id="modalOverlay">
        <div class="modal">
            <div class="modal-header">
                <h2>Detalles del expediente</h2>
                <button class="modal-close" id="modalClose">×</button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="loading">Cargando...</div>
            </div>
            <div class="modal-footer">
                <button class="btn-cerrar" id="btnCerrar">Cerrar</button>
            </div>
        </div>
    </div>

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

    <script>
        const API_BASE = '/api';
        let currentPage = 1;
        let totalPages = 1;

        function getToken() {
            return localStorage.getItem('token') || sessionStorage.getItem('token');
        }

        function getUsuario() {
            const usuarioStr = localStorage.getItem('usuario') || sessionStorage.getItem('usuario');
            return usuarioStr ? JSON.parse(usuarioStr) : null;
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

            cargarExpedientes(1);
        });

        // Cargar expedientes
        async function cargarExpedientes(page = 1) {
            const token = getToken();
            if (!token) {
                alert('Token no encontrado. Por favor inicia sesión.');
                return;
            }

            const searchValue = document.querySelector('.search-input').value;

            try {
                const url = new URL(`${API_BASE}/expedientes/participes`, window.location.origin);
                url.searchParams.append('page', page);
                if (searchValue) url.searchParams.append('search', searchValue);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Error al cargar expedientes');

                const result = await response.json();
                renderExpedientes(result.registros);

                currentPage = result.meta.current_page;
                totalPages = result.meta.last_page;

                actualizarPaginacion();
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('expedientesTableBody').innerHTML =
                    `<tr><td colspan="7" class="error">Error al cargar los expedientes</td></tr>`;
            }
        }

        function formatearNombreExpediente(expediente) {
            const nombre = expediente.numero || 'Expediente';
            const anio = expediente.anio || '—';
            const codigo = expediente.codigo || '—';
            return `${nombre} - ${anio}/${codigo}`;
        }

        function formatearFecha(fecha) {
            if (!fecha) return '—';
            const date = new Date(fecha);
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        function getEstadoClass(estado) {
            switch (estado.toLowerCase()) {
                case 'en trámite':
                case 'en tramite':
                    return 'en-tramite';
                case 'suspendido':
                    return 'suspendido';
                case 'archivado':
                    return 'archivado';
                case 'concluido':
                    return 'concluido';
                default:
                    return '';
            }
        }

        function renderExpedientes(expedientes) {
            const tbody = document.getElementById('expedientesTableBody');

            if (expedientes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="loading">No hay expedientes</td></tr>';
                return;
            }


            tbody.innerHTML = expedientes.map(exp => {
                let rol = exp.condicion || exp.participesCondiciones;
                // Mostrar 'Árbitro' solo si fue creado por admin/staff
                if (exp.creado_por_admin) {
                    rol = 'Árbitro';
                } else {
                    if (!rol) rol = 'Sin rol';
                }
                return `
                <tr>
                    <td><button class="btn-ver" data-id="${exp.id}">Ver</button></td>
                    <td>${exp.codigo}</td>
                    <td><span class="badge ${getEstadoClass(exp.estado)}">${exp.estado}</span></td>
                    <td>${rol}</td>
                    <td>${exp.cantidad_documentos || '0'}</td>
                    <td>${formatearFecha(exp.fecha_actualizacion)}</td>
                    <td><button class="btn-seguir" data-id="${exp.id}" data-nombre="${formatearNombreExpediente(exp)}">Seguir trámite</button></td>
                </tr>
                `;
            }).join('');

            agregarEventosABotones();
        }

        function agregarEventosABotones() {
            document.querySelectorAll('.btn-ver').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const expedienteId = this.getAttribute('data-id');
                    const token = getToken();

                    if (!token) {
                        alert('Token no encontrado. Por favor inicia sesión.');
                        return;
                    }

                    document.getElementById('modalOverlay').classList.add('active');
                    document.getElementById('modalBody').innerHTML = '<div class="loading">Cargando detalles...</div>';

                    try {
                        const response = await fetch(`${API_BASE}/expedientes/${expedienteId}`, {
                            method: 'GET',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) throw new Error('Error al cargar el expediente');

                        const result = await response.json();

                        if (result.status && result.data) {
                            renderModalContent(result.data);
                        } else {
                            document.getElementById('modalBody').innerHTML = '<div class="error">No se pudo cargar la información del expediente</div>';
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        document.getElementById('modalBody').innerHTML = `<div class="error">Error al cargar: ${error.message}</div>`;
                    }
                });
            });

            document.querySelectorAll('.btn-seguir').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Solo redirigir si el id y nombre son válidos y el usuario realmente hace clic
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    if (id && id !== 'null' && nombre && nombre !== 'null') {
                        window.location.href = `${window.location.origin}/expedientes/participes/seguimiento?id=${id}&nombre=${encodeURIComponent(nombre)}`;
                    } else {
                        mostrarToast('No se puede seguir el trámite: datos incompletos');
                    }
                });
            });
        }

        function renderModalContent(data) {
            let html = `
                <div class="modal-section">
                    <h3>Información del caso</h3>
                    <div class="modal-field">
                        <label>Nombre del expediente:</label>
                        <div class="expediente-info">
                            <value class="expediente-nombre">${data.numero || 'N/A'}</value>
                        </div>
                    </div>
                    <div class="modal-field">
                        <label>Año:</label>
                        <value>${data.anio || 'N/A'}</value>
                    </div>
                    <div class="modal-field">
                        <label>Codigo:</label>
                        <value>${data.codigo|| 'N/A'}</value>
                    </div>
                    <div class="modal-field">
                        <label>Tipo de expediente:</label>
                        <value>${data.tipo_proceso || 'N/A'}</value>
                    </div>
                    <div class="modal-field">
                        <label>Inicio del proceso:</label>
                        <value>${formatearFecha(data.inicio_proceso)}</value>
                    </div>
                    <div class="modal-field">
                        <label>Etapa procesal:</label>
                        <value>${data.etapa_procesal || 'N/A'}</value>
                    </div>
                    <div class="modal-field">
                        <label>Documentos subidos:</label>
                        <value>${data.cantidad_documentos || 0}</value>
                    </div>
            `;

            if (data.fecha_laudo) {
                html += `
                    <div class="modal-field">
                        <label>Fecha de Laudo Arbitral:</label>
                        <value>${formatearFecha(data.fecha_laudo)}</value>
                    </div>
                `;
            }

            if (data.fecha_resolucion) {
                html += `
                    <div class="modal-field">
                        <label>Fecha de Resolución que resuelve pedido contra Laudo Arbitral:</label>
                        <value>${formatearFecha(data.fecha_resolucion)}</value>
                    </div>
                `;
            }

            html += `</div>`;
            document.getElementById('modalBody').innerHTML = html;
        }

        function actualizarPaginacion() {
            const paginationNumbers = document.getElementById('paginationNumbers');
            paginationNumbers.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.className = `page-number ${i === currentPage ? 'active' : ''}`;
                btn.textContent = i;
                btn.addEventListener('click', () => cargarExpedientes(i));
                paginationNumbers.appendChild(btn);
            }
        }

        // Búsqueda
        document.querySelector('.search-btn').addEventListener('click', () => cargarExpedientes(1));

        // Paginación
        document.getElementById('btnAnterior').addEventListener('click', () => {
            if (currentPage > 1) cargarExpedientes(currentPage - 1);
        });

        document.getElementById('btnSiguiente').addEventListener('click', () => {
            if (currentPage < totalPages) cargarExpedientes(currentPage + 1);
        });

        // Modal
        document.getElementById('modalClose').addEventListener('click', () => {
            document.getElementById('modalOverlay').classList.remove('active');
        });

        document.getElementById('btnCerrar').addEventListener('click', () => {
            document.getElementById('modalOverlay').classList.remove('active');
        });

        document.getElementById('modalOverlay').addEventListener('click', (e) => {
            if (e.target === document.getElementById('modalOverlay')) {
                document.getElementById('modalOverlay').classList.remove('active');
            }
        });

        // Logout
        const modalLogoutOverlay = document.getElementById('modalLogoutOverlay');
        const logoutBtn = document.querySelector('.logout-btn');

        logoutBtn.addEventListener('click', () => modalLogoutOverlay.classList.add('active'));

        document.getElementById('btnCancelarLogout').addEventListener('click', () => {
            modalLogoutOverlay.classList.remove('active');
        });

        document.getElementById('btnConfirmarLogout').addEventListener('click', () => {
            localStorage.removeItem('token');
            localStorage.removeItem('usuario');
            sessionStorage.removeItem('token');
            sessionStorage.removeItem('usuario');
            window.location.href = '/login';
        });

        modalLogoutOverlay.addEventListener('click', (e) => {
            if (e.target === modalLogoutOverlay) modalLogoutOverlay.classList.remove('active');
        });
    </script>

</body>

</html>