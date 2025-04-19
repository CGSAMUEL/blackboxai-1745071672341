<?php
session_start();
include 'conexionDB.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "El correo electrónico no es válido.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Las contraseñas no coinciden.";
    } else {
        $conexion = conectarDB();

        // Check if username or email already exists
        $stmt = $conexion->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "El nombre de usuario o correo electrónico ya está en uso.";
        } else {
            // Insert new user
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conexion->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $insert_stmt->bind_param('sss', $username, $email, $password_hash);
            if ($insert_stmt->execute()) {
                $success = "Registro exitoso. Puedes iniciar sesión ahora.";
            } else {
                $errors[] = "Error al registrar el usuario.";
            }
            $insert_stmt->close();
        }
        $stmt->close();
        $conexion->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registro - Marvel Info</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-md bg-gray-800 p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-semibold mb-4">Registro de Usuario</h1>
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
        <form method="POST" action="registration.php" class="space-y-4">
            <div>
                <label for="username" class="block mb-1">Nombre de usuario</label>
                <input type="text" id="username" name="username" required class="w-full p-2 rounded bg-gray-700 text-white" value="<?php echo htmlspecialchars($_POST['username'] ?? '') ?>" />
            </div>
            <div>
                <label for="email" class="block mb-1">Correo electrónico</label>
                <input type="email" id="email" name="email" required class="w-full p-2 rounded bg-gray-700 text-white" value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>" />
            </div>
            <div>
                <label for="password" class="block mb-1">Contraseña</label>
                <input type="password" id="password" name="password" required class="w-full p-2 rounded bg-gray-700 text-white" />
            </div>
            <div>
                <label for="confirm_password" class="block mb-1">Confirmar contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="w-full p-2 rounded bg-gray-700 text-white" />
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 p-3 rounded font-semibold">Registrarse</button>
        </form>
        <p class="mt-4 text-center">
            ¿Ya tienes cuenta? <a href="login.php" class="text-red-500 hover:underline">Iniciar sesión</a>
        </p>
    </div>
</body>
</html>
