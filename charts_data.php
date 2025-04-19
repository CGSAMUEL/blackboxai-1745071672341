<?php
session_start();
include 'conexionDB.php';

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$conexion = conectarDB();

// Get data for bar chart: number of characters modified per year
$barLabels = [];
$barData = [];
$result = $conexion->query("SELECT YEAR(modified) as year, COUNT(*) as count FROM final_characters WHERE modified IS NOT NULL GROUP BY YEAR(modified) ORDER BY year");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $barLabels[] = $row['year'];
        $barData[] = (int)$row['count'];
    }
}

// Get data for pie chart: distribution of comics by format
$pieLabels = [];
$pieData = [];
$result = $conexion->query("SELECT format, COUNT(*) as count FROM final_comics GROUP BY format");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $pieLabels[] = $row['format'] ?: 'Unknown';
        $pieData[] = (int)$row['count'];
    }
}

// Get data for line chart: comics published per month in current year
$lineLabels = [];
$lineData = [];
$currentYear = date('Y');
$result = $conexion->query("SELECT MONTH(onsale_date) as month, COUNT(*) as count FROM final_comics WHERE YEAR(onsale_date) = $currentYear GROUP BY MONTH(onsale_date) ORDER BY month");
if ($result) {
    $months = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $monthCounts = array_fill(1, 12, 0);
    while ($row = $result->fetch_assoc()) {
        $monthCounts[(int)$row['month']] = (int)$row['count'];
    }
    for ($i = 1; $i <= 12; $i++) {
        $lineLabels[] = $months[$i-1];
        $lineData[] = $monthCounts[$i];
    }
}

$conexion->close();

header('Content-Type: application/json');
echo json_encode([
    'bar' => ['labels' => $barLabels, 'data' => $barData],
    'pie' => ['labels' => $pieLabels, 'data' => $pieData],
    'line' => ['labels' => $lineLabels, 'data' => $lineData]
]);
?>
