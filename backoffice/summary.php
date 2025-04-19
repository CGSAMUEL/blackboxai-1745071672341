<?php
session_start();
include '../conexionDB.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header('Location: ../index.php');
    exit();
}

$conexion = conectarDB();

// Get counts for summary
$summary = [];

$tables = [
    'final_characters' => 'Personajes',
    'final_comics' => 'Comics',
    'final_creators' => 'Creadores',
    'final_events' => 'Eventos',
    'final_series' => 'Series',
    'final_stories' => 'Historias',
    'users' => 'Usuarios registrados'
];

foreach ($tables as $table => $label) {
    $result = $conexion->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        $summary[$label] = $row['count'];
    } else {
        $summary[$label] = 'Error';
    }
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Resumen de Datos - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white p-6 min-h-screen">
    <h1 class="text-3xl font-bold mb-6">Resumen de Datos en la Base</h1>
    <table class="min-w-full bg-gray-800 rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-red-600 text-white text-left">
                <th class="py-3 px-6">Entidad</th>
                <th class="py-3 px-6">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($summary as $entity => $count): ?>
            <tr class="border-b border-gray-700 hover:bg-gray-700">
                <td class="py-3 px-6"><?php echo htmlspecialchars($entity); ?></td>
                <td class="py-3 px-6"><?php echo htmlspecialchars($count); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="mt-6"><a href="admin.php" class="text-red-500 hover:underline">Volver a Gestión de Base de Datos</a></p>
</body>
</html>
