<?php
session_start();
include 'conexionDB.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errors[] = "Por favor, complete todos los campos.";
    } else {
        $conexion = conectarDB();

        $stmt = $conexion->prepare("SELECT id, username, password, admin FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['admin'] = $user['admin'];
                header('Location: main.php');
                exit();
            } else {
                $errors[] = "Contraseña incorrecta.";
            }
        } else {
            $errors[] = "Usuario no encontrado.";
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
    <title>Iniciar Sesión - Marvel Info</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col justify-center items-center p-4">
    <div class="w-full max-w-md bg-gray-800 p-6 rounded-lg shadow-lg">
        <h1 class="text-2xl font-semibold mb-4">Iniciar Sesión</h1>
        <?php if ($errors): ?>
            <div class="bg-red-600 p-3 rounded mb-4">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST" action="login.php" class="space-y-4">
            <div>
                <label for="username" class="block mb-1">Nombre de usuario</label>
                <input type="text" id="username" name="username" required class="w-full p-2 rounded bg-gray-700 text-white" value="<?php echo htmlspecialchars($_POST['username'] ?? '') ?>" />
            </div>
            <div>
                <label for="password" class="block mb-1">Contraseña</label>
                <input type="password" id="password" name="password" required class="w-full p-2 rounded bg-gray-700 text-white" />
            </div>
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 p-3 rounded font-semibold">Iniciar Sesión</button>
        </form>
        <p class="mt-4 text-center">
            ¿No tienes cuenta? <a href="registration.php" class="text-red-500 hover:underline">Regístrate</a>
        </p>
    </div>
</body>
</html>
