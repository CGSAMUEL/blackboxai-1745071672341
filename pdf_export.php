<?php
require_once('vendor/autoload.php'); // Assuming you have installed TCPDF or similar library via Composer

use TCPDF;

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

// Example: Export characters list to PDF
$conexion = conectarDB();
$query = "SELECT name, description FROM final_characters LIMIT 100";
$result = $conexion->query($query);

$html = '<h1>Marvel Characters</h1><table border="1" cellpadding="5"><thead><tr><th>Name</th><th>Description</th></tr></thead><tbody>';
while ($row = $result->fetch_assoc()) {
    $html .= '<tr><td>' . htmlspecialchars($row['name']) . '</td><td>' . htmlspecialchars($row['description']) . '</td></tr>';
}
$html .= '</tbody></table>';

generatePDF($html, 'marvel_characters.pdf');
?>
