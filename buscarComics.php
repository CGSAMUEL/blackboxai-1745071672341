<?php
    
    // Script para conectarse a la base de datos
    include 'conexionDB.php';
    
    function obtenerComics($nombre_personaje, $fecha) {
        $conexion = conectarDB();
    
        $comics = [];
        $query = "select title, onsale_date, comic_id
                  from final_comics fcom
                  join final_comic_characters fcomchar
                  on fcom.id = fcomchar.comic_id
                  join final_characters fchar
                  on fchar.id = character_id
                  where name like ?";

        $nombre_busqueda= "%".$nombre_personaje."%";

        if ($fecha) {
            $query .= " and onsale_date = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param('ss', $nombre_busqueda, $fecha);
        } else {
            $stmt = $conexion->prepare($query);
            $stmt->bind_param('s', $nombre_busqueda);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();

        $comics = [];

        if ($resultado->num_rows > 0) {
            while ($comic = $resultado->fetch_assoc()) {
                $comics[] = $comic;
            }
        }
        
        $stmt->close();
        $conexion->close();

        return $comics;
    }
    
    // Obtener el nombre del personaje de la solicitud
    $nombre_personaje = isset($_GET['nombre']) ? $_GET['nombre'] : '';
    $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : '';

    // Llamar a la función para obtener los personajes
    $comics = obtenerComics($nombre_personaje, $fecha);
    
    // Mostrar los resultados
    if (count($comics) > 0) {
        echo "<ul>";
        foreach ($comics as $comic) {
            echo "<li>" . htmlspecialchars($comic['title']) . " (ID: " . $comic['comic_id'] . "). Published on ". $comic['onsale_date']."</li>";
        }
        echo "</ul>";
    } else {
        echo "No se encontraron comics para esos criterios.";
    }
?>
