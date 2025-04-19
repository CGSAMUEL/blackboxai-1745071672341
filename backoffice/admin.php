<?php
session_start();
include '../conexionDB.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header('Location: ../index.php');
    exit();
}

$conexion = conectarDB();

$lastUpdate = '';
$result = $conexion->query("SELECT MAX(modified) as last_update FROM final_characters");
if ($result) {
    $row = $result->fetch_assoc();
    $lastUpdate = $row['last_update'] ?? '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'reset') {
            // Delete API data from tables
            $tables = ['final_characters', 'final_comics', 'final_comic_characters', 'final_comic_creators', 'final_comic_events', 'final_comic_stories', 'final_creators', 'final_creator_characters', 'final_events', 'final_series', 'final_stories'];
            foreach ($tables as $table) {
                $conexion->query("TRUNCATE TABLE $table");
            }
            $message = "Datos borrados correctamente.";
        } elseif ($_POST['action'] === 'fetch') {
            // Call API fetch script (assuming api_fetch.php is accessible)
            // For security, better to run this via CLI or controlled method
            exec('php ../api_fetch.php', $output, $return_var);
            if ($return_var === 0) {
                $message = "Datos actualizados desde la API correctamente.";
            } else {
                $message = "Error al actualizar datos desde la API.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - Gestión de Base de Datos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-6 min-h-screen">
    <h1 class="text-3xl font-bold mb-6">Gestión de Base de Datos - Admin</h1>
    <?php if (!empty($message)): ?>
        <div class="bg-green-600 p-3 rounded mb-4"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <p>Última actualización de datos desde la API: <strong><?php echo htmlspecialchars($lastUpdate); ?></strong></p>
<form method="POST" class="mt-4 space-x-4">
    <button type="submit" name="action" value="reset" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded">Borrar datos de la base</button>
    <button type="submit" name="action" value="fetch" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded">Actualizar datos desde la API</button>
</form>

<h2 class="text-2xl font-semibold mt-8 mb-4">Gestión de Usuarios</h2>
<p><a href="users.php" class="text-red-500 hover:underline">Ir a la gestión de usuarios</a></p>

<h2 class="text-2xl font-semibold mt-8 mb-4">Resumen de Datos</h2>
<p><a href="summary.php" class="text-red-500 hover:underline">Ver resumen de datos</a></p>
</body>
</html>
