<?php
    
    // Script para conectarse a la base de datos
    include 'conexionDB.php';

    /*
     * Función para obtener los creadores filtrados por nombre.
     * Este método obtiene el listado de creadores desde una conexión a la DB.
     */
    function obtenerCreadores($character_id, $nombre_creador) {
        // Obtener la conexión
        $conexion = conectarDB();

        // Evitar inyecciones SQL utilizando prepared statements
        $query = "SELECT DISTINCT fcr.full_name, fcr.id AS creator_id
                  FROM final_creators fcr
                  LEFT JOIN final_creator_characters fcc ON fcr.id = fcc.creator_id
                  LEFT JOIN final_characters fc ON fcc.character_id = fc.id";

        $params = [];
        $types = "";

        $whereClauses = [];

        if ($character_id !== null && $character_id !== '') {
            $whereClauses[] = "fc.id = ?";
            $types .= "i";
            $params[] = &$character_id;
        }

        if ($nombre_creador !== null && $nombre_creador !== '') {
            $whereClauses[] = "fcr.full_name LIKE ?";
            $types .= "s";
            $nombre_creador_like = "%" . $nombre_creador . "%";
            $params[] = &$nombre_creador_like;
        }

        if (count($whereClauses) > 0) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $stmt = $conexion->prepare($query);

        if ($types !== "") {
            $bind_names = [];
            $bind_names[] = $types;
            for ($i=0; $i<count($params); $i++) {
                $bind_names[] = &$params[$i];
            }
            call_user_func_array(array($stmt, 'bind_param'), $bind_names);
        }

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener los resultados
        $resultado = $stmt->get_result();

        // Arreglo para almacenar los creadores encontrados
        $creadores = [];

        if ($resultado->num_rows > 0) {
            while ($creador = $resultado->fetch_assoc()) {
                $creadores[] = $creador;
            }
        }

        // Cerrar la consulta y la conexión
        $stmt->close();
        $conexion->close();

        return $creadores;
    }

    // Obtener el nombre del creador y character_id de la solicitud (puede ser un formulario o parámetro GET)
    $character_id = isset($_GET['character_id']) ? $_GET['character_id'] : null;
    $nombre_creador = isset($_GET['nombre']) ? $_GET['nombre'] : '';

    // Debug output for received parameters
    error_log("buscarCreadores.php called with character_id: " . var_export($character_id, true) . ", nombre: " . var_export($nombre_creador, true));

    // Llamar a la función para obtener los creadores
    $creadores = obtenerCreadores($character_id, trim($nombre_creador));

    // Debug output for number of creators found
    error_log("Number of creators found: " . count($creadores));

    // Mostrar los resultados
    if (count($creadores) > 0) {
        echo "<ul>";
        foreach ($creadores as $creador) {
            echo "<li>" . htmlspecialchars($creador['full_name']) . " (ID: " . $creador['creator_id'] . ")</li>";
        }
        echo "</ul>";
    } else {
        echo "No se encontraron creadores con ese nombre.";
    }
?>
