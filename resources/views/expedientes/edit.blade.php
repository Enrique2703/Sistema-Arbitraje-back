<!-- Modal Overlay para Editar Expediente -->
<div id="editModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
                <h3 class="text-lg font-semibold text-gray-900">Editar expediente</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Formulario -->
            <div class="px-6 py-4">
                <form id="editExpedienteForm">
                    <input type="hidden" name="expediente_id" value="">
                    <input type="hidden" name="usuario_id" value="">

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
                            <select name="codigo"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="">Placeholder</option>
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
                                <button type="button" onclick="openCreateUsuarioModal()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Crear usuario
                                </button>
                                <button type="button" onclick="editAddArbitro()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Agregar usuario
                                </button>
                            </div>
                        </div>
                        <div id="editArbitrosContainer"></div>
                    </div>

                    <!-- Adjudicador -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-900">Adjudicador</label>
                            <div class="flex gap-3">
                                <button type="button" onclick="openCreateUsuarioModal()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Crear usuario
                                </button>
                                <button type="button" onclick="editAddAdjudicador()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Agregar usuario
                                </button>
                            </div>
                        </div>
                        <div id="editAdjudicadoresContainer"></div>
                    </div>

                    <!-- Secretario Técnico -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-900">Secretario Técnico</label>
                            <div class="flex gap-3">
                                <button type="button" onclick="openCreateUsuarioModal()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Crear usuario
                                </button>
                                <button type="button" onclick="editAddSecretarioTecnico()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Agregar usuario
                                </button>
                            </div>
                        </div>
                        <div id="editSecretariosTecnicosContainer"></div>
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
                                <button type="button" onclick="editAddParticipe()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                    <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                    Agregar partícipe
                                </button>
                            </div>
                        </div>
                        <div id="editParticipesContainer"></div>
                    </div>

                    <!-- Fecha de Laudo Arbitral -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-900">Fecha de Laudo Arbitral</label>
                            <button type="button" onclick="editAddFechaLaudo()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                Agregar fecha
                            </button>
                        </div>
                        <div id="editFechasLaudoContainer"></div>
                    </div>

                    <!-- Fecha de Resolución -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-900">Fecha de Resolución:</label>
                            <button type="button" onclick="editAddFechaResolucion()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                <span class="w-5 h-5 border-2 border-gray-700 rounded-full flex items-center justify-center mr-1.5 text-lg leading-none">+</span>
                                Agregar fecha
                            </button>
                        </div>
                        <div id="editFechasResolucionContainer"></div>
                    </div>

                    <!-- Botones -->
                    <div class="grid grid-cols-2 gap-3 pt-4">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2.5 bg-gray-200 text-gray-900 rounded-md hover:bg-gray-300 font-medium">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2.5 bg-black text-white rounded-md hover:bg-gray-800 font-medium">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Caches de opciones
let editUsuariosOptions = '';
let editClientesOptions = '';

function openEditModal(id) {
    document.getElementById('editModalOverlay').classList.remove('hidden');
    loadEditSelects().then(() => loadExpedienteToForm(id));
}

function closeEditModal() {
    document.getElementById('editModalOverlay').classList.add('hidden');
    document.getElementById('editExpedienteForm').reset();
    // limpiar contenedores
    ['editArbitrosContainer','editAdjudicadoresContainer','editSecretariosTecnicosContainer','editParticipesContainer','editFechasLaudoContainer','editFechasResolucionContainer'].forEach(id => {
        document.getElementById(id).innerHTML = '';
    });
}

