<?php
session_start();
include '../conexionDB.php';

// Show last update date
$conexion = conectarDB();
$lastUpdate = '';
$result = $conexion->query("SELECT MAX(modified) as last_update FROM final_characters");
if ($result) {
    $row = $result->fetch_assoc();
    $lastUpdate = $row['last_update'] ?? '';
}
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Marvel Info - Anónimo</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-6 min-h-screen">
    <h1 class="text-3xl font-bold mb-4">Bienvenido a Marvel Info</h1>
    <p>Última actualización de datos desde la API: <strong><?php echo htmlspecialchars($lastUpdate); ?></strong></p>
    <p>Accede a los listados para explorar la información recogida a través de la API.</p>
    <ul class="list-disc ml-6 mt-4 space-y-2">
        <li><a href="../dashboard.php" class="text-red-500 hover:underline">Listado de Personajes</a></li>
        <li><a href="../dashboard.php" class="text-red-500 hover:underline">Listado de Comics</a></li>
        <li><a href="../dashboard.php" class="text-red-500 hover:underline">Listado de Creadores</a></li>
    </ul>
    <p class="mt-6">¿Quieres registrarte? <a href="../registration.php" class="text-red-500 hover:underline">Haz clic aquí</a></p>
</body>
</html>
