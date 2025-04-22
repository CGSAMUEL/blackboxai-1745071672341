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
                const creatorId = document.getElementById('creatorFilter').value;
                fetchList('buscarPersonajes.php', { nombre: document.getElementById('characterName').value, creator_id: creatorId }, 'charactersList');
            });
            document.getElementById('creatorFilter').addEventListener('change', () => {
                const creatorId = document.getElementById('creatorFilter').value;
                fetchList('buscarPersonajes.php', { nombre: document.getElementById('characterName').value, creator_id: creatorId }, 'charactersList');
            });
            document.getElementById('comicName').addEventListener('input', () => {
                fetchList('buscarComics.php', { nombre: document.getElementById('comicName').value, fecha: document.getElementById('comicDate').value }, 'comicsList');
            });
            document.getElementById('comicDate').addEventListener('change', () => {
                fetchList('buscarComics.php', { nombre: document.getElementById('comicName').value, fecha: document.getElementById('comicDate').value }, 'comicsList');
            });
            document.getElementById('creatorName').addEventListener('input', () => {
+                console.log("Creator name input changed");
+                const characterFilter = document.getElementById('characterFilterForCreators').value;
+                console.log("Character filter value on creatorName input:", characterFilter);
                const characterFilter = document.getElementById('characterFilterForCreators').value;
                fetchList('buscarCreadores.php', { nombre: document.getElementById('creatorName').value, character_id: characterFilter }, 'creatorsList');
            });
            document.getElementById('characterFilterForCreators').addEventListener('change', () => {
+                console.log("Character filter changed");
+                const characterFilter = document.getElementById('characterFilterForCreators').value;
+                console.log("Character filter value on change:", characterFilter);
                const characterFilter = document.getElementById('characterFilterForCreators').value;
                fetchList('buscarCreadores.php', { nombre: document.getElementById('creatorName').value, character_id: characterFilter }, 'creatorsList');
            });
            }

        window.onload = () => {
            setupListeners();
            // Initial fetch
            fetchList('buscarPersonajes.php', { nombre: '' }, 'charactersList');
            fetchList('buscarComics.php', { nombre: '', fecha: '' }, 'comicsList');
            fetchList('buscarCreadores.php', { nombre: '', character_id: '' }, 'creatorsList');
        };
    </script>
</head>
<body>
    <header class="flex justify-between items-center bg-red-700 p-4 text-white">
        <div>Bienvenido, <?php echo htmlspecialchars($username); ?></div>
        <nav>
            <?php if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Anonymous' && strtolower($_SESSION['username']) !== 'guest'): ?>
            <a href="profile.php" class="mr-4 hover:underline">Mi Perfil</a>
            <?php endif; ?>
            <a href="logout.php" class="hover:underline">Cerrar sesión</a>
        </nav>
    </header>
    <main>
