<!-- Modal Overlay para Crear -->
<div id="createParticipeModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="p-4 w-full flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-screen overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Nuevo partícipe</h3>
                <button onclick="closeCreateParticipeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <form id="createParticipeForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nombres</label>
                        <input type="text" name="nombres" required required placeholder="Ingrese su nombre"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required required placeholder="Ingrese un email"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                        <input type="password" name="password" required required placeholder="Ingrese una contraseña de 8 digitos"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            <select name="estado" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCreateParticipeModal()"
                            class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Cancelar</button>
                        <button type="submit"
                            class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Abrir modal de partícipe
    function openCreateParticipeModal() {
        const modal = document.getElementById('createParticipeModalOverlay');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    // Cerrar modal de partícipe
    function closeCreateParticipeModal() {
        const modal = document.getElementById('createParticipeModalOverlay');
        if (modal) {
            modal.classList.add('hidden');
            const form = document.getElementById('createParticipeForm');
            if (form) form.reset();
        }
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('createParticipeModalOverlay')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCreateParticipeModal();
        }
    });

    // Manejar envío del formulario
    const participeForm = document.getElementById('createParticipeForm');
    if (participeForm) {
        participeForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const formData = new FormData(this);

            const data = {
                nombres: formData.get('nombres'),
                email: formData.get('email'),
                password: formData.get('password'),
                estado: formData.get('estado')
            };

        try {
            const response = await fetch('/api/participes', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                // Mostrar el mensaje que viene del backend si existe, si no, mensaje por defecto
                alert(result.mensaje || 'Partícipe creado correctamente');
                closeCreateParticipeModal();
                // Recargar la lista de partícipes
                if (typeof cargarParticipes === 'function') {
                    await cargarParticipes();
                }
                // Recargar lista de partícipes en expediente si existe
                if (typeof loadSelectsExpediente === 'function') {
                    await loadSelectsExpediente();
                }
            } else {
                alert('Error: ' + (result.message || result.mensaje || 'No se pudo crear el partícipe'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Email ya registrado, por favor use otro email');
        }
        });
    }
</script>
