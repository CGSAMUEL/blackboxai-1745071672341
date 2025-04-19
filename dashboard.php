<?php
session_start();
include 'conexionDB.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'] ?? null;
$admin = $_SESSION['admin'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard - Marvel Info</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #1a1a1a;
            color: #f0f0f0;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        header {
            background: linear-gradient(90deg, #e62429, #ff4500);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        main {
            padding: 2rem;
        }
        .list-section {
            margin-bottom: 2rem;
        }
        .list-section h2 {
            margin-bottom: 1rem;
            font-weight: 600;
            font-size: 1.5rem;
        }
        .list-results {
            background: #222;
            padding: 1rem;
            border-radius: 0.375rem;
            max-height: 300px;
            overflow-y: auto;
        }
        .chart-container {
            background: #222;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-top: 2rem;
        }
    </style>
    <script>
        async function fetchList(url, params, containerId) {
            const query = new URLSearchParams(params).toString();
            const response = await fetch(url + '?' + query);
            const text = await response.text();
            document.getElementById(containerId).innerHTML = text;
        }

        function setupListeners() {
            document.getElementById('characterName').addEventListener('input', () => {
                fetchList('buscarPersonajes.php', { nombre: document.getElementById('characterName').value }, 'charactersList');
            });
            document.getElementById('comicName').addEventListener('input', () => {
                fetchList('buscarComics.php', { nombre: document.getElementById('characterName').value, fecha: document.getElementById('comicDate').value }, 'comicsList');
            });
            document.getElementById('comicDate').addEventListener('change', () => {
                fetchList('buscarComics.php', { nombre: document.getElementById('characterName').value, fecha: document.getElementById('comicDate').value }, 'comicsList');
            });
            document.getElementById('creatorName').addEventListener('input', () => {
                fetchList('buscarCreadores.php', { nombre: document.getElementById('characterName').value, creador: document.getElementById('creatorName').value }, 'creatorsList');
            });
        }

        window.onload = () => {
            setupListeners();
            // Initial fetch
            fetchList('buscarPersonajes.php', { nombre: '' }, 'charactersList');
            fetchList('buscarComics.php', { nombre: '', fecha: '' }, 'comicsList');
            fetchList('buscarCreadores.php', { nombre: '', creador: '' }, 'creatorsList');
        };
    </script>
</head>
<body>
    <header>
        <div>Bienvenido, <?php echo htmlspecialchars($username); ?></div>
        <div><a href="logout.php" class="text-white hover:underline">Cerrar sesión</a></div>
    </header>
    <main>
        <section class="list-section">
            <h2>Buscar Personajes</h2>
            <input type="text" id="characterName" placeholder="Nombre del personaje" class="p-2 rounded w-full max-w-md mb-2 text-black" />
            <div id="charactersList" class="list-results"></div>
        </section>
        <section class="list-section">
            <h2>Buscar Comics</h2>
            <input type="text" id="comicName" placeholder="Nombre del personaje para comics" class="p-2 rounded w-full max-w-md mb-2 text-black" />
            <input type="date" id="comicDate" class="p-2 rounded w-full max-w-md mb-2" />
            <div id="comicsList" class="list-results"></div>
        </section>
        <section class="list-section">
            <h2>Buscar Creadores</h2>
            <input type="text" id="creatorName" placeholder="Nombre del creador" class="p-2 rounded w-full max-w-md mb-2 text-black" />
            <div id="creatorsList" class="list-results"></div>
        </section>
        <section class="chart-container">
            <h2>Gráficos (Próximamente)</h2>
            <p>Implementación de gráficos de columnas, tarta y líneas será añadida aquí.</p>
        </section>
    </main>
</body>
</html>
