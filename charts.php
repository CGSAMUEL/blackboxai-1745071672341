<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Gráficos - Marvel Info</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: #1a1a1a;
            color: #f0f0f0;
            padding: 2rem;
        }
        .chart-container {
            background: #222;
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <h1 class="text-3xl font-bold mb-6">Gráficos de Marvel Info</h1>

    <div class="chart-container">
        <h2 class="text-xl mb-2">Gráfico de Columnas - Número de personajes por año modificado</h2>
        <canvas id="barChart"></canvas>
    </div>

    <div class="chart-container">
        <h2 class="text-xl mb-2">Gráfico de Tarta - Distribución de comics por formato</h2>
        <canvas id="pieChart"></canvas>
    </div>

    <div class="chart-container">
        <h2 class="text-xl mb-2">Gráfico de Líneas - Comics publicados por mes</h2>
        <canvas id="lineChart"></canvas>
    </div>

    <script>
        async function fetchChartData() {
            const response = await fetch('charts_data.php');
            const data = await response.json();

            return data;
        }

        const barConfig = {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Personajes modificados',
                    data: [],
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                }]
            },
            options: {}
        };

        const pieConfig = {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    label: 'Formatos',
                    data: [],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ]
                }]
            },
            options: {}
        };

        const lineConfig = {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Comics publicados',
                    data: [],
                    fill: false,
                    borderColor: 'rgba(255, 99, 132, 0.7)',
                    tension: 0.1
                }]
            },
            options: {}
        };

        const barConfig = {
            type: 'bar',
            data: barData,
            options: {}
        };

        const pieConfig = {
            type: 'pie',
            data: pieData,
            options: {}
        };

        const lineConfig = {
            type: 'line',
            data: lineData,
            options: {}
        };

        new Chart(
            document.getElementById('barChart'),
            barConfig
        );

        new Chart(
            document.getElementById('pieChart'),
            pieConfig
        );

        new Chart(
            document.getElementById('lineChart'),
            lineConfig
        );

        // Fetch data and update charts
        fetchChartData().then(data => {
            barConfig.data.labels = data.bar.labels;
            barConfig.data.datasets[0].data = data.bar.data;
            pieConfig.data.labels = data.pie.labels;
            pieConfig.data.datasets[0].data = data.pie.data;
            lineConfig.data.labels = data.line.labels;
            lineConfig.data.datasets[0].data = data.line.data;

            // Update charts
            window.barChart.update();
            window.pieChart.update();
            window.lineChart.update();
        });

        // Initialize charts globally for update
        window.barChart = new Chart(
            document.getElementById('barChart'),
            barConfig
        );

        window.pieChart = new Chart(
            document.getElementById('pieChart'),
            pieConfig
        );

        window.lineChart = new Chart(
            document.getElementById('lineChart'),
            lineConfig
        );
    </script>
</body>
</html>