async function loadEditSelects() {
    const token = localStorage.getItem('token') || sessionStorage.getItem('token');

    try {
        const [usuariosRes, clientesRes] = await Promise.all([
            fetch('/api/usuarios', { headers: { 'Authorization': `Bearer ${token}` } }),
            fetch('/api/clientes', { headers: { 'Authorization': `Bearer ${token}` } })
        ]);

        const usuarios = await usuariosRes.json();
        const clientes = await clientesRes.json();

        editUsuariosOptions = '<option value="">Seleccionar usuario</option>' + usuarios.map(u => `<option value="${u.id}">${u.id} - ${u.nombres}</option>`).join('');
        editClientesOptions = '<option value="">Seleccionar cliente</option>' + clientes.map(c => `<option value="${c.id}">${c.id} - ${c.nombre}</option>`).join('');
    } catch (error) {
        console.error('Error cargando selects para editar:', error);
    }
}

async function loadExpedienteToForm(id) {
    const token = localStorage.getItem('token') || sessionStorage.getItem('token');
    try {
        const res = await fetch(`/api/expedientes/${id}`, { headers: { 'Authorization': `Bearer ${token}` } });
        const json = await res.json();
        if (!res.ok) throw new Error(json.message || 'No se pudo obtener expediente');

        const exp = json.data;

        const form = document.getElementById('editExpedienteForm');
        form.querySelector('input[name="expediente_id"]').value = exp.id;
        form.querySelector('input[name="usuario_id"]').value = exp.usuario?.id || '';
        form.querySelector('input[name="numero"]').value = exp.numero || '';
        form.querySelector('input[name="anio"]').value = exp.anio || '';
        form.querySelector('select[name="etapa_procesal"]').value = exp.etapa_procesal || '';
        form.querySelector('input[name="inicio_proceso"]').value = exp.inicio_proceso || '';
        form.querySelector('select[name="tipo_proceso"]').value = exp.tipo_proceso || '';
        form.querySelector('select[name="estado"]').value = exp.estado || '';

        // Árbitros
        const arbContainer = document.getElementById('editArbitrosContainer');
        arbContainer.innerHTML = '';
        if (exp.arbitros && exp.arbitros.length) {
            exp.arbitros.forEach(a => {
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 mb-2';
                row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                    <select name="arbitros[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">${editUsuariosOptions}</select>`;
                row.querySelector('select').value = a.usuario_id || a.usuario?.id || '';
                arbContainer.appendChild(row);
            });
        }

        // Adjutadores
        const adjContainer = document.getElementById('editAdjudicadoresContainer');
        adjContainer.innerHTML = '';
        if (exp.adjutadores && exp.adjutadores.length) {
            exp.adjutadores.forEach(a => {
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 mb-2';
                row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                    <select name="adjutadores[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">${editUsuariosOptions}</select>`;
                row.querySelector('select').value = a.usuario_id || a.usuario?.id || '';
                adjContainer.appendChild(row);
            });
        }

        // Secretarios técnicos
        const secTecContainer = document.getElementById('editSecretariosTecnicosContainer');
        secTecContainer.innerHTML = '';
        if (exp.secretarios_tecnicos && exp.secretarios_tecnicos.length) {
            exp.secretarios_tecnicos.forEach(s => {
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 mb-2';
                row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                    <select name="secretarios_tecnicos[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">${editUsuariosOptions}</select>`;
                row.querySelector('select').value = s.usuario_id || s.usuario?.id || '';
                secTecContainer.appendChild(row);
            });
        }

        // Partícipes
        const partsContainer = document.getElementById('editParticipesContainer');
        partsContainer.innerHTML = '';
        if (exp.participes && exp.participes.length) {
            exp.participes.forEach(p => {
                const row = document.createElement('div');
                row.className = 'flex items-center gap-2 mb-2';
                row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                    <select name="participes_id[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">${editClientesOptions}</select>
                    <select name="participes_condicion[]" class="w-40 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <option value="">Condición</option>
                        <option value="Demandante">Demandante</option>
                        <option value="Demandado">Demandado</option>
                    </select>`;
                row.querySelector('select[name="participes_id[]"]').value = p.participe_id || p.participe?.id || '';
                row.querySelector('select[name="participes_condicion[]"]').value = p.condicion || '';
                partsContainer.appendChild(row);
            });
        }

        // Fechas
        const fLaudoCont = document.getElementById('editFechasLaudoContainer');
        fLaudoCont.innerHTML = '';
        if (exp.fechaLaudo && exp.fechaLaudo.fecha) {
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 mb-2';
            row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                <input type="date" name="fecha_laudo" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="${exp.fechaLaudo.fecha}">`;
            fLaudoCont.appendChild(row);
        }

        const fResCont = document.getElementById('editFechasResolucionContainer');
        fResCont.innerHTML = '';
        if (exp.fechaResolucion && exp.fechaResolucion.fecha) {
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 mb-2';
            row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
                <input type="date" name="fecha_resolucion" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="${exp.fechaResolucion.fecha}">`;
            fResCont.appendChild(row);
        }

    } catch (error) {
        console.error('Error cargando expediente:', error);
        alert('No se pudo cargar el expediente para edición');
        closeEditModal();
    }
}

