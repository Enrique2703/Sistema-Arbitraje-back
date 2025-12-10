<!-- Modal de Edición -->
<div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center">
    <div class="p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Editar Solicitud</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editForm" onsubmit="updateSolicitud(event)">
            <input type="hidden" id="edit_id" name="id">
            
            <div class="mb-4">
                <label for="edit_tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Solicitud</label>
                <select id="edit_tipo" name="tipo" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Seleccione un tipo</option>
                    <option value="Arbitraje">Arbitraje</option>
                    <option value="Mediación">Mediación</option>
                    <option value="Conciliación">Conciliación</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="edit_descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="edit_descripcion" name="descripcion" required rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Describa brevemente su solicitud"></textarea>
            </div>

            <div class="mb-4">
                <label for="edit_estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select id="edit_estado" name="estado" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="Pendiente">Pendiente</option>
                    <option value="En revisión">En revisión</option>
                    <option value="Aprobada">Aprobada</option>
                    <option value="Rechazada">Rechazada</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="edit_documentos" class="block text-sm font-medium text-gray-700 mb-1">Agregar Documentos</label>
                <input type="file" id="edit_documentos" name="documentos[]" multiple
                    class="w-full px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">Cancelar</button>
                <button type="submit"
                    class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<script>
    async function openEditModal(id) {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const response = await fetch(`/api/solicitudes/${id}`, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) throw new Error('Error al cargar la solicitud');
            const solicitud = await response.json();

            document.getElementById('edit_id').value = solicitud.id;
            document.getElementById('edit_tipo').value = solicitud.tipo;
            document.getElementById('edit_descripcion').value = solicitud.descripcion;
            document.getElementById('edit_estado').value = solicitud.estado;

            document.getElementById('editModal').classList.remove('hidden');
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar los datos de la solicitud');
        }
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editForm').reset();
    }

    async function updateSolicitud(event) {
        event.preventDefault();
        const id = document.getElementById('edit_id').value;
        const formData = new FormData(event.target);

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const response = await fetch(`/api/solicitudes/${id}`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });

            if (!response.ok) throw new Error('Error al actualizar la solicitud');

            closeEditModal();
            loadSolicitudes(currentPage);
            alert('Solicitud actualizada correctamente');
        } catch (error) {
            console.error('Error:', error);
            alert('Error al actualizar la solicitud');
        }
    }
</script>