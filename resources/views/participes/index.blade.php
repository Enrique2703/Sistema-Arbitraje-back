@extends('layouts.app')
@include('participes.create')
@include('participes.edit')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<div class="min-h-screen bg-gray-100 flex">

    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Partícipes</h1>
                <div class="flex items-center space-x-4">
                    <button onclick="exportToExcel()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <img src="{{ asset('img/folder.png') }}" alt="User Icon" class="w-6 h-6">
                    </button>
                </div>
            </div>
        </header>

        <div class="flex-1 p-6">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="searchInput"
                            class="block w-80 pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Buscar">
                    </div>

                    <!-- Botón de filtro -->
                    <button id="filterButton"
                        class="p-2 bg-gray-200 hover:bg-gray-300 rounded-md border border-gray-300 flex items-center justify-center">
                        <i class="bi bi-funnel-fill text-black text-lg"></i>
                    </button>
                </div>
                <button onclick="openCreateModal()"
                    class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors font-medium">
                    Nuevo participe
                </button>
            </div>
            <!--Tabla-->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead style="background-color: #737373;">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nombres</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Expedientes</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuariosTableBody" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Cargando partícipes...</td>
                        </tr>
                    </tbody>
                </table>
                <!--Paginación -->
                <div id="paginationContainer" class="px-6 py-4 border-t bg-white">
                    <nav id="paginationControls" class="flex items-center justify-between" aria-label="Pagination">
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cambiar Contraseña -->
<div id="passwordModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full max-h-screen overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Cambiar Contraseña</h3>
                <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 py-4">
                <form id="passwordForm" onsubmit="event.preventDefault(); changePassword();">
                    <input type="hidden" id="passwordUserId">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nueva contraseña</label>
                        <input type="password" id="newPassword" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar contraseña</label>
                        <input type="password" id="confirmPassword" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closePasswordModal()"
                            class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-black text-white rounded-md hover:bg-gray-800">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 🔽 MODAL DE FILTRO -->
<div id="filterModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800">Filtrar partícipes</h2>

        <label class="block text-sm text-gray-700 mb-2">Estado:</label>
        <select id="estadoFilter"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-6">
            <option value="Todos">Todos</option>
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
        </select>

        <div class="flex justify-end space-x-3">
            <button id="closeFilterModal" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 text-gray-800">
                Cancelar
            </button>
            <button id="applyFilter"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                Aplicar
            </button>
        </div>
    </div>
</div>

