@extends('layouts.app')

@section('title', 'Auditoría')

@section('styles')
<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #4A5568;
            color: white;
            padding: 20px;
        }

        .sidebar-logo {
            margin-bottom: 40px;
        }

        .sidebar-logo img {
            max-width: 180px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 15px;
        }

        .sidebar-menu a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            display: block;
            padding: 10px;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.1);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            background-color: #F7FAFC;
        }

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

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #E2E8F0;
        }

        th {
            background-color: #4A5568;
            color: white;
            font-weight: 500;
        }

        td {
            color: #2D3748;
        }

        /* Action Buttons */
        .btn-view {
            background-color: #000;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
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
            color: #4A5568;
        }

        .pagination-numbers a.active {
            background-color: #000;
            color: white;
        }

        .pagination-nav {
            color: #4A5568;
            text-decoration: none;
        }
    </style>
@endsection

@section('content')
        <div class="header">
            <h1>Auditoría</h1>
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
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Expediente</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="auditoriaTableBody">
                    <!-- Los datos se cargarán dinámicamente aquí -->
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <a href="#" class="pagination-nav" id="prevPage">← Anterior</a>
            <div class="pagination-numbers" id="paginationNumbers">
                <!-- Los números de página se generarán dinámicamente -->
            </div>
            <a href="#" class="pagination-nav" id="nextPage">Siguiente →</a>
        </div>
    </div>

    <script>
        let currentPage = 1;
        const itemsPerPage = 10;

        async function loadAuditoria(page = 1, search = '') {
            try {
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                const response = await fetch(`/api/auditoria?page=${page}&search=${search}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Error al cargar registros de auditoría');
                }

                const data = await response.json();
                renderAuditoria(data.data);
                renderPagination(data.meta);
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function renderAuditoria(registros) {
            const tbody = document.getElementById('auditoriaTableBody');
            tbody.innerHTML = registros.map(registro => `
                <tr>
                    <td>${registro.expediente}</td>
                    <td>${registro.fecha}</td>
                    <td>${registro.hora}</td>
                    <td>${registro.usuario}</td>
                    <td>${registro.accion}</td>
                    <td>
                        <button class="btn-view" onclick="verDetalle(${registro.id})">Ver</button>
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
            loadAuditoria(page, document.getElementById('searchInput').value);
        }

        async function verDetalle(id) {
            try {
                const token = localStorage.getItem('token') || sessionStorage.getItem('token');
                const response = await fetch(`/api/auditoria/${id}`, {
                    headers: {
                        'Authorization': `Bearer ${token}`
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    // Aquí puedes implementar la lógica para mostrar el detalle
                    // Por ejemplo, abrir un modal con la información
                    alert('Detalles del registro: ' + JSON.stringify(data));
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Búsqueda con debounce
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentPage = 1;
                loadAuditoria(1, e.target.value);
            }, 300);
        });

        // Cargar registros al iniciar
        document.addEventListener('DOMContentLoaded', () => {
            loadAuditoria();
        });
    </script>
@endsection