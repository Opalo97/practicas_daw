<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Guarda un mensaje flash (solo dura hasta el próximo request)
 */
function set_flash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

/**
 * Recupera y borra el mensaje flash
 */
function get_flash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}
