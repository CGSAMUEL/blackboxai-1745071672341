<?php
function conectarDB() {
    $host = "dbserver";
    $usuario = "grupo08";
    $password = "Uas9Noo9Xe";
    $baseDatos = "db_grupo08";

    $conexion = new mysqli($host, $usuario, $password, $baseDatos);

    if ($conexion->connect_error) {
        die("Error de conexión a la base de datos: " . $conexion->connect_error);
    }

    // Set charset to utf8mb4 for proper encoding
    $conexion->set_charset("utf8mb4");

    return $conexion;
}
?>
