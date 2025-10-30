<?php
// control_acceso.php
// Recibe POST desde login.php y valida los campos y credenciales.
// No usa sesiones (requisito de la práctica).

// Recoger valores originales (sin trim) para permitir devolver el usuario en caso de error
$raw_usuario = isset($_POST['usuario']) ? $_POST['usuario'] : null;
$raw_password = isset($_POST['password']) ? $_POST['password'] : null;

// Si no vienen por POST, redirigimos al login (evita accesos directos)
if ($raw_usuario === null || $raw_password === null) {
    header("Location: login.php");
    exit;
}

// Trim para comprobar contenido real
$usuario = trim($raw_usuario);
$password = trim($raw_password);

// 1) Comprobar que no estén vacíos (después de trim)
// Esto evita campos vacíos y también entradas que sean solo espacios/tabuladores/newlines
if ($usuario === '' || $password === '') {
    // Devolvemos también el usuario original sin espacios al GET para rellenar el campo
    $user_for_get = urlencode($usuario);
    header("Location: login.php?error=vacio&user={$user_for_get}");
    exit;
}

// Nota: la comprobación anterior ya cubre "solo espacios o tabuladores" porque trim() convierte eso en ''.
// Si por alguna razón quieres detectar estrictamente solo espacios/tab, se podría usar una regex sobre raw values.

// 2) Cargar lista de usuarios válidos
// usuarios.php devuelve un array con los usuarios permitidos
$usuarios_validos = include("usuarios.php");
if (!is_array($usuarios_validos)) {
    // Error en fichero de usuarios
    header("Location: login.php?error=credenciales");
    exit;
}

// 3) Comparar credenciales
$autenticado = false;
foreach ($usuarios_validos as $u) {
    // Comparación exacta (sensible a mayúsculas/minúsculas)
    if ($u['usuario'] === $usuario && $u['password'] === $password) {
        $autenticado = true;
        break;
    }
}

// 4) Redirecciones según resultado
if ($autenticado) {
    // Éxito: redirige al menú privado. Pasamos el nombre de usuario en GET para mostrar bienvenida.
    header("Location: index.php?usuario=" . urlencode($usuario));
    exit;
} else {
    // Credenciales incorrectas: redirige al login con mensaje y reponer usuario
    header("Location: login.php?error=credenciales&user=" . urlencode($usuario));
    exit;
}
