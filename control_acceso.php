<?php
ob_start();
session_start();
require_once("flashdata.php");

// Recoger valores originales (sin trim)
$raw_usuario  = $_POST['usuario'] ?? null;
$raw_password = $_POST['password'] ?? null;

if ($raw_usuario === null || $raw_password === null) {
    header("Location: login.php");
    exit;
}

$usuario  = trim($raw_usuario);
$password = trim($raw_password);
$recordarme = isset($_POST['recordarme']);

// Validar campos vacíos
if ($usuario === '' || $password === '') {
    set_flash('error', 'Por favor, rellena ambos campos.');
    set_flash('user', $usuario);
    header("Location: login.php");
    exit;
}

// Cargar lista de usuarios válidos
$usuarios_validos = include("usuarios.php");
if (!is_array($usuarios_validos)) {
    set_flash('error', 'Error al cargar credenciales.');
    header("Location: login.php");
    exit;
}

// Comprobar credenciales
$autenticado = false;
$usuario_info = null;

foreach ($usuarios_validos as $u) {
    if ($u['usuario'] === $usuario && $u['password'] === $password) {
        $autenticado = true;
        $usuario_info = $u;
        break;
    }
}

if ($autenticado) {
    // Guardar datos de sesión
    $_SESSION['usuario'] = $usuario;

    // 🔹 Asignar estilo (cada usuario puede tener uno en usuarios.php)
    $estilo = "basic.css"; // Estilo por defecto
    if (isset($usuario_info['estilo']) && $usuario_info['estilo'] !== '') {
        $estilo = $usuario_info['estilo'];
    }
    $_SESSION['estilo'] = $estilo;

    // 🔹 Si el usuario marcó "Recordarme"
    if ($recordarme) {
        $duracion = time() + (90 * 24 * 60 * 60); // 90 días

        // Recuperar la última visita anterior (si existe)
        $ultima_visita_anterior = $_COOKIE['ultima_visita'] ?? null;

        // Guardar cookies de sesión recordada
        setcookie('usuario', $usuario, $duracion, '/', '', false, true);
        setcookie('password', $password, $duracion, '/', '', false, true);
        setcookie('estilo', $estilo, $duracion, '/', '', false, true);

        // Guardar nueva fecha y hora de acceso actual
        setcookie('ultima_visita', date('d/m/Y H:i:s'), $duracion, '/', '', false, true);

        // Si había una visita anterior, la mostramos en la sesión
        if ($ultima_visita_anterior) {
            $_SESSION['ultima_visita'] = $ultima_visita_anterior;
        }
    } else {
        // 🔸 Si no se marcó "recordarme", eliminar posibles cookies antiguas
        setcookie('usuario', '', time() - 3600, '/');
        setcookie('password', '', time() - 3600, '/');
        setcookie('estilo', '', time() - 3600, '/');
        setcookie('ultima_visita', '', time() - 3600, '/');
    }

    // Redirigir al inicio o zona privada
    header("Location: index.php");
    exit;

} else {
    // Credenciales incorrectas → flashdata
    set_flash('error', 'Usuario o contraseña incorrectos.');
    set_flash('user', $usuario);
    header("Location: login.php");
    exit;
}
