<?php
require_once __DIR__ . '/TCPDF-main/tcpdf.php';

$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->Write(0, '¡TCPDF está funcionando correctamente!');
$pdf->Output('test.pdf', 'I');