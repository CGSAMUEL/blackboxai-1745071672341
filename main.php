<?php
session_start();

// Database connection
$conexion = mysqli_connect("dbserver", "grupo08", "Uas9Noo9Xe", "db_grupo08");
if (!$conexion) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
$user_status = "Anonymous";
if (isset($_SESSION['username'])) {
    $user_status = htmlspecialchars($_SESSION['username']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Marvel Info - Main</title>
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
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            border-bottom: 2px solid #ff4500;
        }
        .user-status {
            font-weight: 500;
            font-size: 1rem;
            color: #ff6347;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <header class="header">
        <h1 class="orbitron text-3xl select-none">Marvel Info</h1>
        <div class="user-status" title="User Status">
            Logged in as: <?php echo $user_status; ?>
        </div>
    </header>

    <main class="flex-grow p-8">
        <p class="text-lg text-gray-300">Welcome to the main page. Here you can display Marvel information and user-specific content.</p>
        <!-- Content to be added here -->
        <?php if (isset($_SESSION['username'])): ?>
            <section class="mt-8">
                <h2 class="text-2xl font-bold mb-4">Data Charts</h2>
                <p class="mb-4">Access the data charts below:</p>
                <a href="charts.php" class="btn-marvel inline-block px-4 py-2 rounded text-white font-semibold">View Charts</a>
            </section>
            <section class="mt-8">
                <h2 class="text-2xl font-bold mb-4">Download PDFs</h2>
                <p class="mb-4">Download charts and search results as PDF files:</p>
                <a href="pdf_export.php" class="btn-marvel inline-block px-4 py-2 rounded text-white font-semibold">Download PDF</a>
            </section>
        <?php else: ?>
            <p class="mt-8 text-red-500 font-semibold">Please log in to access charts and download PDFs.</p>
        <?php endif; ?>
    </main>

    <footer class="text-center text-gray-500 text-sm p-4 select-none border-t border-gray-700">
        &copy; 2024 Marvel Info. All rights reserved.
    </footer>
</body>
</html>
