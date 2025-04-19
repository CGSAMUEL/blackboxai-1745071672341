<?php
session_start();

// Check if user is admin (for now, simple check, can be improved)
if (!isset($_SESSION['username']) || !isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Back Office - User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #1a1a1a;
            color: #f0f0f0;
            padding: 2rem;
        }
        .btn-marvel {
            background: linear-gradient(90deg, #e62429, #ff4500);
            box-shadow: 0 0 10px #ff4500;
            transition: all 0.3s ease;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
        }
        .btn-marvel:hover {
            background: linear-gradient(90deg, #ff4500, #e62429);
            box-shadow: 0 0 20px #ff6347;
            transform: scale(1.05);
        }
        .user-list {
            margin-top: 1rem;
        }
        .usuario-item {
            background: #222;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0.375rem;
        }
        .desplegable input {
            margin: 0.25rem 0.5rem 0.25rem 0;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            border: none;
            width: 200px;
        }
    </style>
    <script>
        function actualizarUsuario(event, input) {
            if (event.key === 'Enter') {
                const userId = input.getAttribute('data-id');
                const newUsername = input.value;
                fetch('actualizarUsuario.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(userId) + '&username=' + encodeURIComponent(newUsername)
                }).then(response => response.text())
                .then(data => alert(data))
                .catch(err => alert('Error: ' + err));
            }
        }
        function actualizarClave(event, input) {
            if (event.key === 'Enter') {
                const userId = input.getAttribute('data-id');
                const newPassword = input.value;
                fetch('actualizarClave.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=' + encodeURIComponent(userId) + '&password=' + encodeURIComponent(newPassword)
                }).then(response => response.text())
                .then(data => alert(data))
                .catch(err => alert('Error: ' + err));
            }
        }
    </script>
</head>
<body>
    <h1 class="text-3xl font-bold mb-4">User Management - Back Office</h1>
    <div class="user-list">
        <?php include '../listarUsuarios.php'; ?>
    </div>
</body>
</html>
