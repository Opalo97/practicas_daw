<?php
$title = "Detalle del anuncio";
require_once("cabecera.inc");
require_once("inicio.inc");

// Cargamos los anuncios desde un fichero PHP que devuelve un array asociativo
$anuncios = require("anuncios.php"); // por ejemplo: anuncios[1] y anuncios[2]

// Obtenemos el ID del anuncio de la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Elegimos el anuncio según si el ID es par o impar
if ($id % 2 == 0) {
    $anuncio = $anuncios[2]; // Anuncio para ID par
} else {
    $anuncio = $anuncios[1]; // Anuncio para ID impar
}
?>

<article>
  <div class="imagenes">
    <div class="imagen_principal">
      <img src="<?php echo $anuncio['foto_principal']; ?>" alt="Foto principal del anuncio">
    </div>
    <div class="imagen_secundaria">
      <?php foreach ($anuncio['fotos_secundarias'] as $foto): ?>
        <img src="<?php echo $foto; ?>" alt="Foto adicional">
      <?php endforeach; ?>
    </div>
  </div>

  <fieldset>
    <legend>Descripción</legend>
    <dl>
      <dt>Tipo de anuncio</dt>
      <dd><?php echo $anuncio['tipo_anuncio']; ?></dd>
      <dt>Tipo de vivienda</dt>
      <dd><?php echo $anuncio['tipo_vivienda']; ?></dd>
      <dt>Detalles</dt>
      <dd><?php echo $anuncio['detalles']; ?></dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Información del anuncio</legend>
    <dl>
      <dt>Fecha de publicación</dt>
      <dd><?php echo $anuncio['fecha']; ?></dd>
      <dt>Ciudad</dt>
      <dd><?php echo $anuncio['ciudad']; ?></dd>
      <dt>País</dt>
      <dd><?php echo $anuncio['pais']; ?></dd>
      <dt>Precio</dt>
      <dd><?php echo $anuncio['precio']; ?></dd>
      <dt>Propietario</dt>
      <dd><?php echo $anuncio['propietario']; ?></dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Características</legend>
    <dl>
      <?php foreach ($anuncio['caracteristicas'] as $clave => $valor): ?>
        <dt><?php echo $clave; ?></dt>
        <dd><?php echo $valor; ?></dd>
      <?php endforeach; ?>
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
