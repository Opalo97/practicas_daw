<?php
$title = "Resultados de búsqueda";
require_once("cabecera.inc");
require_once("inicio.inc");
?>

<section>

  <!-- ANUNCIO 1 -->
  <fieldset>
    <legend>Piso en Madrid centro</legend>
    <a href="detalle_anuncio.php">
      <div class="imagenes">
        <div class="imagen_principal">
          <img src="img/foto_piso.jpg" alt="Foto principal del piso">
        </div>

        <div class="imagen_secundaria">
          <img src="img/foto_piso.jpg" alt="Foto adicional del piso">
          <img src="img/foto_piso.jpg" alt="Foto adicional del piso">
        </div>
      </div>
    </a>
    <dl>
      <dt>Fecha</dt>
      <dd>15/09/2025</dd>

      <dt>Ciudad</dt>
      <dd>Madrid</dd>

      <dt>País</dt>
      <dd>España</dd>

      <dt>Precio</dt>
      <dd>250.000 €</dd>
    </dl>
  </fieldset>

  <!-- ANUNCIO 2 -->
  <fieldset>
    <legend>Apartamento en París</legend>
    <a href="aviso.php">
      <div class="imagenes">
        <div class="imagen_principal">
          <img src="img/foto_piso1.jpg" alt="Foto principal del piso">
        </div>

        <div class="imagen_secundaria">
          <img src="img/foto_piso1.jpg" alt="Foto adicional del piso">
          <img src="img/foto_piso1.jpg" alt="Foto adicional del piso">
        </div>
      </div>
    </a>
    <dl>
      <dt>Fecha</dt>
      <dd>10/09/2025</dd>

      <dt>Ciudad</dt>
      <dd>París</dd>

      <dt>País</dt>
      <dd>Francia</dd>

      <dt>Precio</dt>
      <dd>350.000 €</dd>
    </dl>
  </fieldset>
</section>

</main>

<?php
require_once("footer.inc");
?>
