<?php
session_start();
require_once("bd.php");
require_once('flashdata.php');

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

        // Verificar contraseña usando password_verify()
        // Compara el texto plano con el hash almacenado en BD
        // Funciona con contraseñas hasheadas mediante password_hash()
        if (password_verify($password, $fila['Clave'])) {

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
            set_flash('error', "Usuario o contraseña incorrectos.");
            set_flash('user', $usuario);
            header("Location: login.php");
            exit;
        }
    } else {
        set_flash('error', "Usuario o contraseña incorrectos.");
        set_flash('user', $usuario);
        header("Location: login.php");
        exit;
    }

} else {
    set_flash('error', "Debes introducir usuario y contraseña.");
    set_flash('user', $usuario);
    header("Location: login.php");
    exit;
}
