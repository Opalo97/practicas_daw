<?php
$title = "Ver detalles de mi anuncio";
require_once("cabecera.inc");
require_once("inicio.inc");



// guarda el anuncio en el panel de ultimos anuncios visitados

$id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$max_anuncios = 4;
$cookie_name = "ultimos_anuncios";
$anuncios = require("anuncios.php");

if (!isset($anuncios[$id])) {
    $id = 1;
}

$anuncio_actual = [
    'id' => $id,
    'foto' => $anuncios[$id]['foto_principal'],
    'tipo_vivienda' => $anuncios[$id]['tipo_vivienda'],
    'ciudad' => $anuncios[$id]['ciudad'],
    'pais' => $anuncios[$id]['pais'],
    'precio' => $anuncios[$id]['precio'],
    'pagina' => 'ver' // 👈 marca este como anuncio propio
];

$ultimos = isset($_COOKIE[$cookie_name]) ? json_decode($_COOKIE[$cookie_name], true) : [];
$ultimos = array_filter($ultimos, function($a) use ($id) {
    // Si no existe la clave 'pagina', lo tratamos como diferente
    return $a['id'] != $id || (!isset($a['pagina']) || $a['pagina'] != 'ver');
});

array_unshift($ultimos, $anuncio_actual);
$ultimos = array_slice($ultimos, 0, $max_anuncios);
setcookie($cookie_name, json_encode($ultimos), time() + 7 * 24 * 60 * 60, '/', '', false, true);

?>

<article>

  <div class="imagenes">
    <div class="imagen_principal">
      <img src="img/piso4.jpg" alt="Foto principal del piso">
    </div>

    <div class="imagen_secundaria">
      <img src="img/piso3.jpg" alt="Foto adicional del piso">
      <img src="img/piso5.jpg" alt="Foto adicional del piso">
    </div>
  </div>

  <!-- DESCRIPCIÓN -->
  <fieldset>
    <legend>Descripción</legend>
    <dl>
      <dt>Título</dt>
      <dd>Ático en Alicante</dd>

      <dt>Tipo de anuncio</dt>
      <dd>Alquiler</dd>

      <dt>Tipo de vivienda</dt>
      <dd>Vivienda</dd>

      <dt>Detalles</dt>
      <dd>
        Vivienda reformada con 3 habitaciones y 2 baños. Salón-comedor con salida a balcón, cocina
        equipada y orientación sur. Finca con ascensor. Ideal para entrar a vivir.
      </dd>
    </dl>
  </fieldset>

  <!-- INFORMACIÓN DEL ANUNCIO -->
  <fieldset>
    <legend>Información del anuncio</legend>
    <dl>
      <dt>Fecha de publicación</dt>
      <dd><time datetime="2025-05">05/2025</time></dd>

      <dt>Ciudad</dt>
      <dd>Alicante</dd>

      <dt>País</dt>
      <dd>España</dd>

      <dt>Precio</dt>
      <dd>235.000 €</dd>

    </dl>
  </fieldset>

  <!-- CARACTERÍSTICAS -->
  <fieldset>
    <legend>Características</legend>
    <dl>
      <dt>Superficie</dt>
      <dd>95 m²</dd>

      <dt>Habitaciones</dt>
      <dd>3</dd>

      <dt>Baños</dt>
      <dd>2</dd>

      <dt>Planta</dt>
      <dd>4ª</dd>

      <dt>Año de construcción</dt>
      <dd>2008</dd>

      <dt>Ascensor</dt>
      <dd>Sí</dd>

      <dt>Balcón</dt>
      <dd>Sí</dd>
    </dl>
  </fieldset>

   <!-- ENLACE A AÑADIR FOTO -->
  <fieldset>
    <legend>Gestión del anuncio</legend>
    <p>
      <a class="enlaces" href="anyadir_foto.php?anuncio_id=123">Añadir foto a este anuncio</a>
    </p>
  </fieldset>

</article>

</main>


<?php
require_once("footer.inc");
?>
