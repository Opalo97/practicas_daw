<?php
ob_start();
session_start();

// Borrar todas las variables de sesión
$_SESSION = [];

// Destruir la sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// 🔹 Borrar cookies de recordarme
setcookie('usuario', '', time() - 3600, '/');
setcookie('password', '', time() - 3600, '/');
setcookie('estilo', '', time() - 3600, '/');
setcookie('ultima_visita', '', time() - 3600, '/');

// Redirigir a la parte pública
header("Location: index_no.php");
exit;
?>
