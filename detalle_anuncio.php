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

// Cargar datos de anuncios desde archivo externo (usa claves 1 y 2)
$anuncios = require("anuncios.php");

// Seleccionar anuncio según si el id es par o impar
// como tu anuncios.php usa claves numéricas 1 y 2, mapear a 1/2
$key = ($id % 2 === 0) ? 2 : 1;
if (!isset($anuncios[$key])) {
    // fallback si algo falla
    $key = 1;
}
$anuncio = $anuncios[$key];

// Guardar en cookie de últimos anuncios (usar los datos del anuncio seleccionado)
$anuncio_actual = [
    'id' => $id,
    'foto' => $anuncio['foto_principal'],
    'tipo_vivienda' => $anuncio['tipo_vivienda'] ?? ($anuncio['tipo_vivienda'] ?? ''),
    'ciudad' => $anuncio['ciudad'],
    'pais' => $anuncio['pais'],
    'precio' => $anuncio['precio']
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
      <img src="<?= htmlspecialchars($anuncio['foto_principal'], ENT_QUOTES, 'UTF-8') ?>" alt="Foto principal del anuncio">
    </div>
    <div class="imagen_secundaria">
      <?php
      // tus anuncios usan 'fotos_secundarias'
      if (!empty($anuncio['fotos_secundarias']) && is_array($anuncio['fotos_secundarias'])):
          foreach ($anuncio['fotos_secundarias'] as $f): ?>
            <img src="<?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8') ?>" alt="Foto adicional">
      <?php endforeach;
      endif;
      ?>
    </div>
  </div>

  <fieldset>
    <legend>Descripción</legend>
    <dl>
      <dt>Tipo de anuncio</dt>
      <dd><?= htmlspecialchars($anuncio['tipo_anuncio'], ENT_QUOTES, 'UTF-8') ?></dd>
      <dt>Tipo de vivienda</dt>
      <dd><?= htmlspecialchars($anuncio['tipo_vivienda'], ENT_QUOTES, 'UTF-8') ?></dd>
      <dt>Detalles</dt>
      <dd><?= htmlspecialchars($anuncio['detalles'], ENT_QUOTES, 'UTF-8') ?></dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Información del anuncio</legend>
    <dl>
      <dt>Fecha de publicación</dt>
      <dd><?= htmlspecialchars($anuncio['fecha'], ENT_QUOTES, 'UTF-8') ?></dd>
      <dt>Ciudad</dt>
      <dd><?= htmlspecialchars($anuncio['ciudad'], ENT_QUOTES, 'UTF-8') ?></dd>
      <dt>País</dt>
      <dd><?= htmlspecialchars($anuncio['pais'], ENT_QUOTES, 'UTF-8') ?></dd>
      <dt>Precio</dt>
      <dd><?= htmlspecialchars($anuncio['precio'], ENT_QUOTES, 'UTF-8') ?></dd>
      <dt>Propietario</dt>
      <dd><?= htmlspecialchars($anuncio['propietario'], ENT_QUOTES, 'UTF-8') ?></dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Características</legend>
    <dl>
      <?php
      if (!empty($anuncio['caracteristicas']) && is_array($anuncio['caracteristicas'])):
        foreach ($anuncio['caracteristicas'] as $clave => $valor): ?>
          <dt><?= htmlspecialchars($clave, ENT_QUOTES, 'UTF-8') ?></dt>
          <dd><?= htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') ?></dd>
      <?php endforeach;
      endif;
      ?>
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