<!-- Modal para Editar al Usuario -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadUsuarios(currentPage, perPage);

        // Configuración del filtro
        document.getElementById('filterButton').addEventListener('click', () => {
            document.getElementById('filterModal').classList.remove('hidden');
        });

        document.getElementById('closeFilterModal').addEventListener('click', () => {
            document.getElementById('filterModal').classList.add('hidden');
        });

        document.getElementById('applyFilter').addEventListener('click', () => {
            const estado = document.getElementById('estadoFilter').value;
            document.getElementById('filterModal').classList.add('hidden');
            loadUsuarios(1, perPage, document.getElementById('searchInput').value.trim(), estado);
        });
    });

    let allUsuarios = [];
    let estadoSeleccionado = 'Todos';

    //Estado de paginación
    let currentPage = 1;
    let lastPage = 1;
    let perPage = 7;

    async function loadUsuarios(page = 1, pageSize = perPage, search = '', estado = estadoSeleccionado) {
        const tbody = document.getElementById('usuariosTableBody');
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-gray-500">Cargando...</td></tr>`;

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const url = new URL('/api/participes', window.location.origin);
            url.searchParams.append('page', page);
            url.searchParams.append('per_page', pageSize);
            if (search) url.searchParams.append('search', search);
            if (estado && estado !== 'Todos') url.searchParams.append('estado', estado);

            const res = await fetch(url, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                if (res.status === 401) {

                    window.location.href = '/login';
                    return;
                }
                throw new Error('Error al obtener usuarios');
            }

            const data = await res.json();

            if (data && Array.isArray(data.registros)) {
                allUsuarios = data.registros;
                currentPage = data.meta?.current_page || page;
                lastPage = data.meta?.last_page || 1;
                perPage = data.meta?.per_page || pageSize;
                renderPagination();
            } else if (Array.isArray(data)) {
                allUsuarios = data;
                currentPage = 1;
                lastPage = 1;
                renderPagination();
            } else {
                allUsuarios = [];
            }

            if (allUsuarios.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-gray-500">No hay usuarios registrados</td></tr>`;
                return;
            }

            renderUsuarios(allUsuarios);
        } catch (error) {
            console.error(error);
            const tbody = document.getElementById('usuariosTableBody');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-red-500">Error al cargar usuarios</td></tr>`;
        }
    }

    function renderPagination() {
        const container = document.getElementById('paginationControls');
        container.innerHTML = '';

        const prevDisabled = currentPage <= 1;
        const prevBtn = `<button ${prevDisabled ? 'disabled' : ''} onclick="goToPage(${currentPage-1})" class="px-3 py-2 text-sm ${prevDisabled ? 'text-gray-400' : 'text-gray-700'}">&larr; Anterior</button>`;

        const nextDisabled = currentPage >= lastPage;
        const nextBtn = `<button ${nextDisabled ? 'disabled' : ''} onclick="goToPage(${currentPage+1})" class="px-3 py-2 text-sm ${nextDisabled ? 'text-gray-400' : 'text-gray-700'}">Siguiente &rarr;</button>`;

        let pagesHtml = '';
        const maxPagesToShow = 4;
        let start = Math.max(1, currentPage - 3);
        let end = Math.min(lastPage, start + maxPagesToShow - 1);
        if (end - start < maxPagesToShow - 1) {
            start = Math.max(1, end - maxPagesToShow + 1);
        }

        if (start > 1) {
            pagesHtml += `<button onclick="goToPage(1)" class="mx-1 text-sm text-gray-500">1</button>`;
            if (start > 2) pagesHtml += `<span class="mx-1 text-sm text-gray-400">...</span>`;
        }

        for (let p = start; p <= end; p++) {
            if (p === currentPage) {
                pagesHtml += `<button class="mx-1 px-2 py-1 text-sm bg-gray-100 rounded">${p}</button>`;
            } else {
                pagesHtml += `<button onclick="goToPage(${p})" class="mx-1 text-sm text-gray-500">${p}</button>`;
            }
        }

        if (end < lastPage) {
            if (end < lastPage - 1) pagesHtml += `<span class="mx-1 text-sm text-gray-400">...</span>`;
            pagesHtml += `<button onclick="goToPage(${lastPage})" class="mx-1 text-sm text-gray-500">${lastPage}</button>`;
        }

        container.innerHTML = `
            <div class="flex items-center justify-between w-full">
                <div>${prevBtn}</div>
                <div class="flex items-center">${pagesHtml}</div>
                <div>${nextBtn}</div>
            </div>
        `;
    }

    function goToPage(page) {
        if (page < 1) page = 1;
        if (page > lastPage) page = lastPage;
        loadUsuarios(page, perPage);
    }

    function renderUsuarios(list) {
        const tbody = document.getElementById('usuariosTableBody');
        if (!Array.isArray(list) || list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-gray-500">No hay usuarios registrados</td></tr>`;
            return;
        }

        tbody.innerHTML = '';
        list.forEach(usuario => {
            tbody.innerHTML += `
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4">${String(usuario.id).padStart(4, '0')}</td>
                <td class="px-6 py-4">${usuario.nombres}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-xs font-semibold ${
                        usuario.estado === 'Activo'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    }">${usuario.estado}</span>
                </td>
                <td class="px-6 py-4">${usuario.credencial?.email || 'Sin correo'}</td>
                <td class="px-6 py-4">${usuario.documentos_count || 0}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openPasswordModal(${usuario.id})" class="bg-black text-white px-3 py-1.5 rounded-sm text-center">Contraseña</button>
                        <button onclick="openEditModal(${usuario.id})" class="text-gray-900 font-medium hover:underline ml-2">Editar</button>
                        <button onclick="deleteUsuario(${usuario.id})" class="text-red-500 hover:underline ml-2">Eliminar</button>
                    </div>
                </td>
            </tr>`;
        });
    }

    let searchTimeout;

    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const searchTerm = e.target.value.trim();
            loadUsuarios(1, perPage, searchTerm, estadoSeleccionado);
        }, 300);
    });

    async function fetchUsuarios(search = '') {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');

            const url = new URL('/api/participes', window.location.origin);
            const params = {
                page: 1,
                per_page: 7
            };
            if (search) params.search = search;

            Object.entries(params).forEach(([key, value]) => url.searchParams.append(key, value));

            const res = await fetch(url, {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                if (res.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                throw new Error(`Error ${res.status} al obtener los usuarios`);
            }

            const data = await res.json();
            allUsuarios = data.registros || [];
            renderUsuarios(allUsuarios);

        } catch (error) {
            console.error('Error en la búsqueda:', error);
        }
    }

    async function exportToExcel() {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            console.log('Iniciando exportación...');
            
            const response = await fetch('/api/participes/export', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                console.error('Error en la respuesta:', response.status);
                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                throw new Error('Error al exportar datos');
            }

            console.log('Datos recibidos, procesando...');
            const data = await response.json();
            
            if (!Array.isArray(data)) {
                console.error('Datos recibidos no válidos:', data);
                throw new Error('Formato de datos inválido');
            }

            // Crear un libro de Excel usando SheetJS
            const ws = XLSX.utils.json_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Partícipes");

            // Ajustar el ancho de las columnas
            const colWidths = [
                { wch: 8 },  // ID
                { wch: 30 }, // NOMBRES
                { wch: 15 }, // ESTADO
                { wch: 35 }, // EMAIL
                { wch: 15 }  // EXPEDIENTES
            ];
            ws['!cols'] = colWidths;

            console.log('Descargando archivo...');
            // Descargar el archivo con formato de fecha DD-MM-YYYY
            const today = new Date();
            const day = String(today.getDate()).padStart(2, '0');
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const year = today.getFullYear();
            XLSX.writeFile(wb, `Participes_${day}-${month}-${year}.xlsx`);
            console.log('Exportación completada');

        } catch (error) {
            console.error('Error detallado:', error);
            alert('Error al exportar los datos: ' + error.message);
        }
    }

    document.getElementById('createUserForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const data = {
            nombres: form.nombres.value,
            email: form.email.value,
            password: form.password.value,
            estado: form.estado.value,
        };

        const token = localStorage.getItem('token') || sessionStorage.getItem('token');

        try {
            const res = await fetch('/api/participes', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!res.ok) {
                let errorText = 'No se pudo crear el usuario.';
                try {
                    const errorData = await res.json();
                    errorText = errorData.message || errorText;
                } catch (_) {}
                alert('Error: ' + errorText);
                return;
            }

            // Creado correctamente
            alert('Partícipe creado exitosamente');
            closeCreateModal();
            loadUsuarios(currentPage, perPage);
        } catch (err) {
            console.error(err);
            alert('Error de conexión con la API');
        }
    });

    async function deleteUsuario(id) {
        if (!confirm('¿Seguro que deseas eliminar este partícipe?')) return;

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const res = await fetch(`/api/participes/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (res.ok) {
                alert('Partícipe eliminado correctamente');
                loadUsuarios(currentPage, perPage);
            } else {
                let errMsg = 'Error al eliminar usuario';
                try {
                    const errData = await res.json();
                    errMsg = errData.message || errMsg;
                } catch (_) {}
                alert(errMsg);
            }
        } catch (error) {
            console.error(error);
            alert('Error de conexión con la API');
        }
    }

    // Modales
    function openCreateModal() {
        document.getElementById('createParticipeModalOverlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateModal() {
        document.getElementById('createParticipeModalOverlay').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('createUserForm').reset();
    }

    // Modales de contraseña
    function openPasswordModal(id) {
        document.getElementById('passwordUserId').value = id;
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
        document.getElementById('passwordModalOverlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePasswordModal() {
        document.getElementById('passwordModalOverlay').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('passwordForm').reset();
    }

    async function changePassword() {
        const id = document.getElementById('passwordUserId').value;
        const newPassword = document.getElementById('newPassword').value.trim();
        const confirmPassword = document.getElementById('confirmPassword').value.trim();

        if (!newPassword || !confirmPassword) {
            alert('Por favor ingresa la nueva contraseña y su confirmación.');
            return;
        }
        if (newPassword !== confirmPassword) {
            alert('Las contraseñas no coinciden.');
            return;
        }
        if (newPassword.length < 6) {
            alert('La contraseña debe tener al menos 6 caracteres.');
            return;
        }

        const token = localStorage.getItem('token') || sessionStorage.getItem('token');

        try {
            const res = await fetch(`/api/participes/${id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    password: newPassword
                })
            });

            if (res.ok) {
                alert('Contraseña actualizada correctamente');
                closePasswordModal();
                // Recargar lista para reflejar cambios si fuera necesario
                loadUsuarios(currentPage, perPage);
            } else {
                let errMsg = 'Error al actualizar contraseña';
                try {
                    const errData = await res.json();
                    errMsg = errData.message || errMsg;
                } catch (_) {}
                alert(errMsg);
            }
        } catch (err) {
            console.error(err);
            alert('Error de conexión con la API');
        }
    }

    // Edición de usuario desde modal
    function openEditModal(id) {
        const usuario = allUsuarios.find(u => u.id === id);
        if (!usuario) return alert('Usuario no encontrado');

        document.getElementById('editUserId').value = usuario.id;
        document.getElementById('edit_nombres_modal').value = usuario.nombres || '';
        document.getElementById('edit_email_modal').value = usuario.credencial?.email || '';
        document.getElementById('edit_password_modal').value = '';

        const estadoVal = usuario.estado ? (usuario.estado.charAt(0).toUpperCase() + usuario.estado.slice(1)) : '';
        document.getElementById('edit_estado_modal').value = estadoVal;

        document.getElementById('editModalOverlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('editModalOverlay').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('editUserModalForm').reset();
    }

    async function submitEditUser() {
        const id = document.getElementById('editUserId').value;
        const data = {
            nombres: document.getElementById('edit_nombres_modal').value.trim(),
            email: document.getElementById('edit_email_modal').value.trim(),
            estado: document.getElementById('edit_estado_modal').value,
        };
        const pwd = document.getElementById('edit_password_modal').value.trim();
        if (pwd) data.password = pwd;

        const token = localStorage.getItem('token') || sessionStorage.getItem('token');

        try {
            const res = await fetch(`/api/participes/${id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (res.ok) {

                try {
                    const updated = await res.json();
                } catch (_) {}
                alert('Partícipe actualizado correctamente');
                closeEditModal();

                loadUsuarios(currentPage, perPage);
            } else {
                let errMsg = 'Error al actualizar usuario';
                try {
                    const errData = await res.json();
                    errMsg = errData.message || errMsg;
                } catch (_) {}
                alert(errMsg);
            }
        } catch (err) {
            console.error(err);
            alert('Error de conexión con la API');
        }
    }
    if (!localStorage.getItem('token') && !sessionStorage.getItem('token')) {
        window.location.href = '/login';
    }
</script>
@endsection