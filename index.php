<?php
session_start();
if (isset($_GET['guest'])) {
    // Set anonymous user session
    $_SESSION['username'] = 'Anonymous';
    header('Location: main.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Marvel Info - Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #0f0f0f 100%);
            color: #f0f0f0;
        }
        .orbitron {
            font-family: 'Orbitron', sans-serif;
        }
        .btn-marvel {
            background: linear-gradient(90deg, #e62429, #ff4500);
            box-shadow: 0 0 10px #ff4500;
            transition: all 0.3s ease;
        }
        .btn-marvel:hover {
            background: linear-gradient(90deg, #ff4500, #e62429);
            box-shadow: 0 0 20px #ff6347;
            transform: scale(1.05);
        }
        .social-btn {
            transition: transform 0.3s ease;
        }
        .social-btn:hover {
            transform: scale(1.1);
        }
        /* Dynamic flicker animation for title */
        @keyframes flicker {
            0%, 19%, 21%, 23%, 25%, 54%, 56%, 100% {
                opacity: 1;
            }
            20%, 22%, 24%, 55% {
                opacity: 0.4;
            }
        }
        .flicker {
            animation: flicker 3s infinite;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-center items-center px-4">
    <header class="mb-12 text-center">
        <h1 class="orbitron text-5xl flicker mb-4 select-none">Marvel Info</h1>
        <p class="text-lg text-gray-300 max-w-xl mx-auto">Explore the universe of Marvel with anonymous access or create an account to unlock more features.</p>
    </header>

    <main class="w-full max-w-md space-y-6">
        <a href="?guest=1" class="btn-marvel block text-center py-3 rounded-lg font-semibold text-white shadow-lg hover:shadow-2xl">Continue as Guest</a>

        <a href="#register" class="btn-marvel block text-center py-3 rounded-lg font-semibold text-white shadow-lg hover:shadow-2xl">Register</a>

        <a href="#login" class="btn-marvel block text-center py-3 rounded-lg font-semibold text-white shadow-lg hover:shadow-2xl">Login</a>

        <div class="flex justify-center space-x-6 mt-6">
            <button aria-label="Login with Google" class="social-btn btn-marvel p-3 rounded-full shadow-lg hover:shadow-2xl" onclick="alert('Google login placeholder')">
                <i class="fab fa-google fa-lg"></i>
            </button>
            <button aria-label="Login with Facebook" class="social-btn btn-marvel p-3 rounded-full shadow-lg hover:shadow-2xl" onclick="alert('Facebook login placeholder')">
                <i class="fab fa-facebook-f fa-lg"></i>
            </button>
        </div>
    </main>

    <footer class="mt-16 text-center text-gray-500 text-sm select-none">
        &copy; 2024 Marvel Info. All rights reserved.
    </footer>
</body>
</html>
