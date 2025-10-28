<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red Nacional de Arbitraje - Consulta de expedientes</title>
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
            color: #666;
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
            background-color: #d4d4d4;
            color: #333;
        }

        .badge.activo {
            background-color: #c8d4c0;
            color: #2d5016;
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
            padding: 25px;
        }

        .modal-section {
            margin-bottom: 30px;
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

    <main>
        <h1>Consulta de expedientes</h1>

        <div class="search-section">
            <input type="text" class="search-input" placeholder="Buscar">
            <button class="search-btn">Buscar</button>
        </div>

        <div class="table-scroll-container">
            <table>
                <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
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

        function formatearFecha(fecha) {
            if (!fecha) return 'DD/MM/AA';
            const date = new Date(fecha);
            return date.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        function renderExpedientes(expedientes) {
            const tbody = document.getElementById('expedientesTableBody');
            
            if (expedientes.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="loading">No hay expedientes</td></tr>';
                return;
            }

            tbody.innerHTML = expedientes.map(exp => `
                <tr>
                    <td><button class="btn-ver" data-id="${exp.id}">Ver</button></td>
                    <td>${exp.id}</td>
                    <td><span class="badge ${exp.estado === 'Activo' ? 'activo' : ''}">${exp.estado}</span></td>
                    <td>Demandado</td>
                    <td>${exp.cantidad_participes}</td>
                    <td>${formatearFecha(exp.fecha_actualizacion)}</td>
                    <td><button class="btn-seguir">Seguir trámite</button></td>
                </tr>
            `).join('');

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
                btn.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const id = row.querySelector('td:nth-child(2)').textContent;
                    console.log('Seguir trámite:', id);
                });
            });
        }

        function renderModalContent(data) {
            let html = `
                <div class="modal-section">
                    <h3>Información del caso</h3>
                    <div class="modal-field">
                        <label>Número de expediente:</label>
                        <value>${data.numero || 'N/A'}</value>
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
                        <value>${data.cantidad_participes || 0}</value>
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

            if (data.participes && data.participes.length > 0) {
                html += `
                    <div class="modal-section">
                        <h3>Partícipes</h3>
                        <table class="participes-table">
                            <thead>
                                <tr>
                                    <th>Nombres Apellidos</th>
                                    <th>Condición</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                data.participes.forEach(p => {
                    const nombres = p.participe?.nombres || p.nombres || 'N/A';
                    const condicion = p.condicion || 'N/A';
                    html += `
                        <tr>
                            <td>${nombres}</td>
                            <td>${condicion}</td>
                        </tr>
                    `;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                `;
            }

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