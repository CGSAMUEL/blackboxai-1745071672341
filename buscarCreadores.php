<?php
    
    // Script para conectarse a la base de datos
    include 'conexionDB.php';

    /*
     * Función para obtener los creadores filtrados por nombre.
     * Este método obtiene el listado de creadores desde una conexión a la DB.
     */
    function obtenerCreadores($nombre_personaje, $nombre_creador) {
        // Obtener la conexión
        $conexion = conectarDB();

        $nombre_busqueda = "%" . $nombre_personaje. "%"; // Usar el porcentaje para buscar coincidencias parciales
        
        // Evitar inyecciones SQL utilizando prepared statements
        $query = "select full_name, creator_id
                  from final_creators fcre
                  join final_creator_characters fcrechar
                  on fcre.id = fcrechar.creator_id
                  join final_characters fchar
                  on fchar.id = character_id
                  where name like ?";

        if ($nombre_creador) {
            $query .= " and full_name like ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param('ss', $nombre_busqueda, $nombre_creador);
        } else {
            $stmt = $conexion->prepare($query);
            $stmt->bind_param('s', $nombre_busqueda);
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

    // Obtener el nombre del creador de la solicitud (puede ser un formulario o parámetro GET)
    $nombre_personaje = isset($_GET['nombre']) ? $_GET['nombre'] : '';
    $nombre_creador = isset($_GET['creador']) ? $_GET['creador'] : '';

    // Llamar a la función para obtener los creadores
    $creadores = obtenerCreadores($nombre_personaje, trim($nombre_creador));

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
