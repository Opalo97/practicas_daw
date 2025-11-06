<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔹 Determinar estilo activo
$estilo_activo = $_SESSION['estilo'] ?? ($_COOKIE['estilo'] ?? "basic.css");
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo $title; ?> - Pisos & Inmuebles</title>

  <!-- Enlaces comunes -->
  <link rel="stylesheet" href="iconos/fontello-88eddae3/css/fontello.css">

  <!-- 🔸 Cargar el estilo preferido -->
  <link rel="stylesheet" href="<?php echo htmlspecialchars($estilo_activo); ?>" title="Modo Actual">

  <!-- 🔹 Modos alternativos -->
  <link rel="alternate stylesheet" href="modo-oscuro.css" title="Modo Oscuro">
  <link rel="stylesheet" href="forms.css">
  <link rel="alternate stylesheet" href="print.css" media="print">
  <link rel="alternate stylesheet" href="accesible.css" title="Accesible">
  <link rel="alternate stylesheet" href="alto-contraste.css" title="Modo Alto Contraste">
  <link rel="alternate stylesheet" href="letra_grande.css" title="Letra Grande">
</head>
