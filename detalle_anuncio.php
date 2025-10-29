<?php
$title = "Piso luminoso en el centro";
require_once("cabecera.inc");
require_once("inicio.inc");
?>

<article>

  <div class="imagenes">
    <div class="imagen_principal">
      <img src="img/piso.jpg" alt="Foto principal del piso">
    </div>

    <div class="imagen_secundaria">
      <img src="img/piso1.jpg" alt="Foto adicional del piso">
      <img src="img/piso3.jpg" alt="Foto adicional del piso">
    </div>
  </div>

  <!-- DESCRIPCIÓN -->
  <fieldset>
    <legend>Descripción</legend>
    <dl>
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

      <dt>Propietario</dt>
      <dd>User</dd>
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

  <!-- CTA -->
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
