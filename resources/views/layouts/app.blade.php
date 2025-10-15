<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Red Nacional de Arbitraje</title>

    <!-- Agregar Tailwind (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap Icons opcional -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Tus estilos personalizados existentes */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #525252;
            color: white;
            padding: 20px;
        }

        .sidebar-logo {
            margin-bottom: 40px;
        }

        .sidebar-logo img {
            max-width: 180px;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 120px);
        }

        .sidebar-menu li:not(:last-child) {
            margin-bottom: 15px;
        }

        .sidebar-menu li:last-child {
            margin-top: auto;
        }

        .sidebar-menu a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: block;
            padding: 10px;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .main-content {
            flex: 1;
            padding: 30px;
            background-color: #F7FAFC;
        }

        /* Estilos del modal de logout */
        #logoutModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 90%;
            max-width: 400px;
        }

        .modal-actions {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-modal {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-confirm {
            background-color: #E53E3E;
            color: white;
        }

        .btn-cancel {
            background-color: #718096;
            color: white;
        }
    </style>
    @yield('styles')
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <br>
            <img src="/img/logo2.png" alt="Red Nacional de Arbitraje">
        </div>
        <ul class="sidebar-menu">
            <li><a href="/expedientes" class="{{ request()->is('expedientes*') ? 'active' : '' }}">Expedientes</a></li>
            <li><a href="/participes" class="{{ request()->is('participes*') ? 'active' : '' }}">Partícipes</a></li>
            <li><a href="/usuarios" class="{{ request()->is('usuarios*') ? 'active' : '' }}">Usuarios</a></li>
            <li><a href="/auditoria" class="{{ request()->is('auditoria*') ? 'active' : '' }}">Auditoría</a></li>
            <li><a href="/calculadora" class="{{ request()->is('calculadora*') ? 'active' : '' }}">Calculadora</a></li>
            <li><a href="/solicitudes" class="{{ request()->is('solicitudes*') ? 'active' : '' }}">Solicitudes</a></li>
            <li><a href="#" onclick="showLogoutModal()">Salir</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal">
        <div class="modal-content">
            <h2>Cerrar Sesión</h2>
            <br>
            <p>¿Está seguro de que desea cerrar sesión?</p>
            <div class="modal-actions">
                <button class="btn-modal btn-cancel" onclick="hideLogoutModal()">Cancelar</button>
                <button class="btn-modal btn-confirm" onclick="logout()">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'block';
        }

        function hideLogoutModal() {
            document.getElementById('logoutModal').style.display = 'none';
        }

        async function logout() {
            try {
                // Buscar token en localStorage o sessionStorage
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');

                // Si no hay token, igual limpiamos y redirigimos
                if (!token) {
                    localStorage.removeItem('token');
                    localStorage.removeItem('usuario');
                    sessionStorage.removeItem('token');
                    sessionStorage.removeItem('usuario');
                    window.location.href = '/login';
                    return;
                }

                const response = await fetch('/api/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });

                // Eliminar en ambos storages por seguridad
                localStorage.removeItem('token');
                localStorage.removeItem('usuario');
                sessionStorage.removeItem('token');
                sessionStorage.removeItem('usuario');

                if (response.ok) {
                    window.location.href = '/login';
                } else {
                    throw new Error('Error al cerrar sesión');
                }
            } catch (error) {
                console.error('Error:', error);
                // Limpieza de seguridad
                localStorage.removeItem('token');
                localStorage.removeItem('usuario');
                sessionStorage.removeItem('token');
                sessionStorage.removeItem('usuario');
                window.location.href = '/login';
            }
        }


        window.onclick = function(event) {
            const modal = document.getElementById('logoutModal');
            if (event.target === modal) hideLogoutModal();
        }
    </script>
    @yield('scripts')
</body>

</html>