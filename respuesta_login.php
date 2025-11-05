<?php
ob_start();
session_start();

$usuarios = require_once("../includes/usuarios.php");

$usuario = $_POST['usuario'] ?? '';
$password = $_POST['password'] ?? '';
$recordarme = isset($_POST['recordarme']);

$valido = false;

foreach ($usuarios as $u) {
    if ($u['usuario'] === $usuario && $u['password'] === $password) {
        $valido = true;
        break;
    }
}

if ($valido) {
    $_SESSION['usuario'] = $usuario;

    if ($recordarme) {
        // caduca en 90 días
        $duracion = time() + (90 * 24 * 60 * 60);
        setcookie('usuario', $usuario, $duracion, '/', '', false, true);
        setcookie('password', $password, $duracion, '/', '', false, true);
    } else {
        // eliminar cookies si no se marca
        setcookie('usuario', '', time() - 3600, '/');
        setcookie('password', '', time() - 3600, '/');
    }

    header("Location: ../privado/index.php");
    exit;
} else {
    header("Location: login.php?error=1");
    exit;
}
