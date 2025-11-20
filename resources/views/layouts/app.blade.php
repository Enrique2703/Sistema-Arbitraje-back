<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red Nacional de Arbitraje</title>

    <!-- Agregar Tailwind (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="icon" href="{{ asset('/img/logo2.png') }}" type="image/png">
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

        .hamburger-button {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 100;
            background: #525252;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }

        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            min-height: 100vh;
            overflow: hidden;
        }

        .sidebar {
            width: 250px;
            background-color: #525252;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: sticky;
            top: 0;
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                transform: translateX(-100%);
                z-index: 50;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .hamburger-button {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding-top: 80px;
            }
        }

        .sidebar-logo {
            margin-bottom: 40px;
            flex-shrink: 0;
        }

        .sidebar-logo img {
            max-width: 180px;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            margin: 0;
            padding: 0;
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
            height: 100vh;
            overflow-y: auto;
        }

        /* MODAL MODERNO DE LOGOUT */
        #logoutModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.2s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        #logoutModal .modal-content {
            background: white;
            border-radius: 15px;
            padding: 40px 30px 30px 30px;
            text-align: center;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        #logoutModal .modal-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px auto;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            background: #fff3e0;
            color: #ff9800;
            animation: popIn 0.5s ease-out;
        }

        @keyframes popIn {
            0% {
                transform: scale(0);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        #logoutModal .btn-modal {
            padding: 10px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 15px;
            transition: background 0.3s;
        }

        #logoutModal .btn-cancel {
            background: #f5f5f5;
            color: #333;
        }

        #logoutModal .btn-cancel:hover {
            background: #e0e0e0;
        }

        #logoutModal .btn-confirm {
            background: #f44336;
            color: white;
        }

        #logoutModal .btn-confirm:hover {
            background: #da190b;
        }
    </style>
    @yield('styles')
</head>

<body>
    <!-- Botón Hamburguesa -->
    <button class="hamburger-button" onclick="toggleSidebar()">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
        </svg>
    </button>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <br>
            <img src="/img/logo2.png" alt="Red Nacional de Arbitraje">
        </div>
        <ul class="sidebar-menu">
            <li id="menu-expedientes"><a href="/expedientes">Expedientes</a></li>
            <li id="menu-participes"><a href="/participes">Partícipes</a></li>
            <li id="menu-usuarios"><a href="/usuarios">Usuarios</a></li>
            <li id="menu-auditoria"><a href="/auditoria">Auditoría</a></li>
            <li id="menu-calculadora"><a href="/calculadora">Calculadora</a></li>
            <li id="menu-solicitudes"><a href="/solicitudes">Solicitudes</a></li>
            <li><a href="#" onclick="showLogoutModal()">Salir</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Logout Modal Moderno -->
    <div id="logoutModal">
        <div class="modal-content">
            <div class="modal-icon">!</div>
            <h2 style="font-size: 22px; color: #333; margin-bottom: 10px;">Cerrar Sesión</h2>
            <p style="color: #666; margin-bottom: 30px; font-size: 14px;">¿Está seguro de que desea cerrar sesión?</p>
            <div class="modal-actions">
                <button class="btn-modal btn-cancel" onclick="hideLogoutModal()">Cancelar</button>
                <button class="btn-modal btn-confirm" onclick="logout()">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        // Función para alternar la visibilidad del sidebar
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }

        // Cerrar el sidebar al hacer clic en un enlace (en pantallas pequeñas)
        document.querySelectorAll('.sidebar-menu a').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    document.querySelector('.sidebar').classList.remove('active');
                }
            });
        });

        // Cerrar el sidebar al hacer clic fuera de él (en pantallas pequeñas)
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                const sidebar = document.querySelector('.sidebar');
                const hamburgerButton = document.querySelector('.hamburger-button');
                if (!sidebar.contains(e.target) && !hamburgerButton.contains(e.target) && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            }
        });

        function showLogoutModal() {
            document.getElementById('logoutModal').style.display = 'flex';
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const usuario = JSON.parse(localStorage.getItem('usuario')) || JSON.parse(sessionStorage.getItem('usuario'));

            if (!usuario) return;

            const tipo = usuario.tipo_usuario;

            if (tipo === 'staff') {
                document.getElementById('menu-usuarios').style.display = 'none';
                document.getElementById('menu-auditoria').style.display = 'none';
                document.getElementById('menu-calculadora').style.display = 'none';
                document.getElementById('menu-solicitudes').style.display = 'none';
            }

        });
    </script>

</body>

</html>