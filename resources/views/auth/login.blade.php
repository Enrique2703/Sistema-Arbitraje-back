<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red Nacional de Arbitraje - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
        }

        /* Layout dos columnas con proporciones 70/30 */
        .left-panel {
            flex: 7;
            /* 70% */
            background: url('/img/login.png') no-repeat center center;
            background-size: cover;
        }

        .right-panel {
            flex: 3;
            /* 30% */
            display: flex;
            justify-content: center;
            align-items: center;
            background: #fff;
            padding: 40px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container img {
            max-width: 220px;
            height: auto;
        }

        h1 {
            text-align: center;
            font-size: 24px;
            color: #2D3748;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 500;
            color: #2D3748;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 14px;
            background-color: #F7FAFC;
        }

        .form-control:focus {
            border-color: #4299E1;
            outline: none;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #4A5568;
            font-size: 16px;
        }

        .forgot-password {
            display: block;
            text-align: right;
            margin-top: 6px;
            font-size: 12px;
            color: #4299E1;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
            color: #3182CE;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background-color: #4299E1;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: #3182CE;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(66, 153, 225, 0.2);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .modal-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .modal-success .modal-icon {
            color: #28a745;
        }

        .modal-error .modal-icon {
            color: #dc3545;
        }

        .btn-accept {
            background: #4299E1;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-accept:hover {
            background: #3182CE;
        }

        .label-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* 🔑 centra verticalmente */
            margin-bottom: 8px;
        }

        .label-row label {
            font-size: 14px;
            font-weight: 500;
            color: #2D3748;
            margin: 0;
            /* 🔑 quita espacio extra */
            display: inline;
            /* 🔑 evita que sea block */
        }

        .forgot-password {
            font-size: 12px;
            color: #4299E1;
            text-decoration: none;
            margin-left: 10px;
        }

        .forgot-password:hover {
            text-decoration: underline;
            color: #3182CE;
        }
    </style>
</head>

<body>
    <!-- Columna izquierda -->
    <div class="left-panel"></div>

    <!-- Columna derecha -->
    <div class="right-panel">
        <div class="login-container">
            <div class="logo-container">
                <img src="/img/logo.png" alt="Red Nacional de Arbitraje">
            </div>
            <h1>Iniciar sesión</h1>

            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Ingresar Correo" required>
                </div>

                <div class="form-group">
                    <div class="label-row">
                        <label for="password">Contraseña</label>
                        <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
                    </div>
                    <div class="password-container">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Ingresar Contraseña" required>
                        <i class="fa fa-eye toggle-password" onclick="togglePassword()"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label><input type="checkbox" id="rememberMe"> Recordarme</label>
                </div>



                <button type="submit" class="btn-login">Iniciar sesión</button>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div id="messageModal" class="modal">
        <div class="modal-content" id="modalContent">
            <div class="modal-icon" id="modalIcon"></div>
            <h2 id="modalTitle"></h2>
            <h2 id="modalMessage"></h2>
            <br>
            <button class="btn-accept" onclick="closeModal()">Aceptar</button>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function showModal(title, message, type = 'success', redirectUrl = null) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;

            const modal = document.getElementById('messageModal');
            const modalContent = document.getElementById('modalContent');
            const modalIcon = document.getElementById('modalIcon');

            modalContent.classList.remove('modal-success', 'modal-error');
            if (type === 'success') {
                modalContent.classList.add('modal-success');
                modalIcon.innerHTML = '<i class="fa fa-check-circle"></i>';
            } else {
                modalContent.classList.add('modal-error');
                modalIcon.innerHTML = '<i class="fa fa-times-circle"></i>';
            }

            modal.style.display = 'flex';
            modal.dataset.redirect = redirectUrl || '';
        }

        function closeModal() {
            const modal = document.getElementById('messageModal');
            modal.style.display = 'none';
            const redirectUrl = modal.dataset.redirect;
            if (redirectUrl) window.location.href = redirectUrl;
        }

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json' // 👈 importante
                    },
                    body: JSON.stringify({
                        email: document.getElementById('email').value,
                        password: document.getElementById('password').value
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // ✅ Guarda token en localStorage o sessionStorage según 'Recordarme'
                    const storage = document.getElementById('rememberMe').checked ? localStorage : sessionStorage;
                    storage.setItem('token', data.token);
                    storage.setItem('usuario', JSON.stringify(data.usuario));

                    const tipoUsuario = data.usuario.tipo_usuario;
                    const ruta = tipoUsuario === 'participe' ? '/usuarios' : '/usuarios';

                    showModal('', data.message || 'Inicio de sesión exitoso', 'success', ruta);
                    
                } else {
                    showModal('', data.message || 'Error al iniciar sesión', 'error');
                }

            } catch (err) {
                showModal('Error', 'No se pudo conectar con el servidor', 'error');
            }
        });
    </script>
</body>

</html>