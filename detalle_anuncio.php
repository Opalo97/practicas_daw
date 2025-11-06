<?php
ob_start();
session_start();

$title = "Detalle del anuncio";
require_once("cabecera.inc");
require_once("inicio.inc");

 
// comprobar si pagina anterior = index_no
 
if (isset($_SERVER['HTTP_REFERER'])) {
    $pagina_anterior = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
    // Si viene desde index_no.php → acceso desde la parte pública
    if ($pagina_anterior === 'index_no.php') {
        header("Location: aviso.php");
        exit;
    }
}

 
// solo usuarios registrados
 
if (!isset($_SESSION['usuario'])) {
    // restaurar sesión con cookies válidas
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

// si sigue sin haber sesión → redirigir a aviso
if (!isset($_SESSION['usuario'])) {
    header("Location: aviso.php");
    exit;
}


// guarda el anuncio en el panel de ultimos anuncios visitados
$id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$max_anuncios = 4; // guarda los 4 ultimos
$cookie_name = "ultimos_anuncios";
$anuncios = require("anuncios.php");

// Obtener datos  del anuncio actual
$anuncio_actual = [
    'id' => $id,
    'foto' => $anuncios[$id]['foto_principal'],
    'tipo_vivienda' => $anuncios[$id]['tipo_vivienda'],
    'ciudad' => $anuncios[$id]['ciudad'],
    'pais' => $anuncios[$id]['pais'],
    'precio' => $anuncios[$id]['precio']
];

// Leer cookie existente (si la hay)
$ultimos = isset($_COOKIE[$cookie_name]) ? json_decode($_COOKIE[$cookie_name], true) : [];

// Si este anuncio ya estaba, eliminarlo para evitar duplicados
$ultimos = array_filter($ultimos, fn($a) => $a['id'] != $id);

// Insertar el nuevo al principio
array_unshift($ultimos, $anuncio_actual);

// Limitar a 4 elementos
$ultimos = array_slice($ultimos, 0, $max_anuncios);

// Guardar cookie (1 semana)
setcookie($cookie_name, json_encode($ultimos), time() + 7 * 24 * 60 * 60, '/', '', false, true);

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
