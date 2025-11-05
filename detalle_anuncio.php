<?php
ob_start();
session_start();

$title = "Detalle del anuncio";
require_once("cabecera.inc");
require_once("inicio.inc");

// ====================================
// COMPROBAR PÁGINA DE ORIGEN (referer)
// ====================================
if (isset($_SERVER['HTTP_REFERER'])) {
    $pagina_anterior = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
    // Si viene desde index_no.php → acceso desde la parte pública
    if ($pagina_anterior === 'index_no.php') {
        header("Location: aviso.php");
        exit;
    }
}

// ====================================
// CONTROL DE ACCESO: solo usuarios registrados
// ====================================
if (!isset($_SESSION['usuario'])) {
    // Intentar restaurar sesión con cookies válidas
    if (isset($_COOKIE['usuario']) && isset($_COOKIE['password'])) {
        $usuarios_validos = include("usuarios.php");
        foreach ($usuarios_validos as $u) {
            if ($u['usuario'] === $_COOKIE['usuario'] && $u['password'] === $_COOKIE['password']) {
                $_SESSION['usuario'] = $u['usuario'];
                break;
            }
        }
    }
}

// Si sigue sin haber sesión → redirigir al aviso
if (!isset($_SESSION['usuario'])) {
    header("Location: aviso.php");
    exit;
}
?>

<article>
  <div class="imagenes">
    <div class="imagen_principal">
      <img src="img/foto_piso.jpg" alt="Foto principal del anuncio">
    </div>
    <div class="imagen_secundaria">
      <img src="img/foto_piso1.jpg" alt="Foto adicional 1">
      <img src="img/foto_piso2.jpg" alt="Foto adicional 2">
    </div>
  </div>

  <fieldset>
    <legend>Descripción</legend>
    <dl>
      <dt>Tipo de anuncio</dt>
      <dd>Venta</dd>
      <dt>Tipo de vivienda</dt>
      <dd>Piso</dd>
      <dt>Detalles</dt>
      <dd>Vivienda luminosa y moderna con 3 habitaciones, 2 baños y balcón con vistas al centro.</dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Información del anuncio</legend>
    <dl>
      <dt>Fecha de publicación</dt>
      <dd>05/11/2025</dd>
      <dt>Ciudad</dt>
      <dd>Madrid</dd>
      <dt>País</dt>
      <dd>España</dd>
      <dt>Precio</dt>
      <dd>280.000 €</dd>
      <dt>Propietario</dt>
      <dd>Juan Pérez</dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Características</legend>
    <dl>
      <dt>Habitaciones</dt>
      <dd>3</dd>
      <dt>Baños</dt>
      <dd>2</dd>
      <dt>Superficie</dt>
      <dd>120 m²</dd>
      <dt>Terraza</dt>
      <dd>Sí</dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>¿Interesado?</legend>
    <p>
      <a class="enlaces" href="enviar_mensaje.php">Enviar mensaje al anunciante</a>
    </p>
  </fieldset>
</article>

</main>

<?php
require_once("footer.inc");
?>
