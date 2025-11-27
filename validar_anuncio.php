<?php

if (!function_exists('limpiar')) {
    function limpiar($txt) {
        return trim($txt ?? '');
    }
}

$errores = [];

$tipo_anuncio  = limpiar($_POST['tipo_anuncio'] ?? '');
$tipo_vivienda = limpiar($_POST['tipo_vivienda'] ?? '');
$titulo        = limpiar($_POST['titulo'] ?? '');
$precio        = limpiar($_POST['precio'] ?? '');
$descripcion   = limpiar($_POST['descripcion'] ?? '');
$ciudad        = limpiar($_POST['ciudad'] ?? '');
$pais          = limpiar($_POST['pais'] ?? '');
$fecha         = limpiar($_POST['fecha'] ?? '');

$superficie    = ($_POST['superficie'] ?? '') !== '' ? (int)$_POST['superficie'] : null;
$habitaciones  = ($_POST['habitaciones'] ?? '') !== '' ? (int)$_POST['habitaciones'] : null;
$banos         = ($_POST['banos'] ?? '') !== '' ? (int)$_POST['banos'] : null;
$planta        = limpiar($_POST['planta'] ?? '');
$anio          = ($_POST['anio'] ?? '') !== '' ? (int)$_POST['anio'] : null;

$alt_foto      = limpiar($_POST['alt_foto'] ?? '');

// -----------------------
// VALIDACIONES
// -----------------------
if ($titulo === '')        $errores[] = "El título es obligatorio.";
if ($descripcion === '')   $errores[] = "La descripción es obligatoria.";
if ($tipo_anuncio === '')  $errores[] = "Selecciona un tipo de anuncio.";
if ($tipo_vivienda === '') $errores[] = "Selecciona un tipo de vivienda.";
if ($ciudad === '')        $errores[] = "La ciudad es obligatoria.";
if ($pais === '')          $errores[] = "Selecciona un país.";
if ($fecha === '')         $errores[] = "La fecha es obligatoria.";

if (!is_numeric($precio))  $errores[] = "El precio debe ser numérico.";

// Si no existe la variable, por defecto la foto es obligatoria
if (!isset($validar_foto)) {
    $validar_foto = true;
}

if ($validar_foto) {
    if (!isset($_FILES['foto_principal']) 
        || $_FILES['foto_principal']['error'] !== UPLOAD_ERR_OK) {
        $errores[] = "Debes subir una foto principal.";
    }
}
