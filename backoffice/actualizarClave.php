<?php
include '../conexionDB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($id) || empty($password)) {
        echo "ID de usuario o contraseña vacía.";
        exit();
    }

    $conexion = conectarDB();

    // Hash the password before storing
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conexion->prepare($query);
    if (!$stmt) {
        echo "Error en la preparación de la consulta.";
        exit();
    }
    $stmt->bind_param('si', $password_hash, $id);

    if ($stmt->execute()) {
        echo "Contraseña actualizada correctamente.";
    } else {
        echo "Error al actualizar la contraseña.";
    }

    $stmt->close();
    $conexion->close();
} else {
    echo "Método no permitido.";
}
?>
