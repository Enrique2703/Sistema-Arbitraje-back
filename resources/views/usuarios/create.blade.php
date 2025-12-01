<!-- Modal Overlay para Crear -->
<div id="createUserModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-screen overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Nuevo usuario</h3>
                <button onclick="closeCreateUserModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <form id="createUserForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombres</label>
                        <input type="text" name="nombres" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div id="error-nombres" class="text-red-500 text-xs mt-1"></div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div id="error-email" class="text-red-500 text-xs mt-1"></div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                        <input type="password" name="password" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <div id="error-password" class="text-red-500 text-xs mt-1"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nivel de usuario</label>
                            <select name="nivel_usuario" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="administrador">Administrador</option>
                                <option value="staff">Staff</option>
                                <option value="participe">Participe</option>
                            </select>
                            <div id="error-nivel_usuario" class="text-red-500 text-xs mt-1"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            <select name="estado" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                            <div id="error-estado" class="text-red-500 text-xs mt-1"></div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCreateUserModal()" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // 🟢 Abre el modal de usuario
    function openCreateUserModal() {
        const modal = document.getElementById('createUserModalOverlay');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    // 🔴 Cierra el modal de usuario
    function closeCreateUserModal() {
        const modal = document.getElementById('createUserModalOverlay');
        if (modal) {
            modal.classList.add('hidden');
            const form = document.getElementById('createUserForm');
            if (form) form.reset();
        }
    }

    // 🟡 Cierra el modal al hacer clic fuera del contenido
    const userModalOverlay = document.getElementById('createUserModalOverlay');
    if (userModalOverlay) {
        userModalOverlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeCreateUserModal();
            }
        });
    }

    // 🟣 Maneja el envío del formulario con manejo de errores
    document.getElementById('createUserForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Limpiar errores previos
        ['nombres','email','password','nivel_usuario','estado'].forEach(function(campo){
            document.getElementById('error-' + campo).textContent = '';
        });

        const form = e.target;
        const formData = new FormData(form);
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Creando...';

        try {
            const response = await fetch('/api/usuarios', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                if (errorData && errorData.errors) {
                    // Mostrar errores debajo de cada campo
                    Object.entries(errorData.errors).forEach(([campo, mensajes]) => {
                        const errorDiv = document.getElementById('error-' + campo);
                        if (errorDiv) errorDiv.textContent = mensajes.join(' ');
                    });
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Crear';
                    return; // No cerrar el modal
                } else if (errorData && errorData.mensaje) {
                    // Si el backend retorna un mensaje de error específico
                    alert(errorData.mensaje);
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Crear';
                    return;
                } else {
                    alert('Error al crear el usuario');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Crear';
                    return;
                }
            }

            // Éxito: mostrar mensaje y actualizar tabla
            alert('Usuario creado correctamente');
            closeCreateUserModal();
            if (typeof fetchUsuarios === 'function') {
                fetchUsuarios();
            }
            // Recargar lista de usuarios en expediente si existe
            if (typeof loadSelectsExpediente === 'function') {
                await loadSelectsExpediente();
            }
            if (typeof inicializarTomSelect === 'function') {
                const selects = document.querySelectorAll('select[name="arbitros[]"], select[name="adjutadores[]"], select[name="secretarios_tecnicos[]"]');
                inicializarTomSelect(selects, window.listaUsuarios || []);
            }
        } catch (error) {
            alert('Error de red o inesperado al crear el usuario');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Crear';
        }
    });
</script>