// Funciones para agregar campos dinámicos en edit
function editAddArbitro() {
    const container = document.getElementById('editArbitrosContainer');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 mb-2';
    row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        <select name="arbitros[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">${editUsuariosOptions}</select>`;
    container.appendChild(row);
}

function editAddAdjudicador() {
    const container = document.getElementById('editAdjudicadoresContainer');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 mb-2';
    row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        <select name="adjutadores[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">${editUsuariosOptions}</select>`;
    container.appendChild(row);
}

function editAddSecretarioTecnico() {
    const container = document.getElementById('editSecretariosTecnicosContainer');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 mb-2';
    row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        <select name="secretarios_tecnicos[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">${editUsuariosOptions}</select>`;
    container.appendChild(row);
}

function editAddParticipe() {
    const container = document.getElementById('editParticipesContainer');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 mb-2';
    row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        <select name="participes_id[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">${editClientesOptions}</select>
        <select name="participes_condicion[]" class="w-40 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
            <option value="">Condición</option>
            <option value="Demandante">Demandante</option>
            <option value="Demandado">Demandado</option>
        </select>`;
    container.appendChild(row);
}

function editAddFechaLaudo() {
    const container = document.getElementById('editFechasLaudoContainer');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 mb-2';
    row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        <input type="date" name="fecha_laudo" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">`;
    container.appendChild(row);
}

function editAddFechaResolucion() {
    const container = document.getElementById('editFechasResolucionContainer');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 mb-2';
    row.innerHTML = `<button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        <input type="date" name="fecha_resolucion" class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">`;
    container.appendChild(row);
}

// Placeholders para modales de crear usuario/partícipe
function openCreateUsuarioModal() { alert('Aquí se abriría el modal para crear usuario'); }
function openCreateParticipeModal() { alert('Aquí se abriría el modal para crear partícipe'); }

// Submit del formulario de edición
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editExpedienteForm');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        const formData = new FormData(form);
        const expedienteId = formData.get('expediente_id');

        const data = {
            usuario_id: formData.get('usuario_id') || 1,
            nombre: formData.get('numero') + '/' + formData.get('anio'),
            numero: formData.get('numero'),
            anio: parseInt(formData.get('anio')),
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

        const participesIds = formData.getAll('participes_id[]').filter(v => v);
        const participesCondiciones = formData.getAll('participes_condicion[]');
        data.participes = participesIds.map((id, index) => ({ id: parseInt(id), condicion: participesCondiciones[index] || null }));

        try {
            const response = await fetch(`/api/expedientes/${expedienteId}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (response.ok) {
                alert('Expediente actualizado correctamente');
                closeEditModal();
                if (typeof loadExpedientes === 'function') loadExpedientes();
            } else {
                alert('Error: ' + (result.message || 'No se pudo actualizar el expediente'));
            }
        } catch (error) {
            console.error('Error al actualizar expediente:', error);
            alert('Error al actualizar expediente');
        }
    });
});
</script>
