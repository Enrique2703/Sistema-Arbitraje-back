@extends('layouts.app')

@section('content')
@include('usuarios.create')
@include('usuarios.edit')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">
<div class="min-h-screen bg-gray-100 flex">

    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-900">Usuarios</h1>
                <div class="flex items-center space-x-4">
                    <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <img src="{{ asset('img/folder.png') }}" alt="User Icon" class="w-6 h-6">
                    </button>
                </div>
            </div>
        </header>

        <div class="flex-1 p-6">
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center space-x-4">
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
                </div>
                <button onclick="openCreateUserModal()"
                    class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition-colors font-medium">
                    Nuevo usuario
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nivel</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-white uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuariosTableBody" class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Cargando usuarios...</td>
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
<div id="passwordModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="p-4 w-full flex items-center justify-center">
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

<!-- Modal para Editar al Usuario -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        loadUsuarios(currentPage, perPage);
    });

    let allUsuarios = [];

    //Estado de paginación
    let currentPage = 1;
    let lastPage = 1;
    let perPage = 7;

    async function loadUsuarios(page = 1, pageSize = perPage) {
        const tbody = document.getElementById('usuariosTableBody');
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-gray-500">Cargando...</td></tr>`;

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const res = await fetch(`/api/usuarios?page=${page}&per_page=${pageSize}`, {
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
        const prevBtn = `<button ${prevDisabled ? 'disabled' : ''} onclick="goToPage(${currentPage-1})" class="px-3 py-2 text-sm text-black">&larr; Anterior</button>`;

        const nextDisabled = currentPage >= lastPage;
        const nextBtn = `<button ${nextDisabled ? 'disabled' : ''} onclick="goToPage(${currentPage+1})" class="px-3 py-2 text-sm text-black">Siguiente &rarr;</button>`;

        let pagesHtml = '';
        const maxPagesToShow = 4;
        let start = Math.max(1, currentPage - 3);
        let end = Math.min(lastPage, start + maxPagesToShow - 1);
        if (end - start < maxPagesToShow - 1) {
            start = Math.max(1, end - maxPagesToShow + 1);
        }

        if (start > 1) {
            pagesHtml += `<button onclick="goToPage(1)" class="mx-1 text-sm text-black">1</button>`;
            if (start > 2) pagesHtml += `<span class="mx-1 text-sm text-gray-400">...</span>`;
        }

        for (let p = start; p <= end; p++) {
            if (p === currentPage) {
                pagesHtml += `<button class="mx-1 px-2 py-1 text-sm bg-gray-200 rounded text-black">${p}</button>`;
            } else {
                pagesHtml += `<button onclick="goToPage(${p})" class="mx-1 text-sm text-black">${p}</button>`;
            }
        }

        if (end < lastPage) {
            if (end < lastPage - 1) pagesHtml += `<span class="mx-1 text-sm text-gray-400">...</span>`;
            pagesHtml += `<button onclick="goToPage(${lastPage})" class="mx-1 text-sm text-black">${lastPage}</button>`;
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
                <td class="px-6 py-4 capitalize">${usuario.nivel_usuario}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Botón Contraseña a la izquierda -->
                        <button onclick="openPasswordModal(${usuario.id})" class="bg-black text-white px-4 py-2 rounded-sm mr-4 w-28 text-center">Contraseña</button>

                        <!-- Acciones en columna a la derecha -->
                        <div class="flex flex-col items-end space-y-1">
                            <button onclick="openEditModal(${usuario.id})" class="text-gray-900 font-medium hover:underline">Editar</button>
                            <button onclick="deleteUsuario(${usuario.id})" class="text-red-500 hover:underline">Eliminar</button>
                        </div>
                    </div>
                </td>
            </tr>`;
        });
    }

    let searchTimeout;

    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const term = e.target.value.trim();
            fetchUsuarios(term);
        }, 300);
    });

    async function fetchUsuarios(search = '') {
        const tbody = document.getElementById('usuariosTableBody');
        tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-gray-500">Buscando...</td></tr>`;

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');

            const url = new URL('/api/usuarios', window.location.origin);
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
            
            if (data && Array.isArray(data.registros)) {
                allUsuarios = data.registros;
                currentPage = data.meta?.current_page || 1;
                lastPage = data.meta?.last_page || 1;
                perPage = data.meta?.per_page || 7;
                renderPagination();
            } else {
                allUsuarios = [];
                currentPage = 1;
                lastPage = 1;
                renderPagination();
            }
            
            renderUsuarios(allUsuarios);

        } catch (error) {
            console.error('Error en la búsqueda:', error);
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-red-500">Error al buscar usuarios</td></tr>`;
        }
    }


    document.getElementById('createUserForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const data = {
            nombres: form.nombres.value,
            email: form.email.value,
            password: form.password.value,
            nivel_usuario: form.nivel_usuario.value,
            estado: form.estado.value,
        };

        const token = localStorage.getItem('token') || sessionStorage.getItem('token');

        try {
            const res = await fetch('/api/usuarios', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });


            // Creado correctamente
            closeCreateModal();
            loadUsuarios(currentPage, perPage);
        } catch (error) {
        }
    });

    async function deleteUsuario(id) {
        if (!confirm('¿Seguro que deseas eliminar este usuario?')) return;

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const res = await fetch(`/api/usuarios/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (res.ok) {
                alert('Usuario eliminado correctamente');
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
        document.getElementById('createModalOverlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeCreateModal() {
        document.getElementById('createModalOverlay').classList.add('hidden');
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
            const res = await fetch(`/api/usuarios/${id}`, {
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

        document.getElementById('edit_nivel_usuario_modal').value = usuario.nivel_usuario || usuario.nivel || '';

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
            nivel_usuario: document.getElementById('edit_nivel_usuario_modal').value,
            estado: document.getElementById('edit_estado_modal').value,
        };
        const pwd = document.getElementById('edit_password_modal').value.trim();
        if (pwd) data.password = pwd;

        const token = localStorage.getItem('token') || sessionStorage.getItem('token');

        try {
            const res = await fetch(`/api/usuarios/${id}`, {
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
                alert('Usuario actualizado correctamente');
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