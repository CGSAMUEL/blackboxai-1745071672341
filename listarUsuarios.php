<?php
	include 'conexionDB.php';

	function listarUsuarios()
	{
		$conexion = conectarDB();

		$query = "SELECT id, username, password
				  FROM users";

		$stmt = $conexion->prepare($query);
        $stmt->execute();

        $resultado = $stmt->get_result();

        $usuarios = [];

        if ($resultado->num_rows > 0) {
            while ($usuario = $resultado->fetch_assoc()) {
                $usuarios[] = $usuario;
            }
        }

        $stmt->close();
        $conexion->close();

        return $usuarios;
	}

	$usuarios = listarUsuarios();
    $desplegable = "<div class=\"desplegable\">
                        Usuario: <input type=\"text\" placeholder=\"Nuevo usuario\" data-id=\"{ID USUARIO}\" onkeyup=\"actualizarUsuario(event, this)\">
                        Contraseña: <input type=\"text\" placeholder=\"Nueva contraseña\" data-id=\"{ID USUARIO}\" onkeyup=\"actualizarClave(event, this)\">
                    </div>";

    if (count($usuarios) > 0) {
        echo "<ul>";
        foreach ($usuarios as $usuario) {
            $desplegable = str_replace("{ID USUARIO}", htmlspecialchars($usuario['id']), $desplegable);

            echo "<li class=\"usuario-item\">User #" . htmlspecialchars($usuario['id']) . "<br/>" . " <b>Username</b>: ". htmlspecialchars($usuario['username']). "<br/>" ."<b>Password: </b>". htmlspecialchars($usuario['password']) . $desplegable . "</li>";
        }
        echo "</ul>";
    } else {
        echo "Ha habido un problema al cargar los usuarios.";
    }
?>
