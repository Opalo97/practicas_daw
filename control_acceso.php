<?php

ob_start();
session_start();

// Recoger valores originales (sin trim) para devolver usuario en caso de error
$raw_usuario  = $_POST['usuario'] ?? null;
$raw_password = $_POST['password'] ?? null;

// Si no vienen por POST, redirigimos al login (acceso directo no permitido)
if ($raw_usuario === null || $raw_password === null) {
    header("Location: login.php");
    exit;
}

// Limpiar entradas
$usuario  = trim($raw_usuario);
$password = trim($raw_password);
$recordarme = isset($_POST['recordarme']); // checkbox

// Validar campos vacíos o con solo espacios
if ($usuario === '' || $password === '') {
    header("Location: login.php?error=vacio&user=" . urlencode($usuario));
    exit;
}

// Cargar lista de usuarios válidos
$usuarios_validos = include("usuarios.php");
if (!is_array($usuarios_validos)) {
    header("Location: login.php?error=credenciales");
    exit;
}

// Verificar credenciales
$autenticado = false;
foreach ($usuarios_validos as $u) {
    if ($u['usuario'] === $usuario && $u['password'] === $password) {
        $autenticado = true;
        break;
    }
}

// Acciones según resultado
if ($autenticado) {
    // Crear variable de sesión
    $_SESSION['usuario'] = $usuario;

    // Si el usuario marcó "recordarme"
    if ($recordarme) {
        $duracion = time() + (90 * 24 * 60 * 60); // 90 días
        setcookie('usuario', $usuario, $duracion, '/', '', false, true);
        setcookie('password', $password, $duracion, '/', '', false, true);
    } else {
        // Borrar cookies antiguas si existen
        setcookie('usuario', '', time() - 3600, '/');
        setcookie('password', '', time() - 3600, '/');
    }

    // Redirigir al área privada (puedes cambiar por tu index privado)
    header("Location: index.php");
    exit;
} else {
    // Credenciales incorrectas
    header("Location: login.php?error=credenciales&user=" . urlencode($usuario));
    exit;
}
