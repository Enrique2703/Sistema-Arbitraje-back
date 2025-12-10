<!-- Tom Select CSS y JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<!-- Modal Overlay para Editar Expediente -->
<div id="editModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden flex items-center justify-center">
    <div class="p-4 w-full flex items-center justify-center">
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
                                <button type="button" onclick="openCreateUserModal()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
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
                                <button type="button" onclick="openCreateUserModal()" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900">
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
    let editUsuariosOptions = '';
    let editClientesOptions = '';

    function openEditModal(id) {
        document.getElementById('editModalOverlay').classList.remove('hidden');
        loadEditSelects().then(() => loadExpedienteToForm(id));
    }

    function closeEditModal() {
        document.getElementById('editModalOverlay').classList.add('hidden');
        document.getElementById('editExpedienteForm').reset();
        ['editArbitrosContainer', 'editAdjudicadoresContainer', 'editSecretariosTecnicosContainer', 'editParticipesContainer', 'editFechasLaudoContainer', 'editFechasResolucionContainer'].forEach(id => {
            document.getElementById(id).innerHTML = '';
        });
    }

    async function loadEditSelects() {
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

            const usuarios = await usuariosRes.json();
            const clientes = await clientesRes.json();

            editUsuariosOptions = '<option value="">Seleccionar usuario</option>' + usuarios.map(u => `<option value="${u.id}">${u.id} - ${u.nombres}</option>`).join('');
            editClientesOptions = '<option value="">Seleccionar cliente</option>' + clientes.map(c => `<option value="${c.id}">${c.id} - ${c.nombre}</option>`).join('');
        } catch (error) {
            console.error('Error cargando selects para editar:', error);
        }
    }

    function editAddArbitro() {
        const container = document.getElementById('editArbitrosContainer');
        const usuarios = window.listaUsuariosEdit || [];
        const row = crearFilaUsuarioSelect('arbitros[]', usuarios);
        container.appendChild(row);
    }

    function editAddAdjudicador() {
        const container = document.getElementById('editAdjudicadoresContainer');
        const usuarios = window.listaUsuariosEdit || [];
        const row = crearFilaUsuarioSelect('adjutadores[]', usuarios);
        container.appendChild(row);
    }

    function editAddSecretarioTecnico() {
        const container = document.getElementById('editSecretariosTecnicosContainer');
        const usuarios = window.listaUsuariosEdit || [];
        const row = crearFilaUsuarioSelect('secretarios_tecnicos[]', usuarios);
        container.appendChild(row);
    }

    function editAddParticipe() {
        const container = document.getElementById('editParticipesContainer');
        const participes = window.listaParticipesEdit || [];
        const row = crearFilaParticipesSelect(participes);
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

    function openCreateUserModal() {
        const modal = document.getElementById('createUserModalOverlay');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    function openCreateParticipeModal() {
        const modal = document.getElementById('createParticipeModalOverlay');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editExpedienteForm');
        if (form) {
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

                const participesIds = formData.getAll('participes_id[]').filter(v => v);
                const participesCondiciones = formData.getAll('participes_condicion[]');
                data.participes = participesIds.map((id, index) => ({
                    id: parseInt(id),
                    condicion: participesCondiciones[index] || null
                }));

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
        }
    });
</script>
<script>
    function inicializarTomSelect(selects, datos, selectedId = '') {
        selects.forEach(select => {
            // Destruir instancia anterior si existe
            if (select.tomselect) {
                select.tomselect.destroy();
            }

            new TomSelect(select, {
                valueField: 'id',
                labelField: 'nombres',
                searchField: ['nombres', 'email', 'nombre'],
                options: datos,
                create: false,
                placeholder: select.name.includes('participes') ? 'Buscar partícipe...' : 'Buscar usuario...',
                render: {
                    option: function(item, escape) {
                        return `<div class="py-2 px-3">
                            <div class="font-medium">${escape(item.nombres || item.nombre || '')}</div>
                            <div class="text-sm text-gray-600">${escape(item.email || '')}</div>
                        </div>`;
                    },
                    item: function(item, escape) {
                        return `<div>${escape(item.nombres || item.nombre || '')}</div>`;
                    }
                },
                loadingClass: 'loading',
                load: async function(query, callback) {
                    try {
                        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                        const endpoint = select.name.includes('participes') ? '/api/participes' : '/api/usuarios';
                        const response = await fetch(`${endpoint}?search=${encodeURIComponent(query)}`, {
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        });
                        const json = await response.json();
                        callback(json.registros || []);
                    } catch (e) {
                        console.error('Error cargando datos:', e);
                        callback();
                    }
                }
            });

            // Si hay un selectedId, seleccionarlo
            if (selectedId && select.tomselect) {
                console.log('Seleccionando valor:', selectedId, 'para select:', select.name);
                // Usar setTimeout para asegurar que TomSelect esté listo
                setTimeout(() => {
                    if (select.tomselect) {
                        select.tomselect.setValue(selectedId.toString());
                        console.log('Valor seleccionado:', select.tomselect.getValue());
                    }
                }, 100);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        await cargarUsuariosEdit();
        await cargarParticipesEdit();
    });

    async function cargarUsuariosEdit() {
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
            window.listaUsuariosEdit = data.registros || data || [];

            // Inicializar Tom Select en todos los selects de usuarios
            inicializarTomSelect(
                document.querySelectorAll('select[name="arbitros[]"], select[name="adjutadores[]"], select[name="secretarios_tecnicos[]"]'),
                window.listaUsuariosEdit
            );
        } catch (error) {
            console.error('Error cargando usuarios (editar):', error);
        }
    }

    async function cargarParticipesEdit() {
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
            window.listaParticipesEdit = data.registros || data || [];

            // Inicializar Tom Select en todos los selects de partícipes
            inicializarTomSelect(
                document.querySelectorAll('select[name="participes_id[]"]'),
                window.listaParticipesEdit
            );
        } catch (error) {
            console.error('Error cargando partícipes (editar):', error);
        }
    }

    function crearFilaUsuarioSelect(name, usuarios, selectedId = '') {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 mb-2';

        row.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()"
            class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center
                    text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        <select name="${name}"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md
                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
            <option value="">Seleccionar usuario</option>
        </select>
    `;
        const select = row.querySelector('select');
        
        // Agregar opciones directamente al select tradicional primero
        if (usuarios && usuarios.length) {
            usuarios.forEach(usuario => {
                const option = document.createElement('option');
                option.value = usuario.id;
                option.textContent = usuario.nombres || usuario.nombre || `Usuario ${usuario.id}`;
                if (selectedId && usuario.id == selectedId) {
                    option.selected = true;
                    console.log('Seleccionado usuario tradicional:', usuario.nombres, 'ID:', usuario.id);
                }
                select.appendChild(option);
            });
        }
        
        // Luego inicializar TomSelect sobre el select ya poblado
        try {
            if (select.tomselect) {
                select.tomselect.destroy();
            }
            
            new TomSelect(select, {
                valueField: 'id',
                labelField: 'nombres',
                searchField: ['nombres'],
                create: false,
                placeholder: 'Buscar usuario...'
            });
            
            // Establecer valor seleccionado en TomSelect
            if (selectedId && select.tomselect) {
                setTimeout(() => {
                    select.tomselect.setValue(selectedId.toString());
                }, 50);
            }
        } catch (error) {
            console.error('Error inicializando TomSelect:', error);
        }
        
        return row;
    }

    function crearFilaParticipesSelect(clientes, selectedId = '', condicion = '') {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 mb-2';

        row.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()"
            class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center
                    text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
        <select name="participes_id[]"
            class="flex-1 px-3 py-2 border border-gray-300 rounded-md
                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
            <option value="">Seleccionar partícipe</option>
        </select>
        <select name="participes_condicion[]"
            class="w-40 px-3 py-2 border border-gray-300 rounded-md
                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
            <option value="">Condición</option>
            <option value="Demandante" ${condicion === 'Demandante' ? 'selected' : ''}>Demandante</option>
            <option value="Demandado" ${condicion === 'Demandado' ? 'selected' : ''}>Demandado</option>
        </select>
    `;
        
        const select = row.querySelector('select[name="participes_id[]"]');
        
        // Agregar opciones directamente primero
        if (clientes && clientes.length) {
            clientes.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.id;
                option.textContent = cliente.nombres || cliente.nombre || `Cliente ${cliente.id}`;
                if (selectedId && cliente.id == selectedId) {
                    option.selected = true;
                    console.log('Seleccionado partícipe tradicional:', cliente.nombres, 'ID:', cliente.id);
                }
                select.appendChild(option);
            });
        }
        
        // Luego inicializar TomSelect
        try {
            if (select.tomselect) {
                select.tomselect.destroy();
            }
            
            new TomSelect(select, {
                valueField: 'id',
                labelField: 'nombres',
                searchField: ['nombres'],
                create: false,
                placeholder: 'Buscar partícipe...'
            });
            
            if (selectedId && select.tomselect) {
                setTimeout(() => {
                    select.tomselect.setValue(selectedId.toString());
                }, 50);
            }
        } catch (error) {
            console.error('Error inicializando TomSelect para partícipes:', error);
        }
        
        return row;
    }


    async function loadExpedienteToForm(id) {
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');
        try {
            // Asegurar que los datos de usuarios y partícipes estén cargados
            await cargarUsuariosEdit();
            await cargarParticipesEdit();
            
            const res = await fetch(`/api/expedientes/${id}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'No se pudo obtener expediente');

            const exp = json.data;
            const form = document.getElementById('editExpedienteForm');

            // Debug: Mostrar qué datos están llegando
            console.log('Datos del expediente recibidos:', exp);
            console.log('Árbitros:', exp.arbitros);
            console.log('Adjutadores:', exp.adjutadores);
            console.log('Secretarios técnicos:', exp.secretarios_tecnicos);
            console.log('Partícipes:', exp.participes);
            console.log('Usuarios disponibles:', window.listaUsuariosEdit);
            console.log('Partícipes disponibles:', window.listaParticipesEdit);

            // Campos principales
            form.querySelector('input[name="expediente_id"]').value = exp.id || '';
            form.querySelector('input[name="usuario_id"]').value = exp.usuario?.id || '';
            form.querySelector('input[name="numero"]').value = exp.numero || '';
            form.querySelector('input[name="anio"]').value = exp.anio || '';
            if (form.querySelector('select[name="codigo"]'))
                form.querySelector('select[name="codigo"]').value = exp.codigo || '';
            form.querySelector('select[name="etapa_procesal"]').value = exp.etapa_procesal || '';
            form.querySelector('input[name="inicio_proceso"]').value = exp.inicio_proceso || '';
            form.querySelector('select[name="tipo_proceso"]').value = exp.tipo_proceso || '';
            form.querySelector('select[name="estado"]').value = exp.estado || '';

            const usuarios = window.listaUsuariosEdit || [];
            const clientes = window.listaParticipesEdit || [];

            // --- FECHAS ---
            const fLaudoCont = document.getElementById('editFechasLaudoContainer');
            fLaudoCont.innerHTML = '';
            const laudoRow = document.createElement('div');
            laudoRow.className = 'flex items-center gap-2 mb-2';
            laudoRow.innerHTML = `
            <button type="button" onclick="this.parentElement.remove()" 
                class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center 
                    text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
            <input type="date" name="fecha_laudo" 
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md 
                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                value="${exp.fecha_laudo || ''}">
        `;
            fLaudoCont.appendChild(laudoRow);

            const fResCont = document.getElementById('editFechasResolucionContainer');
            fResCont.innerHTML = '';
            const resRow = document.createElement('div');
            resRow.className = 'flex items-center gap-2 mb-2';
            resRow.innerHTML = `
            <button type="button" onclick="this.parentElement.remove()" 
                class="w-6 h-6 border-2 border-gray-700 rounded-full flex items-center justify-center 
                    text-gray-700 hover:bg-gray-100 flex-shrink-0">−</button>
            <input type="date" name="fecha_resolucion" 
                class="flex-1 px-3 py-2 border border-gray-300 rounded-md 
                    focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                value="${exp.fecha_resolucion || ''}">
        `;
            fResCont.appendChild(resRow);

            // --- ÁRBITROS ---
            const arbContainer = document.getElementById('editArbitrosContainer');
            arbContainer.innerHTML = '';
            if (exp.arbitros && exp.arbitros.length > 0) {
                console.log('Procesando árbitros:', exp.arbitros);
                exp.arbitros.forEach(arbitro => {
                    console.log('Árbitro:', arbitro);
                    const usuarioId = arbitro.usuario_id || (arbitro.usuario && arbitro.usuario.id);
                    console.log('Usuario ID del árbitro:', usuarioId);
                    arbContainer.appendChild(
                        crearFilaUsuarioSelect('arbitros[]', usuarios, usuarioId)
                    );
                });
            } else {
                console.log('No hay árbitros, creando fila vacía');
                arbContainer.appendChild(crearFilaUsuarioSelect('arbitros[]', usuarios));
            }

            // --- ADJUTADORES ---
            const adjContainer = document.getElementById('editAdjudicadoresContainer');
            adjContainer.innerHTML = '';
            if (exp.adjutadores && exp.adjutadores.length > 0) {
                console.log('Procesando adjutadores:', exp.adjutadores);
                exp.adjutadores.forEach(adjutador => {
                    const usuarioId = adjutador.usuario_id || (adjutador.usuario && adjutador.usuario.id);
                    adjContainer.appendChild(
                        crearFilaUsuarioSelect('adjutadores[]', usuarios, usuarioId)
                    );
                });
            } else {
                adjContainer.appendChild(crearFilaUsuarioSelect('adjutadores[]', usuarios));
            }

            // --- SECRETARIOS TÉCNICOS ---
            const secContainer = document.getElementById('editSecretariosTecnicosContainer');
            secContainer.innerHTML = '';
            if (exp.secretarios_tecnicos && exp.secretarios_tecnicos.length > 0) {
                console.log('Procesando secretarios técnicos:', exp.secretarios_tecnicos);
                exp.secretarios_tecnicos.forEach(secretario => {
                    const usuarioId = secretario.usuario_id || (secretario.usuario && secretario.usuario.id);
                    secContainer.appendChild(
                        crearFilaUsuarioSelect('secretarios_tecnicos[]', usuarios, usuarioId)
                    );
                });
            } else {
                secContainer.appendChild(crearFilaUsuarioSelect('secretarios_tecnicos[]', usuarios));
            }

            // --- PARTÍCIPES ---
            const partsContainer = document.getElementById('editParticipesContainer');
            partsContainer.innerHTML = '';
            if (exp.participes && exp.participes.length > 0) {
                console.log('Procesando partícipes:', exp.participes);
                exp.participes.forEach(participe => {
                    const participeId = participe.participe_id || (participe.participe && participe.participe.id);
                    const condicion = participe.condicion;
                    console.log('Partícipe ID:', participeId, 'Condición:', condicion);
                    partsContainer.appendChild(
                        crearFilaParticipesSelect(clientes, participeId, condicion)
                    );
                });
            } else {
                partsContainer.appendChild(crearFilaParticipesSelect(clientes));
            }

        } catch (error) {
            console.error('Error cargando expediente:', error);
            alert('No se pudo cargar el expediente para edición');
            if (typeof closeEditModal === 'function') closeEditModal();
        }
    }
</script>


<div id="createUserModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden">
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
                        <input type="text" name="nombres" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña</label>
                        <input type="password" name="password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nivel de usuario</label>
                            <select name="nivel_usuario" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="" disabled selected>Seleccionar</option>
                                <option value="administrador">Administrador</option>
                                <option value="staff">Staff</option>
                                <option value="participe">Participe</option>
                            </select>
                        </div>
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
                        <button type="button" onclick="closeCreateUserModal()"
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
    function closeCreateUserModal() {
        const modal = document.getElementById('createUserModalOverlay');
        if (modal) {
            modal.classList.add('hidden');
            const form = document.getElementById('createUserForm');
            if (form) form.reset();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const createUserModalOverlay = document.getElementById('createUserModalOverlay');
        const createUserForm = document.getElementById('createUserForm');

        if (createUserModalOverlay) {
            createUserModalOverlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeCreateUserModal();
                }
            });
        }

        if (createUserForm) {

            const newForm = createUserForm.cloneNode(true);
            createUserForm.parentNode.replaceChild(newForm, createUserForm);
            
            newForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                
                if (!token) {
                    alert('No se encontró token de autenticación');
                    return;
                }

                const formData = new FormData(this);

                const data = {
                    nombres: formData.get('nombres'),
                    email: formData.get('email'),
                    password: formData.get('password'),
                    nivel_usuario: formData.get('nivel_usuario'),
                    estado: formData.get('estado')
                };

                console.log('Enviando datos:', data);

                try {
                    const response = await fetch('/api/usuarios', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();
                    console.log('Respuesta del servidor:', result);

                    if (response.ok) {
                        alert('Usuario creado correctamente');
                        closeCreateUserModal();
                        // Recargar la lista de usuarios
                        if (typeof cargarUsuarios === 'function') await cargarUsuarios();
                        if (typeof cargarUsuariosEdit === 'function') await cargarUsuariosEdit();
                        // Recargar selects del modal de editar si está abierto
                        if (typeof loadEditSelects === 'function') await loadEditSelects();
                    } else {
                        console.error('Error del servidor:', result);
                        alert('Error: ' + (result.message || result.error || 'No se pudo crear el usuario'));
                    }
                } catch (error) {
                    console.error('Error en la petición:', error);
                    alert('Error al crear el usuario333: ' + error.message);
                }
            });
        }
    });
</script>