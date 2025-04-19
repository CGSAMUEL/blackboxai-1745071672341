<?php
session_start();
include 'conexionDB.php';

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$conexion = conectarDB();

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_age = $_POST['age'] ?? null;
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($new_username) || empty($new_email)) {
        $errors[] = "El nombre de usuario y correo electrónico son obligatorios.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo electrónico no es válido.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    } else {
        // Check if username or email already exists for other users
        $stmt = $conexion->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->bind_param('ssi', $new_username, $new_email, $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "El nombre de usuario o correo electrónico ya está en uso por otro usuario.";
        } else {
            // Update user data
            if (!empty($new_password)) {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conexion->prepare("UPDATE users SET username = ?, email = ?, age = ?, password = ? WHERE id = ?");
                $update_stmt->bind_param('ssisi', $new_username, $new_email, $new_age, $password_hash, $user_id);
            } else {
                $update_stmt = $conexion->prepare("UPDATE users SET username = ?, email = ?, age = ? WHERE id = ?");
                $update_stmt->bind_param('ssii', $new_username, $new_email, $new_age, $user_id);
            }

            if ($update_stmt->execute()) {
                $success = "Perfil actualizado correctamente.";
                $_SESSION['username'] = $new_username;
            } else {
                $errors[] = "Error al actualizar el perfil.";
            }
            $update_stmt->close();
        }
        $stmt->close();
    }
}

// Fetch current user data
$stmt = $conexion->prepare("SELECT username, email, age FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Perfil - Marvel Info</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-md bg-gray-800 p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-semibold mb-4">Editar Perfil</h1>
        <?php if ($errors): ?>
            <div class="bg-red-600 p-3 rounded mb-4">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-600 p-3 rounded mb-4">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="profile.php" class="space-y-4">
            <div>
                <label for="username" class="block mb-1">Nombre de usuario</label>
                <input type="text" id="username" name="username" required class="w-full p-2 rounded bg-gray-700 text-white" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" />
            </div>
            <div>
                <label for="email" class="block mb-1">Correo electrónico</label>
                <input type="email" id="email" name="email" required class="w-full p-2 rounded bg-gray-700 text-white" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" />
            </div>
            <div>
                <label for="age" class="block mb-1">Edad</label>
                <input type="number" id="age" name="age" min="0" max="150" class="w-full p-2 rounded bg-gray-700 text-white" value="<?php echo htmlspecialchars($user['age'] ?? ''); ?>" />
            </div>
            <div>
                <label for="password" class="block mb-1">Nueva contraseña (dejar en blanco para no cambiar)</label>
                <input type="password" id="password" name="password" class="w-full p-2 rounded bg-gray-700 text-white" />
            </div>
            <div>
                <label for="confirm_password" class="block mb-1">Confirmar nueva contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" class="w-full p-2 rounded bg-gray-700 text-white" />
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 p-3 rounded font-semibold">Actualizar Perfil</button>
        </form>
        <p class="mt-4 text-center">
            <a href="dashboard.php" class="text-red-500 hover:underline">Volver al Dashboard</a>
        </p>
    </div>
</body>
</html>
