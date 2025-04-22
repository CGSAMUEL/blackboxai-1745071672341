<?php
    
    // Script para conectarse a la base de datos
    include 'conexionDB.php';

        
    
    /*
     * Función para obtener los personajes filtrados por nombre.
     * Este método obtiene el listado de personajes desde una conexión a la DB.
     */
    function obtenerPersonajesPorNombreDesdeDB($nombre_personaje, $modo_estricto, $creator_id = null) {
        // Obtener la conexión
        $conexion = conectarDB();

        // Construir la consulta base con join para filtrar por creador si se proporciona
        $query = "SELECT DISTINCT fc.* FROM final_characters fc
                  LEFT JOIN final_creator_characters fcc ON fc.id = fcc.character_id
                  LEFT JOIN final_creators fcr ON fcc.creator_id = fcr.id
                  WHERE fc.name LIKE ?";

        // Parámetros para bind_param
        $params = [];
        $types = "s";
        $nombre_busqueda = $modo_estricto=="true" ? $nombre_personaje : "%" . $nombre_personaje . "%";
        $params[] = &$nombre_busqueda;

        if ($creator_id !== null && $creator_id !== '') {
            $query .= " AND fcr.id = ?";
            $types .= "i";
            $params[] = &$creator_id;
        }

        $stmt = $conexion->prepare($query);
        // Bind parameters dynamically with references
        $bind_names = [];
        $bind_names[] = $types;
        for ($i=0; $i<count($params); $i++) {
            $bind_names[] = &$params[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $bind_names);

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener los resultados
        $resultado = $stmt->get_result();

        // Arreglo para almacenar los personajes encontrados
        $personajes = [];

        if ($resultado->num_rows > 0) {
            while ($personaje = $resultado->fetch_assoc()) {
                $personajes[] = $personaje;
            }
        }

        // Cerrar la consulta y la conexión
        $stmt->close();
        $conexion->close();

        return $personajes;
    }


    // Obtener el nombre del personaje de la solicitud (puede ser un formulario o parámetro GET)
    $nombre_personaje = isset($_GET['nombre']) ? $_GET['nombre'] : '';
    $modo_estricto = isset($_GET['strict']) ? $_GET['strict'] : false;
    $creator_id = isset($_GET['creator_id']) ? $_GET['creator_id'] : null;

    // Llamar a la función para obtener los personajes
    $personajes = obtenerPersonajesPorNombreDesdeDB($nombre_personaje, $modo_estricto, $creator_id);

    // Mostrar los resultados
    if (count($personajes) > 0) {
        echo "<ul>";
        foreach ($personajes as $personaje) {
            echo "<li>" . htmlspecialchars($personaje['name']) . " (ID: " . $personaje['id'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "No se encontraron personajes con ese nombre.";
    }
    
    
?>
