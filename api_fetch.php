<?php
// Script to fetch data from a public Marvel API and store it in the database
// This is a simplified example and should be adapted to the actual API and data structure

include 'conexionDB.php';

function fetchAndStoreCharacters() {
    $conexion = conectarDB();

    // Example API endpoint (replace with actual Marvel API endpoint and authentication)
    $apiUrl = "https://gateway.marvel.com/v1/public/characters?limit=100&apikey=YOUR_API_KEY";

    $response = file_get_contents($apiUrl);
    if ($response === FALSE) {
        die("Error fetching data from API");
    }

    $data = json_decode($response, true);
    if (!isset($data['data']['results'])) {
        die("Invalid API response");
    }

    $characters = $data['data']['results'];

    // Prepare insert statement
    $stmt = $conexion->prepare("INSERT INTO final_characters (id, name, description, modified, thumbnail) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description), modified=VALUES(modified), thumbnail=VALUES(thumbnail)");

    foreach ($characters as $char) {
        $id = $char['id'];
        $name = $char['name'];
        $description = $char['description'];
        $modified = date('Y-m-d H:i:s', strtotime($char['modified']));
        $thumbnail = $char['thumbnail']['path'] . '.' . $char['thumbnail']['extension'];

        $stmt->bind_param('issss', $id, $name, $description, $modified, $thumbnail);
        $stmt->execute();
    }

    $stmt->close();
    $conexion->close();

    echo "Characters data fetched and stored successfully.";
}

// Call the function
fetchAndStoreCharacters();
?>
