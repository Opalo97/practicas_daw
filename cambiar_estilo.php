<?php
session_start();

if (isset($_POST['estilo'])) {
    $nuevo_estilo = basename($_POST['estilo']); // Evita rutas extrañas
    $_SESSION['estilo'] = $nuevo_estilo;
    setcookie('estilo', $nuevo_estilo, time() + (90 * 24 * 60 * 60), '/', '', false, true);
}

// Volver a la página anterior
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