<?php
// Fetch creators for dropdown
$conexion = conectarDB();
$creators_result = $conexion->query("SELECT id, full_name FROM final_creators ORDER BY full_name ASC");
$creators = [];
if ($creators_result && $creators_result->num_rows > 0) {
    while ($row = $creators_result->fetch_assoc()) {
        $creators[] = $row;
    }
}
$conexion->close();
?>
        <section class="list-section">
            <h2>Buscar Personajes</h2>
            <input type="text" id="characterName" placeholder="Nombre del personaje" class="p-2 rounded w-full max-w-md mb-2 text-black" />
            <select id="creatorFilter" class="p-2 rounded w-full max-w-md mb-2 text-black">
                <option value="">Filtrar por creador (opcional)</option>
                <?php foreach ($creators as $creator): ?>
                    <option value="<?php echo htmlspecialchars($creator['id']); ?>"><?php echo htmlspecialchars($creator['full_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Anonymous' && strtolower($_SESSION['username']) !== 'guest'): ?>
            <button id="downloadCharactersPdf" class="btn-marvel px-4 py-2 rounded text-white font-semibold mb-2">Descargar Resultados Personajes PDF</button>
            <?php else: ?>
            <p>Descarga de PDF disponible solo para usuarios registrados.</p>
            <?php endif; ?>
            <div id="charactersList" class="list-results"></div>
        </section>
        <section class="list-section">
            <h2>Buscar Comics</h2>
            <input type="text" id="comicName" placeholder="Nombre del personaje para comics" class="p-2 rounded w-full max-w-md mb-2 text-black" />
            <input type="date" id="comicDate" class="p-2 rounded w-full max-w-md mb-2" />
            <?php if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Anonymous' && strtolower($_SESSION['username']) !== 'guest'): ?>
            <button id="downloadComicsPdf" class="btn-marvel px-4 py-2 rounded text-white font-semibold mb-2">Descargar Resultados Comics PDF</button>
            <?php else: ?>
            <p>Descarga de PDF disponible solo para usuarios registrados.</p>
            <?php endif; ?>
            <div id="comicsList" class="list-results"></div>
        </section>
<?php
// Fetch characters for dropdown filter for creators
$conexion = conectarDB();
$characters_result = $conexion->query("SELECT id, name FROM final_characters ORDER BY name ASC");
$characters = [];
if ($characters_result && $characters_result->num_rows > 0) {
    while ($row = $characters_result->fetch_assoc()) {
        $characters[] = $row;
    }
}
$conexion->close();
?>
        <section class="list-section">
            <h2>Buscar Creadores</h2>
            <input type="text" id="creatorName" placeholder="Nombre del creador" class="p-2 rounded w-full max-w-md mb-2 text-black" />
            <select id="characterFilterForCreators" class="p-2 rounded w-full max-w-md mb-2 text-black">
                <option value="">Filtrar por personaje (opcional)</option>
                <?php foreach ($characters as $character): ?>
                    <option value="<?php echo htmlspecialchars($character['id']); ?>"><?php echo htmlspecialchars($character['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Anonymous' && strtolower($_SESSION['username']) !== 'guest'): ?>
            <button id="downloadCreatorsPdf" class="btn-marvel px-4 py-2 rounded text-white font-semibold mb-2">Descargar Resultados Creadores PDF</button>
            <?php else: ?>
            <p>Descarga de PDF disponible solo para usuarios registrados.</p>
            <?php endif; ?>
            <div id="creatorsList" class="list-results"></div>
        </section>
        <?php if (isset($_SESSION['username']) && $_SESSION['username'] !== 'Anonymous' && strtolower($_SESSION['username']) !== 'guest'): ?>
        <section class="chart-container">
            <h2>Gráficos de Marvel Info</h2>
            <canvas id="barChart" class="mb-6"></canvas>
            <canvas id="pieChart" class="mb-6"></canvas>
            <canvas id="lineChart" class="mb-6"></canvas>
            <button id="downloadChartsPdf" class="btn-marvel px-4 py-2 rounded text-white font-semibold">Descargar PDF de Gráficos</button>
        </section>
        <?php else: ?>
        <section class="chart-container">
            <h2>Gráficos de Marvel Info</h2>
            <p>Esta funcionalidad está disponible solo para usuarios registrados. Por favor, inicie sesión para acceder a los gráficos y descargar PDFs.</p>
        </section>
        <?php endif; ?>
    </main>

        <script>
            async function fetchList(url, params, containerId) {
                const query = new URLSearchParams(params).toString();
                const response = await fetch(url + '?' + query);
                const text = await response.text();
                document.getElementById(containerId).innerHTML = text;
            }

            function setupListeners() {
            document.getElementById('characterName').addEventListener('input', () => {
                const creatorId = document.getElementById('creatorFilter').value;
                fetchList('buscarPersonajes.php', { nombre: document.getElementById('characterName').value, creator_id: creatorId }, 'charactersList');
            });
            document.getElementById('creatorFilter').addEventListener('change', () => {
                const creatorId = document.getElementById('creatorFilter').value;
                fetchList('buscarPersonajes.php', { nombre: document.getElementById('characterName').value, creator_id: creatorId }, 'charactersList');
            });
            document.getElementById('comicName').addEventListener('input', () => {
                fetchList('buscarComics.php', { nombre: document.getElementById('comicName').value, fecha: document.getElementById('comicDate').value }, 'comicsList');
            });
            document.getElementById('comicDate').addEventListener('change', () => {
                fetchList('buscarComics.php', { nombre: document.getElementById('comicName').value, fecha: document.getElementById('comicDate').value }, 'comicsList');
            });
            document.getElementById('creatorName').addEventListener('input', () => {
                fetchList('buscarCreadores.php', { nombre: document.getElementById('creatorName').value }, 'creatorsList');
            });

                // PDF download buttons
                document.getElementById('downloadChartsPdf').addEventListener('click', () => {
                    // Capture chart images as base64
                    const barImg = document.getElementById('barChart').toDataURL('image/png');
                    const pieImg = document.getElementById('pieChart').toDataURL('image/png');
                    const lineImg = document.getElementById('lineChart').toDataURL('image/png');

                    // Create form to POST images
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'pdf_export.php?type=charts';
                    form.target = '_blank';

                    // Add images as hidden inputs
                    [ ['bar', barImg], ['pie', pieImg], ['line', lineImg] ].forEach(([name, dataUrl]) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name + 'Image';
                        input.value = dataUrl;
                        form.appendChild(input);
                    });

                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                });
                document.getElementById('downloadCharactersPdf').addEventListener('click', () => {
                    const name = document.getElementById('characterName').value;
                    window.open('pdf_export.php?type=characters&nombre=' + encodeURIComponent(name), '_blank');
                });
                document.getElementById('downloadComicsPdf').addEventListener('click', () => {
                    const name = document.getElementById('comicName').value;
                    const date = document.getElementById('comicDate').value;
                    window.open('pdf_export.php?type=comics&nombre=' + encodeURIComponent(name) + '&fecha=' + encodeURIComponent(date), '_blank');
                });
                document.getElementById('downloadCreatorsPdf').addEventListener('click', () => {
                    const name = document.getElementById('creatorName').value;
                    window.open('pdf_export.php?type=creators&nombre=' + encodeURIComponent(name), '_blank');
                });
            }

            window.onload = () => {
                setupListeners();
                // Initial fetch
                fetchList('buscarPersonajes.php', { nombre: '' }, 'charactersList');
                fetchList('buscarComics.php', { nombre: '', fecha: '' }, 'comicsList');
                fetchList('buscarCreadores.php', { nombre: '' }, 'creatorsList');

                // Fetch and render charts
                fetch('charts_data.php')
                    .then(response => response.json())
                    .then(data => {
                        const barCtx = document.getElementById('barChart').getContext('2d');
                        const pieCtx = document.getElementById('pieChart').getContext('2d');
                        const lineCtx = document.getElementById('lineChart').getContext('2d');

                        window.barChartInstance = new Chart(barCtx, {
                            type: 'bar',
                            data: {
                                labels: data.bar.labels,
                                datasets: [{
                                    label: 'Personajes modificados',
                                    data: data.bar.data,
                                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                                }]
                            },
                            options: {}
                        });

                        window.pieChartInstance = new Chart(pieCtx, {
                            type: 'pie',
                            data: {
                                labels: data.pie.labels,
                                datasets: [{
                                    label: 'Formatos',
                                    data: data.pie.data,
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.7)',
                                        'rgba(54, 162, 235, 0.7)',
                                        'rgba(255, 206, 86, 0.7)',
                                        'rgba(75, 192, 192, 0.7)'
                                    ]
                                }]
                            },
                            options: {}
                        });

                        window.lineChartInstance = new Chart(lineCtx, {
                            type: 'line',
                            data: {
                                labels: data.line.labels,
                                datasets: [{
                                    label: 'Comics publicados',
                                    data: data.line.data,
                                    fill: false,
                                    borderColor: 'rgba(255, 99, 132, 0.7)',
                                    tension: 0.1
                                }]
                            },
                            options: {}
                        });
                    });
            };
        </script>
    </main>
</body>
</html>
