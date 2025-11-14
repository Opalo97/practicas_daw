<?php
session_start();
require_once("bd.php");

// Recuperamos usuario y contraseña del formulario
$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';

if ($usuario && $password) {
    $mysqli = obtenerConexion();

    // Consulta segura
    $stmt = $mysqli->prepare("SELECT IdUsuario, NomUsuario, Clave, Estilo FROM usuarios WHERE NomUsuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {

        if ($password === $fila['Clave']) {

            // Guardar sesión
            $_SESSION['usuario'] = $fila['NomUsuario'];
            $_SESSION['idusuario'] = $fila['IdUsuario'];

            // Guardar el estilo del usuario
            $estiloId = $fila['Estilo'];

            // Consultar el CSS del estilo
            $res = $mysqli->query("SELECT Fichero FROM estilos WHERE IdEstilo = $estiloId LIMIT 1");

            if ($row = $res->fetch_assoc()) {
                $_SESSION['estilo'] = $row['Fichero'];
            } else {
                $_SESSION['estilo'] = "basic.css"; // fallback
            }

            // Redirigir
            header("Location: index.php");
            exit;

        } else {
            $_SESSION['error_login'] = "Usuario o contraseña incorrectos.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error_login'] = "Usuario o contraseña incorrectos.";
        header("Location: login.php");
        exit;
    }

} else {
    $_SESSION['error_login'] = "Debes introducir usuario y contraseña.";
    header("Location: login.php");
    exit;
}
