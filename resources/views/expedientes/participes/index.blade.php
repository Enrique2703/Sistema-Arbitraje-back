<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Red Nacional de Arbitraje - Consulta de expedientes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background-color: #f5f5f5;
        }

        /* === HEADER === */
        header {
            background-color: #6f6f6f;
            padding: 15px 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Logo + texto */
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            height: 38px;
            width: auto;
        }

        .logo-text {
            color: white;
            font-size: 14px;
            line-height: 1.2;
        }

        /* Usuario y logout */
        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-box {
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 4px;
            padding: 8px 20px;
            color: white;
            font-size: 14px;
        }

        .logout-btn {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: white;
            font-size: 18px;
            border-radius: 4px;
            padding: 8px 10px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .logout-btn:hover {
            background-color: rgba(255, 255, 255, 0.15);
        }

        /* === MAIN === */
        main {
            padding: 60px 80px;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 40px;
            font-weight: 400;
        }

        .search-section {
            display: flex;
            gap: 30px;
            margin-bottom: 40px;
        }

        .search-input {
            flex: 1;
            padding: 12px 15px 12px 45px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 15px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 15px center;
        }

        .search-btn {
            background-color: #000;
            color: white;
            border: none;
            padding: 12px 130px;
            font-size: 15px;
            cursor: pointer;
            border-radius: 3px;
        }

        .search-btn:hover {
            background-color: #333;
        }

        table {
            width: 100%;
            background-color: white;
            border-collapse: collapse;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        thead {
            background-color: #6b6b6b;
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 500;
            font-size: 14px;
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        tbody tr:hover {
            background-color: #f9f9f9;
        }

        .btn-ver {
            background-color: #000;
            color: white;
            border: none;
            padding: 8px 25px;
            cursor: pointer;
            font-size: 13px;
            border-radius: 3px;
        }

        .btn-ver:hover {
            background-color: #333;
        }

        .badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 12px;
            background-color: #d4d4d4;
            color: #333;
        }

        .badge.activo {
            background-color: #c8d4c0;
            color: #2d5016;
        }

        .btn-seguir {
            background-color: #000;
            color: white;
            border: none;
            padding: 8px 20px;
            cursor: pointer;
            font-size: 13px;
            border-radius: 3px;
        }

        .btn-seguir:hover {
            background-color: #333;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 15px;
            background-color: white;
        }

        .pagination-btn {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .pagination-btn:hover {
            color: #000;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination-numbers {
            display: flex;
            gap: 8px;
        }

        .page-number {
            background: none;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            font-size: 14px;
            color: #666;
            border-radius: 3px;
            text-decoration: none;
        }

        .page-number:hover {
            background-color: #f0f0f0;
        }

        .page-number.active {
            background-color: #e0e0e0;
            color: #000;
        }
    </style>
</head>

<body>

    <header>
        <div class="logo-section">
            <img src="/img/logo2.png" alt="Red Nacional de Arbitraje" class="logo">
            <div class="logo-text">
            </div>
        </div>
        <div class="user-section">
            <div class="user-box">
                <span>Nombre de usuario</span>
            </div>
            <button class="logout-btn" title="Cerrar sesión">⏻</button>
        </div>
    </header>

    <main>
        <h1>Consulta de expedientes</h1>

        <div class="search-section">
            <input type="text" class="search-input" placeholder="Buscar">
            <button class="search-btn">Buscar</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>Estado</th>
                    <th>Mi rol</th>
                    <th>Documentos</th>
                    <th>Actualización</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><button class="btn-ver">Ver</button></td>
                    <td>0001</td>
                    <td><span class="badge activo">Activo</span></td>
                    <td>Demandado</td>
                    <td>8</td>
                    <td>DD/MM/AA</td>
                    <td><button class="btn-seguir">Seguir trámite</button></td>
                </tr>
                <tr>
                    <td><button class="btn-ver">Ver</button></td>
                    <td>0001</td>
                    <td><span class="badge">Proveído</span></td>
                    <td>Demandado</td>
                    <td>8</td>
                    <td>DD/MM/AA</td>
                    <td><button class="btn-seguir">Seguir trámite</button></td>
                </tr>
                <tr>
                    <td><button class="btn-ver">Ver</button></td>
                    <td>0001</td>
                    <td><span class="badge">Proveído</span></td>
                    <td>Demandado</td>
                    <td>8</td>
                    <td>DD/MM/AA</td>
                    <td><button class="btn-seguir">Seguir trámite</button></td>
                </tr>
                <tr>
                    <td><button class="btn-ver">Ver</button></td>
                    <td>0001</td>
                    <td><span class="badge activo">Activo</span></td>
                    <td>Demandado</td>
                    <td>8</td>
                    <td>DD/MM/AA</td>
                    <td><button class="btn-seguir">Seguir trámite</button></td>
                </tr>
                <tr>
                    <td><button class="btn-ver">Ver</button></td>
                    <td>0001</td>
                    <td><span class="badge activo">Activo</span></td>
                    <td>Demandado</td>
                    <td>8</td>
                    <td>DD/MM/AA</td>
                    <td><button class="btn-seguir">Seguir trámite</button></td>
                </tr>
                <tr>
                    <td><button class="btn-ver">Ver</button></td>
                    <td>0001</td>
                    <td><span class="badge activo">Activo</span></td>
                    <td>Demandado</td>
                    <td>8</td>
                    <td>DD/MM/AA</td>
                    <td><button class="btn-seguir">Seguir trámite</button></td>
                </tr>
            </tbody>
        </table>

        <div class="pagination">
            <button class="pagination-btn">← Anterior</button>
            <div class="pagination-numbers">
                <button class="page-number active">1</button>
                <button class="page-number">2</button>
                <button class="page-number">3</button>
                <button class="page-number">4</button>
                <span>...</span>
                <button class="page-number">12</button>
                <button class="page-number">13</button>
                <button class="page-number">14</button>
                <button class="page-number">15</button>
            </div>
            <button class="pagination-btn">Siguiente →</button>
        </div>
    </main>

    <script>
        // Funcionalidad del buscador
        document.querySelector('.search-btn').addEventListener('click', function() {
            const searchValue = document.querySelector('.search-input').value;
            console.log('Buscando:', searchValue);
        });

        // Funcionalidad de los botones Ver
        document.querySelectorAll('.btn-ver').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = row.querySelector('td:nth-child(2)').textContent;
                console.log('Ver expediente:', id);
            });
        });

        // Funcionalidad de los botones Seguir trámite
        document.querySelectorAll('.btn-seguir').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = row.querySelector('td:nth-child(2)').textContent;
                console.log('Seguir trámite:', id);
            });
        });

        // Paginación
        document.querySelectorAll('.page-number').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.page-number').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>

</html>
