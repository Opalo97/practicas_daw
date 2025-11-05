<?php
ob_start();
session_start();

// borra variables de sesión
$_SESSION = [];

 
// borra cookie de sesión (PHPSESSID)
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// borra cookies "recordarme"
if (isset($_COOKIE['usuario'])) {
    setcookie('usuario', '', time() - 3600, '/');
}
if (isset($_COOKIE['password'])) {
    setcookie('password', '', time() - 3600, '/');
}

// destruir la sesión 
session_destroy();
 
// redirigir a la parte pública
header("Location: index_no.php");
exit;
