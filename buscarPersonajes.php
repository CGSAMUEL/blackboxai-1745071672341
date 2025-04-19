<?php
    
    // Script para conectarse a la base de datos
    include 'conexionDB.php';

        
    
    /*
     * Función para obtener los personajes filtrados por nombre.
     * Este método obtiene el listado de personajes desde una conexión a la DB.
     */
    function obtenerPersonajesPorNombreDesdeDB($nombre_personaje, $modo_estricto) {
        // Obtener la conexión
        $conexion = conectarDB();

        // Evitar inyecciones SQL utilizando prepared statements
        $query = "SELECT * FROM final_characters WHERE name LIKE ?";
        $stmt = $conexion->prepare($query);
        $nombre_busqueda = $modo_estricto=="true" ? $nombre_personaje : "%" . $nombre_personaje . "%"; // Usar el porcentaje para buscar coincidencias parciales
        $stmt->bind_param('s', $nombre_busqueda);

        //error_log("Query: ".$query." / nombre_busqueda: ".$nombre_busqueda,3,"log.txt");

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

    // Llamar a la función para obtener los personajes
    $personajes = obtenerPersonajesPorNombreDesdeDB($nombre_personaje, $modo_estricto);

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
