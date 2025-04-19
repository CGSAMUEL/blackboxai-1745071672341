<?php
include '../conexionDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $username = $_POST['username'] ?? '';

    if (empty($id) || empty($username)) {
        echo "ID de usuario o nombre de usuario vacío.";
        exit();
    }

    $conexion = conectarDB();

    $query = "UPDATE users SET username = ? WHERE id = ?";
    $stmt = $conexion->prepare($query);
    if (!$stmt) {
        echo "Error en la preparación de la consulta.";
        exit();
    }
    $stmt->bind_param('si', $username, $id);

    if ($stmt->execute()) {
        echo "Nombre de usuario actualizado correctamente.";
    } else {
        echo "Error al actualizar el nombre de usuario.";
    }

    $stmt->close();
    $conexion->close();
} else {
    echo "Método no permitido.";
}
?>
