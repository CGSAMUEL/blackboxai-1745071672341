<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/TCPDF-main/tcpdf.php';

include 'conexionDB.php';

function generatePDF($htmlContent, $filename = 'export.pdf') {
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Marvel Info');
    $pdf->SetTitle('Exported Data');
    $pdf->SetHeaderData('', 0, 'Marvel Info', 'Exported Data');
    $pdf->setHeaderFont(Array('helvetica', '', 12));
    $pdf->setFooterFont(Array('helvetica', '', 10));
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->AddPage();

    $pdf->writeHTML($htmlContent, true, false, true, false, '');

    $pdf->Output($filename, 'I');
}

function exportChartsPDF() {
    $conexion = conectarDB();

    // Check if images are posted
    $barImage = $_POST['barImage'] ?? null;
    $pieImage = $_POST['pieImage'] ?? null;
    $lineImage = $_POST['lineImage'] ?? null;

    $html = '<h1>Gráficos de Marvel Info</h1>';

    if ($barImage && $pieImage && $lineImage) {
        // Embed images in PDF
        $html .= '<h2>Gráfico de barras: Personajes modificados por año</h2>';
        $html .= '<img src="' . $barImage . '" style="width: 600px; height: 300px;" />';

        $html .= '<h2>Gráfico de tarta: Distribución de comics por formato</h2>';
        $html .= '<img src="' . $pieImage . '" style="width: 600px; height: 300px;" />';

        $html .= '<h2>Gráfico de líneas: Comics publicados por mes</h2>';
        $html .= '<img src="' . $lineImage . '" style="width: 600px; height: 300px;" />';
    } else {
        // Fallback to data tables if images not provided

        // Get data for bar chart
        $barLabels = [];
        $barData = [];
        $result = $conexion->query("SELECT YEAR(modified) as year, COUNT(*) as count FROM final_characters WHERE modified IS NOT NULL GROUP BY YEAR(modified) ORDER BY year");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $barLabels[] = $row['year'];
                $barData[] = (int)$row['count'];
            }
        }

        // Get data for pie chart
        $pieLabels = [];
        $pieData = [];
        $result = $conexion->query("SELECT format, COUNT(*) as count FROM final_comics GROUP BY format");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $pieLabels[] = $row['format'] ?: 'Unknown';
                $pieData[] = (int)$row['count'];
            }
        }

        // Get data for line chart
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

        // Compose tables
        $html .= '<h2>Personajes modificados por año</h2><table border="1" cellpadding="5"><thead><tr><th>Año</th><th>Conteo</th></tr></thead><tbody>';
        foreach ($barLabels as $index => $label) {
            $html .= '<tr><td>' . htmlspecialchars($label) . '</td><td>' . htmlspecialchars($barData[$index]) . '</td></tr>';
        }
        $html .= '</tbody></table>';

        $html .= '<h2>Distribución de comics por formato</h2><table border="1" cellpadding="5"><thead><tr><th>Formato</th><th>Conteo</th></tr></thead><tbody>';
        foreach ($pieLabels as $index => $label) {
            $html .= '<tr><td>' . htmlspecialchars($label) . '</td><td>' . htmlspecialchars($pieData[$index]) . '</td></tr>';
        }
        $html .= '</tbody></table>';

        $html .= '<h2>Comics publicados por mes en el año ' . $currentYear . '</h2><table border="1" cellpadding="5"><thead><tr><th>Mes</th><th>Conteo</th></tr></thead><tbody>';
        foreach ($lineLabels as $index => $label) {
            $html .= '<tr><td>' . htmlspecialchars($label) . '</td><td>' . htmlspecialchars($lineData[$index]) . '</td></tr>';
        }
        $html .= '</tbody></table>';
    }

    $conexion->close();

    generatePDF($html, 'charts.pdf');
}

function exportCharactersPDF($nombre) {
    if (!empty($_POST['htmlContent'])) {
        $htmlContent = $_POST['htmlContent'];
        generatePDF($htmlContent, 'personajes.pdf');
        return;
    }

    $conexion = conectarDB();
    $nombre = $conexion->real_escape_string($nombre);

    $query = "SELECT name, description FROM final_characters WHERE name LIKE '%$nombre%' LIMIT 100";
    $result = $conexion->query($query);

    $html = '<h1>Personajes Marvel</h1><table border="1" cellpadding="5"><thead><tr><th>Nombre</th><th>Descripción</th></tr></thead><tbody>';
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr><td>' . htmlspecialchars($row['name']) . '</td><td>' . htmlspecialchars($row['description']) . '</td></tr>';
    }
    $html .= '</tbody></table>';

    generatePDF($html, 'personajes.pdf');
}

function exportComicsPDF($nombre, $fecha) {
    if (!empty($_POST['htmlContent'])) {
        $htmlContent = $_POST['htmlContent'];
        generatePDF($htmlContent, 'comics.pdf');
        return;
    }

    $conexion = conectarDB();
    $nombre = $conexion->real_escape_string($nombre);
    $fecha = $conexion->real_escape_string($fecha);

    $query = "SELECT title, description, onsale_date FROM final_comics WHERE title LIKE '%$nombre%'";
    if (!empty($fecha)) {
        $query .= " AND onsale_date = '$fecha'";
    }
    $query .= " LIMIT 100";

    $result = $conexion->query($query);

    $html = '<h1>Comics Marvel</h1><table border="1" cellpadding="5"><thead><tr><th>Título</th><th>Descripción</th><th>Fecha de venta</th></tr></thead><tbody>';
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr><td>' . htmlspecialchars($row['title']) . '</td><td>' . htmlspecialchars($row['description']) . '</td><td>' . htmlspecialchars($row['onsale_date']) . '</td></tr>';
    }
    $html .= '</tbody></table>';

    generatePDF($html, 'comics.pdf');
}

function exportCreatorsPDF($nombre) {
    if (!empty($_POST['htmlContent'])) {
        $htmlContent = $_POST['htmlContent'];
        generatePDF($htmlContent, 'creadores.pdf');
        return;
    }

    $conexion = conectarDB();
    $nombre = $conexion->real_escape_string($nombre);

    $query = "SELECT full_name, role FROM final_creators WHERE full_name LIKE '%$nombre%' LIMIT 100";
    $result = $conexion->query($query);

    $html = '<h1>Creadores Marvel</h1><table border="1" cellpadding="5"><thead><tr><th>Nombre</th><th>Rol</th></tr></thead><tbody>';
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr><td>' . htmlspecialchars($row['full_name']) . '</td><td>' . htmlspecialchars($row['role']) . '</td></tr>';
    }
    $html .= '</tbody></table>';

    generatePDF($html, 'creadores.pdf');
}

$type = $_GET['type'] ?? '';

switch ($type) {
    case 'charts':
        exportChartsPDF();
        break;
    case 'characters':
        $nombre = $_GET['nombre'] ?? '';
        exportCharactersPDF($nombre);
        break;
    case 'comics':
        $nombre = $_GET['nombre'] ?? '';
        $fecha = $_GET['fecha'] ?? '';
        exportComicsPDF($nombre, $fecha);
        break;
    case 'creators':
        $nombre = $_GET['nombre'] ?? '';
        exportCreatorsPDF($nombre);
        break;
    default:
        header('HTTP/1.1 400 Bad Request');
        echo 'Invalid export type';
        break;
}

?>
