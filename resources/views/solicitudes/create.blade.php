<!-- Tom Select CSS y JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<!-- Modal Overlay para Crear Expediente -->
<div id="createModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
                <h3 class="text-lg font-semibold text-gray-900">Nuevo expediente</h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Formulario -->
            <div class="px-6 py-4">
                <form id="createExpedienteForm">
                    <!-- Número, Año, Código -->
                    <div class="grid grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Número</label>
                            <input type="text" name="numero" required placeholder="Placeholder"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Año</label>
                            <input type="number" name="anio" required placeholder="Placeholder"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Código</label>
                            <select name="codigo" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="" disabled selected>Código</option>
                                <option value="CA-RENA">CA-RENA</option>
                                <option value="JPRD-RENA">JPRD-RENA</option>
                                <option value="DESIGNACION">DESIGNACIÓN</option>
                                <option value="RECUSACION">RECUSACIÓN</option>
                                <option value="INSTALACION">INSTALACIÓN</option>
                                <option value="AD HOC-RENA">AD HOC-RENA</option>
                                <option value="AE-RENA">AE-RENA</option>
                            </select>
                        </div>
                    </div>

                    <!-- Etapa procesal, Inicio del proceso -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Etapa procesal</label>
                            <select name="etapa_procesal"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="">Seleccione una etapa...</option>
                                <option value="Prearbitral">Prearbitral</option>
                                <option value="Instalación">Instalación</option>
                                <option value="Postulatoria">Postulatoria</option>
                                <option value="Fijación de puntos controvertidos">Fijación de puntos controvertidos</option>
                                <option value="Admisión de medios probatorios">Admisión de medios probatorios</option>
                                <option value="Actuación pericial">Actuación pericial</option>
                                <option value="Actuación probatoria">Actuación probatoria</option>
                                <option value="Cierre de etapa probatoria">Cierre de etapa probatoria</option>
                                <option value="Alegatos">Alegatos</option>
                                <option value="Audiencia">Audiencia</option>
                                <option value="Plazo para laudar">Plazo para laudar</option>
                                <option value="Plazo para resolver pedido contra Laudo Arbitral">Plazo para resolver pedido contra Laudo Arbitral</option>
                                <option value="Concluido con Laudo Arbitral Consentido">Concluido con Laudo Arbitral Consentido</option>
                                <option value="Concluido con pedido contra Laudo Arbitral">Concluido con pedido contra Laudo Arbitral</option>
                                <option value="Recusación de árbitro">Recusación de árbitro</option>
                                <option value="Recusación de adjudicador">Recusación de adjudicador</option>
                                <option value="Reconstitución de Tribunal Arbitral">Reconstitución de Tribunal Arbitral</option>
                                <option value="Reconstitución de JPRD">Reconstitución de JPRD</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">Inicio del proceso</label>
                            <input type="date" name="inicio_proceso" placeholder="DD/MM/AA"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- Tipo de proceso -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-900 mb-2">Tipo de proceso</label>
                        <select name="tipo_proceso"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">Seleccione un tipo...</option>
                            <option value="Arbitraje de Emergencia">Arbitraje de Emergencia</option>
                            <option value="Arbitraje Ad Hoc">Arbitraje Ad Hoc</option>
                            <option value="Arbitraje Institucional">Arbitraje Institucional</option>
                            <option value="Recusación de árbitro">Recusación de árbitro</option>
                            <option value="Recusación de adjudicador">Recusación de adjudicador</option>
                            <option value="Designación residual">Designación residual</option>
                            <option value="Instalación de arbitraje">Instalación de arbitraje</option>
                            <option value="Junta Consultiva de Disputas">Junta Consultiva de Disputas</option>
                            <option value="Junta Decisoria de Disputas">Junta Decisoria de Disputas</option>
                        </select>
                    </div>

                    <!-- Estado -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-900 mb-2">Estado</label>
                        <select name="estado"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">Seleccionar estado</option>
                            <option value="Archivado">Archivado</option>
                            <option value="En trámite">En trámite</option>
                            <option value="Suspendido">Suspendido</option>
                            <option value="Concluido">Concluido</option>
                        </select>
                    </div>

                    <!-- Árbitro -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-900">Árbitro</label>
                            <div class="flex gap-3">
                                <button type="button" onclick="openCreateUserModal()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Crear usuario
                                </button>
                                <button type="button" onclick="addArbitro()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Agregar usuario
                                </button>
                            </div>
                        </div>
                        <div id="arbitrosContainer">
                            <div class="flex items-center gap-2 mb-2">
                                <button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                                <select name="arbitros[]"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">Seleccionar usuario</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Adjudicador -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-900">Adjudicador</label>
                            <div class="flex gap-3">
                                <button type="button" onclick="openCreateUserModal()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Crear usuario
                                </button>
                                <button type="button" onclick="addAdjudicador()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Agregar usuario
                                </button>
                            </div>
                        </div>
                        <div id="adjudicadoresContainer">
                            <div class="flex items-center gap-2 mb-2">
                                <button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                                <select name="adjutadores[]"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">Seleccionar usuario</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Secretario Técnico -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-900">Secretario Técnico</label>
                            <div class="flex gap-3">
                                <button type="button" onclick="openCreateUserModal()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Crear usuario
                                </button>
                                <button type="button" onclick="addSecretarioTecnico()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Agregar usuario
                                </button>
                            </div>
                        </div>
                        <div id="secretariosTecnicosContainer">
                            <div class="flex items-center gap-2 mb-2">
                                <button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                                <select name="secretarios_tecnicos[]"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">Seleccionar usuario</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Partícipes -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-900">Partícipes</label>
                            <div class="flex gap-3">
                                <button type="button" onclick="openCreateParticipeModal()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Crear partícipe
                                </button>
                                <button type="button" onclick="addParticipe()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Agregar partícipe
                                </button>
                            </div>
                        </div>
                        <div id="participesContainer">
                            <div class="flex items-center gap-2 mb-2">
                                <button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                                <select name="participes_id[]"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">Seleccionar partícipe</option>
                                </select>
                                <select name="participes_condicion[]"
                                    class="w-40 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                    <option value="">Condición</option>
                                    <option value="Demandante">Demandante</option>
                                    <option value="Demandado">Demandado</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Fecha de Laudo Arbitral -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-900">Fecha de Laudo Arbitral</label>
                            <button type="button" onclick="addFechaLaudo()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                Agregar fecha
                            </button>
                        </div>
                        <div id="fechasLaudoContainer">
                            <div class="flex items-center gap-2 mb-2">
                                <button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                                <input type="date" name="fecha_laudo" placeholder="DD/MM/AA"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Fecha de Resolución -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-900">Fecha de Resolución:</label>
                            <button type="button" onclick="addFechaResolucion()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                Agregar fecha
                            </button>
                        </div>
                        <div id="fechasResolucionContainer">
                            <div class="flex items-center gap-2 mb-2">
                                <button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                                <input type="date" name="fecha_resolucion" placeholder="DD/MM/AA"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="grid grid-cols-2 gap-3 pt-4">
                        <button type="button" onclick="closeCreateModal()"
                            class="px-4 py-2.5 bg-gray-200 text-gray-900 rounded-md hover:bg-gray-300 font-medium">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2.5 bg-black text-white rounded-md hover:bg-gray-800 font-medium">
                            Crear
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Abrir modal
    function openCreateModal() {
        document.getElementById('createModalOverlay').classList.remove('hidden');
        loadSelectsExpediente();
    }
    window.openCreateModal = openCreateModal;

    // Cerrar modal
    function closeCreateModal() {
        document.getElementById('createModalOverlay').classList.add('hidden');
        document.getElementById('createExpedienteForm').reset();
    }

    // Cargar selects
    async function loadSelectsExpediente() {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');

        try {
            const [usuariosRes, clientesRes] = await Promise.all([
                fetch('/api/usuarios', {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                }),
                fetch('/api/clientes', {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                })
            ]);

            const usuariosJson = await usuariosRes.json();
            const clientesJson = await clientesRes.json();

            // soportar respuesta paginada { registros, meta } o array directo
            const usuarios = Array.isArray(usuariosJson) ? usuariosJson : (usuariosJson.registros || usuariosJson.data || []);
            const clientes = Array.isArray(clientesJson) ? clientesJson : (clientesJson.registros || clientesJson.data || []);

            // Guardar en caché global para reutilizar al clonar campos
            window._expedienteUsuarios = usuarios;
            window._expedienteClientes = clientes;

            function usuariosOptionsHtml() {
                return usuarios.map(u => `<option value="">Seleccionar usuario</option><option value="${u.id}">${u.id} - ${u.nombres}</option>`).join('');
            }

            function clientesOptionsHtml() {
                return clientes.map(c => `<option value="">Seleccionar cliente</option><option value="${c.id}">${c.id} - ${c.nombre}</option>`).join('');
            }
            // Opciones de usuarios
            const usuariosOptions = usuarios.map(u => `<option value="${u.id}">${u.id} - ${u.nombres}</option>`).join('');

            // Actualizar todos los selects de usuarios
            document.querySelectorAll('select[name="arbitros[]"]').forEach(select => {
                select.innerHTML = '<option value="">Seleccionar usuario</option>' + usuariosOptions;
            });

            document.querySelectorAll('select[name="adjutadores[]"]').forEach(select => {
                select.innerHTML = '<option value="">Seleccionar usuario</option>' + usuariosOptions;
            });

            document.querySelectorAll('select[name="secretarios_tecnicos[]"]').forEach(select => {
                select.innerHTML = '<option value="">Seleccionar usuario</option>' + usuariosOptions;
            });

            // Opciones de clientes
            const clientesOptions = clientes.map(c => `<option value="${c.id}">${c.id} - ${c.nombre}</option>`).join('');

            document.querySelectorAll('select[name="participes_id[]"]').forEach(select => {
                select.innerHTML = '<option value="">Seleccionar cliente</option>' + clientesOptions;
            });

            // Helper para rellenar selects cuando clonamos nodos dinámicos
            window.fillExpedienteSelects = function(root) {
                root.querySelectorAll('select[name="arbitros[]"]').forEach(select => {
                    select.innerHTML = '<option value="">Seleccionar usuario</option>' + usuariosOptions;
                });
                root.querySelectorAll('select[name="adjutadores[]"]').forEach(select => {
                    select.innerHTML = '<option value="">Seleccionar usuario</option>' + usuariosOptions;
                });
                root.querySelectorAll('select[name="secretarios_tecnicos[]"]').forEach(select => {
                    select.innerHTML = '<option value="">Seleccionar usuario</option>' + usuariosOptions;
                });
                root.querySelectorAll('select[name="participes_id[]"]').forEach(select => {
                    select.innerHTML = '<option value="">Seleccionar cliente</option>' + clientesOptions;
                });
            };

        } catch (error) {
            console.error('Error cargando datos:', error);
        }
    }

    // Funciones para agregar campos dinámicos
    function addArbitro() {
        const container = document.getElementById('arbitrosContainer');
        const newField = container.children[0].cloneNode(true);
        newField.querySelector('select').value = '';
        if (window.fillExpedienteSelects) window.fillExpedienteSelects(newField);
        container.appendChild(newField);
    }

    function addAdjudicador() {
        const container = document.getElementById('adjudicadoresContainer');
        const newField = container.children[0].cloneNode(true);
        newField.querySelector('select').value = '';
        if (window.fillExpedienteSelects) window.fillExpedienteSelects(newField);
        container.appendChild(newField);
    }

    function addSecretarioTecnico() {
        const container = document.getElementById('secretariosTecnicosContainer');
        const newField = container.children[0].cloneNode(true);
        newField.querySelector('select').value = '';
        if (window.fillExpedienteSelects) window.fillExpedienteSelects(newField);
        container.appendChild(newField);
    }

    function addParticipe() {
        const container = document.getElementById('participesContainer');
        const newField = container.children[0].cloneNode(true);
        newField.querySelectorAll('select').forEach(s => s.value = '');
        if (window.fillExpedienteSelects) window.fillExpedienteSelects(newField);
        container.appendChild(newField);
    }

    function addFechaLaudo() {
        const container = document.getElementById('fechasLaudoContainer');
        const newField = container.children[0].cloneNode(true);
        newField.querySelector('input').value = '';
        container.appendChild(newField);
    }

    function addFechaResolucion() {
        const container = document.getElementById('fechasResolucionContainer');
        const newField = container.children[0].cloneNode(true);
        newField.querySelector('input').value = '';
        container.appendChild(newField);
    }

    // Funciones para modales de crear usuario/partícipe
    function openCreateUserModal() {
        const modal = document.getElementById('createUserModalOverlay');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function closeCreateUserModal() {
        const modal = document.getElementById('createUserModalOverlay');
        if (modal) {
            modal.classList.add('hidden');
            const form = document.getElementById('createUserForm');
            if (form) form.reset();
        }
    }

    function openCreateParticipeModal() {
        const modal = document.getElementById('createParticipeModalOverlay');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    // Submit del formulario
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('createExpedienteForm');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            
            // Obtener el ID de la solicitud guardado globalmente
            const idSolicitud = window.idSolicitud || window.solicitudPendienteAceptar;

            // Recopilar datos del formulario
            const formData = new FormData(form);

            // Construir objeto de datos
            const data = {
                usuario_id: 1, // Ajustar según tu lógica
                nombre: formData.get('numero') + '/' + formData.get('anio'), // O como desees construirlo
                numero: formData.get('numero'),
                anio: parseInt(formData.get('anio')),
                codigo: formData.get('codigo'),
                etapa_procesal: formData.get('etapa_procesal'),
                estado: formData.get('estado'),
                inicio_proceso: formData.get('inicio_proceso'),
                tipo_proceso: formData.get('tipo_proceso'),
                arbitros: formData.getAll('arbitros[]').filter(v => v),
                adjutadores: formData.getAll('adjutadores[]').filter(v => v),
                secretarios_tecnicos: formData.getAll('secretarios_tecnicos[]').filter(v => v),
                fecha_laudo: formData.get('fecha_laudo'),
                fecha_resolucion: formData.get('fecha_resolucion')
            };

            // Construir partícipes con condición
            const participesIds = formData.getAll('participes_id[]').filter(v => v);
            const participesCondiciones = formData.getAll('participes_condicion[]');

            data.participes = participesIds.map((id, index) => ({
                id: parseInt(id),
                condicion: participesCondiciones[index] || null
            }));

            try {

                const res = await fetch(`/api/solicitudes/${idSolicitud}/estado`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        estado: 'Aceptado'
                    })
                });

                if (!res.ok) {
                    alert('No se pudo actualizar el estado');
                    return;
                }

                const response = await fetch('/api/expedientes', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Expediente creado correctamente');
                    closeCreateModal();
                    // Recargar la página completa
                    window.location.reload();
                } else {
                    alert('Error: ' + (result.message || 'No se pudo crear el expediente'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al crear el expediente');
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        await cargarUsuarios(); // carga inicial
    });

    async function cargarUsuarios() {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const res = await fetch('/api/usuarios', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error('Error al obtener usuarios');

            const data = await res.json();
            const usuarios = data.registros || [];

            // Inicializar Tom Select en los selects
            inicializarTomSelect(document.querySelectorAll('select[name="arbitros[]"]'), usuarios);
            inicializarTomSelect(document.querySelectorAll('select[name="adjutadores[]"]'), usuarios);
            inicializarTomSelect(document.querySelectorAll('select[name="secretarios_tecnicos[]"]'), usuarios);

            // Guardar los usuarios globalmente por si se agregan nuevos selects
            window.listaUsuarios = usuarios;
        } catch (error) {
            console.error('Error cargando usuarios:', error);
        }
    }

    function inicializarTomSelect(selects, usuarios) {
        selects.forEach(select => {
            // Destruir instancia anterior si existe
            if (select.tomselect) {
                select.tomselect.destroy();
            }

            new TomSelect(select, {
                valueField: 'id',
                labelField: 'nombres',
                searchField: ['nombres', 'email'],
                options: usuarios,
                create: false,
                placeholder: 'Buscar usuario...',
                render: {
                    option: function(item, escape) {
                        return `<div class="py-2 px-3">
                            <div class="font-medium">${escape(item.nombres)}</div>
                            <div class="text-sm text-gray-600">${escape(item.email || '')}</div>
                        </div>`;
                    },
                    item: function(item, escape) {
                        return `<div>${escape(item.nombres)}</div>`;
                    }
                },
                loadingClass: 'loading',
                load: async function(query, callback) {
                    try {
                        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                        const response = await fetch(`/api/usuarios?search=${encodeURIComponent(query)}`, {
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        });
                        const json = await response.json();
                        callback(json.registros || []);
                    } catch (e) {
                        console.error('Error cargando usuarios:', e);
                        callback();
                    }
                }
            });
        });
    }

    function llenarSelectUsuarios(selects, usuarios) {
        selects.forEach(select => {
            const selectedValue = select.value;
            select.innerHTML = `<option value="">Seleccionar usuario</option>`;
            usuarios.forEach(u => {
                const nombreCompleto = `${u.nombres ?? ''} ${u.apellidos ?? ''}`.trim();
                const option = document.createElement('option');
                option.value = u.id;
                option.textContent = nombreCompleto || 'Usuario sin nombre';
                if (u.id == selectedValue) option.selected = true;
                select.appendChild(option);
            });
        });
    }

    // Funciones para agregar nuevos selects dinámicamente
    function addArbitro() {
        const container = document.getElementById('arbitrosContainer');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 mb-2';
        div.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" 
            class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        <select name="arbitros[]" 
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
            <option value="">Seleccionar usuario</option>
        </select>
    `;
        container.appendChild(div);
        if (window.listaUsuarios) {
            inicializarTomSelect([div.querySelector('select')], window.listaUsuarios);
        }
    }

    function addAdjudicador() {
        const container = document.getElementById('adjudicadoresContainer');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 mb-2';
        div.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" 
            class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        <select name="adjutadores[]" 
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
            <option value="">Seleccionar usuario</option>
        </select>
    `;
        container.appendChild(div);
        if (window.listaUsuarios) {
            inicializarTomSelect([div.querySelector('select')], window.listaUsuarios);
        }
    }

    function addSecretarioTecnico() {
        const container = document.getElementById('secretariosTecnicosContainer');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 mb-2';
        div.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" 
            class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        <select name="secretarios_tecnicos[]" 
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
            <option value="">Seleccionar usuario</option>
        </select>
    `;
        container.appendChild(div);
        if (window.listaUsuarios) {
            inicializarTomSelect([div.querySelector('select')], window.listaUsuarios);
        }
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await cargarParticipes(); // carga inicial
    });

    async function cargarParticipes() {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const res = await fetch('/api/participes', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error('Error al obtener partícipes');

            const data = await res.json();
            const participes = data.registros || [];

            // Llenar todos los select existentes
            llenarSelectParticipes(document.querySelectorAll('select[name="participes_id[]"]'), participes);

            // Guardar los partícipes globalmente para futuros selects dinámicos
            window.listaParticipes = participes;
        } catch (error) {
            console.error('Error cargando partícipes:', error);
        }
    }

    function llenarSelectParticipes(selects, participes) {
        selects.forEach(select => {
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
        });
    }

    // 🔹 Si agregas partícipes dinámicamente desde un botón:
    function addParticipe() {
        const container = document.getElementById('participesContainer');
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 mb-2';
        div.innerHTML = `
            <button type=\"button\" onclick=\"this.parentElement.remove()\" class=\"w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0\">−</button>
            <select name=\"participes_id[]\" class=\"flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white\">
                <option value=\"\">Seleccionar partícipe</option>
            </select>
            <select name=\"participes_condicion[]\" class=\"w-40 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white\">
                <option value=\"\">Condición</option>
                <option value=\"Demandante\">Demandante</option>
                <option value=\"Demandado\">Demandado</option>
            </select>
        `;
        container.appendChild(div);
        const select = div.querySelector('select[name="participes_id[]"]');
        if (window.listaParticipes) llenarSelectParticipes([select], window.listaParticipes);
        // Integrar TomSelect para buscador
        if (window.TomSelect) {
            if (select.tomselect) select.tomselect.destroy();
            new TomSelect(select, {
                valueField: 'id',
                labelField: 'nombres',
                searchField: ['nombres', 'apellidos', 'email'],
                options: window.listaParticipes || [],
                create: false,
                placeholder: 'Buscar partícipe...',
                render: {
                    option: function(item, escape) {
                        return `<div class=\"py-2 px-3\"><div class=\"font-medium\">${escape(item.nombres ?? '')} ${escape(item.apellidos ?? '')}</div><div class=\"text-sm text-gray-600\">${escape(item.email || '')}</div></div>`;
                    },
                    item: function(item, escape) {
                        return `<div>${escape(item.nombres ?? '')} ${escape(item.apellidos ?? '')}</div>`;
                    }
                },
                loadingClass: 'loading',
                load: async function(query, callback) {
                    try {
                        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                        const response = await fetch(`/api/participes?search=${encodeURIComponent(query)}`, {
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        });
                        const json = await response.json();
                        callback(json.registros || []);
                    } catch (e) {
                        console.error('Error cargando partícipes:', e);
                        callback();
                    }
                }
            });
        }
    }
</script>

<!-- Incluir los modales -->
@include('usuarios.create')
@include('participes.create')

<style>
    /* Estilos adicionales para mejorar la apariencia */
    #createModalOverlay select,
    #createModalOverlay input[type="text"],
    #createModalOverlay input[type="number"],
    #createModalOverlay input[type="date"] {
        font-size: 14px;
    }

    #createModalOverlay input::placeholder {
        color: #9CA3AF;
    }

    /* Ocultar el icono de calendario por defecto si quieres un estilo personalizado */
    #createModalOverlay input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
    }
</style>