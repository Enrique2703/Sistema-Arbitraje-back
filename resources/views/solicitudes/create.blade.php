<!-- Modal de Creación -->
<div id="createModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Nueva Solicitud</h3>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="createForm" onsubmit="createSolicitud(event)">
            <div class="mb-4">
                <label for="create_tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Solicitud</label>
                <select id="create_tipo" name="tipo" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Seleccione un tipo</option>
                    <option value="Arbitraje">Arbitraje</option>
                    <option value="Mediación">Mediación</option>
                    <option value="Conciliación">Conciliación</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="create_descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="create_descripcion" name="descripcion" required rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Describa brevemente su solicitud"></textarea>
            </div>

            <div class="mb-4">
                <label for="create_documentos" class="block text-sm font-medium text-gray-700 mb-1">Documentos</label>
                <input type="file" id="create_documentos" name="documentos[]" multiple
                    class="w-full px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCreateModal()"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">Cancelar</button>
                <button type="submit"
                    class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.getElementById('createForm').reset();
    }

    async function createSolicitud(event) {
        event.preventDefault();
        const formData = new FormData(event.target);

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const response = await fetch('/api/solicitudes', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error('Error al crear la solicitud');
            }

            closeCreateModal();
            loadSolicitudes(currentPage);
            alert('Solicitud creada correctamente');
        } catch (error) {
            console.error('Error:', error);
            alert('Error al crear la solicitud');
        }
    }
</script>