@extends('layouts.app')

@section('title', 'Expedientes')
@include('expedientes.create')
@include('expedientes.edit')
@section('styles')
<style>
    /* Header Section */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .header h1 {
        font-size: 24px;
        color: #2D3748;
    }

    /* Search and Filters */
    .filters {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        gap: 20px;
    }

    .search-box {
        flex: 1;
        max-width: 400px;
        position: relative;
    }

    .search-box input {
        width: 100%;
        padding: 10px 40px 10px 15px;
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        font-size: 14px;
    }

    .search-box .search-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #A0AEC0;
    }

    .filter-button {
        background-color: #F7FAFC;
        border: 1px solid #E2E8F0;
        padding: 10px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-new {
        background-color: #000;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
    }

    .btn-new:hover {
        background-color: #2D3748;
    }

    /* Table */
    .table-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #E2E8F0;
    }

    th {
        background-color: #737373;
        color: white;
        font-weight: 500;
    }

    td {
        color: #2D3748;
    }

    .status-badge {
        background-color: #E2E8F0;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
    }

    .status-active {
        background-color: #C6F6D5;
        color: #2F855A;
    }

    .status-proveido {
        background-color: #FEEBC8;
        color: #C05621;
    }

    /* Action Buttons */
    .btn-action {
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        margin-right: 5px;
        text-decoration: none;
    }

    .btn-documents {
        background-color: #000;
        color: white;
    }

    .btn-history {
        background-color: #2D3748;
        color: white;
    }

    .btn-edit {
        color: #4A5568;
        text-decoration: none;
    }

    .btn-delete {
        color: #E53E3E;
        text-decoration: none;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding: 20px;
    }

    .pagination-numbers {
        display: flex;
        gap: 5px;
    }

    .pagination-numbers a {
        padding: 8px 12px;
        border-radius: 4px;
        text-decoration: none;
        color: #737373;
    }

    .pagination-numbers a.active {
        background-color: #000;
        color: white;
    }

    .pagination-nav {
        color: #737373;
        text-decoration: none;
    }
</style>
@endsection

@section('content')
<div class="header">
    <h1>Expedientes</h1>
</div>

<div class="filters">
    <div class="search-box">
        <input type="text" placeholder="Buscar" id="searchInput">
        <span class="search-icon">🔍</span>
    </div>
    <button class="filter-button" id="filterButton">
        <span>Filtros</span>
        <span>▼</span>
    </button>
    <button class="btn-new" onclick="openCreateModal()">Nuevo expediente</button>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Expediente</th>
                <th>Estado</th>
                <th>Partícipes</th>
                <th>Documentos</th>
                <th>Inicio</th>
                <th>Actualización</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="expedientesTableBody">
            <!-- Los datos se cargarán dinámicamente aquí -->
        </tbody>
    </table>
</div>

<div class="pagination">
    <a href="#" class="pagination-nav" id="prevPage">← Anterior</a>
    <div class="pagination-numbers" id="paginationNumbers"></div>
    <a href="#" class="pagination-nav" id="nextPage">Siguiente →</a>
</div>
@endsection

@section('scripts')
<script>
    let currentPage = 1;

    async function loadExpedientes(page = 1, search = '') {
        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const response = await fetch(`/api/expedientes?page=${page}&search=${search}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                throw new Error('Error al cargar expedientes');
            }

            const data = await response.json();
            renderExpedientes(data.registros);
            renderPagination(data.meta);
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function renderExpedientes(expedientes) {
        const tbody = document.getElementById('expedientesTableBody');

        if (!expedientes || expedientes.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;">No hay expedientes registrados</td></tr>`;
            return;
        }

        tbody.innerHTML = expedientes.map(expediente => `
            <tr>
                <td>${expediente.id}</td>
                <td>${expediente.estado}</td>
                <td>${expediente.cantidad_participes}</td>
                <td>${expediente.expediente}</td>
                <td>${expediente.fecha_creacion || '—'}</td>
                <td>${expediente.fecha_actualizacion ? new Date(expediente.fecha_actualizacion).toLocaleDateString() : '—'}</td>
                <td>
                    <a href="/expedientes/${expediente.id}/documentos" class="btn-action btn-documents">Documentos</a>
                    <a href="/expedientes/${expediente.id}/historial" class="btn-action btn-history">Historial</a>
                    <button type="button" onclick="openExpedienteModal(${expediente.id})" class="btn-edit">Editar</button>
                    <a href="#" onclick="deleteExpediente(${expediente.id})" class="btn-delete">Eliminar</a>
                </td>
            </tr>
        `).join('');
    }

    function renderPagination(meta) {
        const paginationDiv = document.getElementById('paginationNumbers');
        const totalPages = meta.last_page;
        let html = '';

        for (let i = 1; i <= totalPages; i++) {
            html += `<a href="#" class="${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</a>`;
        }

        paginationDiv.innerHTML = html;

        document.getElementById('prevPage').style.visibility = currentPage === 1 ? 'hidden' : 'visible';
        document.getElementById('nextPage').style.visibility = currentPage === totalPages ? 'hidden' : 'visible';
    }

    function changePage(page) {
        currentPage = page;
        loadExpedientes(page, document.getElementById('searchInput').value);
    }

    async function deleteExpediente(id) {
        if (!confirm('¿Está seguro de que desea eliminar este expediente?')) {
            return;
        }

        try {
            const token = localStorage.getItem('token') || sessionStorage.getItem('token');
            const response = await fetch(`/api/expedientes/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            if (response.ok) {
                loadExpedientes(currentPage);
            } else {
                alert('Error al eliminar el expediente');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al eliminar el expediente');
        }
    }

    // Búsqueda con debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadExpedientes(1, e.target.value);
        }, 300);
    });

    // Cargar expedientes al iniciar
    document.addEventListener('DOMContentLoaded', () => {
        loadExpedientes();
    });

    // Helper to open modal de editar independientemente de dónde esté definida
    function openExpedienteModal(id) {
        // Si la función openEditModal existe (se define en el partial edit), la llamamos
        if (typeof openEditModal === 'function') {
            openEditModal(id);
            return;
        }

        // Fallback: intentar mostrar el overlay directamente
        const overlay = document.getElementById('editModalOverlay');
        if (overlay) {
            overlay.classList.remove('hidden');
        } else {
            console.warn('Modal de edición no cargado. Asegúrate de incluir el partial edit.blade.php');
        }
    }

    // Mostrar y ocultar modal
    function openCreateModal() {
        document.getElementById('createModalOverlay').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModalOverlay').classList.add('hidden');
    }

    // Manejar el envío del formulario
    document.getElementById('createExpedienteForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const token = localStorage.getItem('token') || sessionStorage.getItem('token');

        try {
            const response = await fetch('/api/expedientes', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                alert('✅ Expediente creado correctamente');
                closeCreateModal();
                loadExpedientes(); // recarga la tabla
            } else {
                alert('❌ Error al crear expediente: ' + (data.message || 'Error desconocido'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al conectar con el servidor');
        }
    });
</script>
@endsection