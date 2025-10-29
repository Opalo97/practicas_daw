<?php
$title = "Mis anuncios";
require_once("cabecera.inc");
require_once("inicio.inc");
?>

<section>
  
  <p>A continuación se muestran tus anuncios activos en la plataforma:</p>

  <!-- ANUNCIO 1 -->
  <fieldset>
    <legend>Piso céntrico en Madrid</legend>
    <article class="anuncio-item">
      <div class="imagen_principal">
        <img src="img/foto_piso.jpg" alt="Piso en Madrid">
      </div>
      <div class="info-anuncio">
        <h4>Piso céntrico en Madrid</h4>
        <p><strong>Ciudad:</strong> Madrid</p>
        <p><strong>País:</strong> España</p>
        <p><strong>Precio:</strong> 250.000 €</p>
        <a class="enlaces" href="ver_anuncio.php">Ver detalle</a>
      </div>
    </article>
  </fieldset>

  <!-- ANUNCIO 2 -->
  <fieldset>
    <legend>Apartamento en París</legend>
    <article class="anuncio-item">
      <div class="imagen_principal" >
        <img src="img/foto_piso1.jpg" alt="Apartamento en París" >
      </div>
      <div class="info-anuncio">
        <h4>Apartamento en París</h4>
        <p><strong>Ciudad:</strong> París</p>
        <p><strong>País:</strong> Francia</p>
        <p><strong>Precio:</strong> 320.000 €</p>
        <a class="enlaces" href="ver_anuncio.php">Ver detalle</a>
      </div>
    </article>
  </fieldset>

  <!-- ANUNCIO 3 -->
  <fieldset>
    <legend>Ático con terraza en Lisboa</legend>
    <article class="anuncio-item">
      <div class="imagen_principal" >
        <img src="img/foto_piso5.jpeg" alt="Ático en Lisboa" >
      </div>
      <div class="info-anuncio">
        <h4>Ático con terraza en Lisboa</h4>
        <p><strong>Ciudad:</strong> Lisboa</p>
        <p><strong>País:</strong> Portugal</p>
        <p><strong>Precio:</strong> 270.000 €</p>
        <a class="enlaces" href="ver_anuncio.php">Ver detalle</a>
      </div>
    </article>
  </fieldset>

  <!-- ENLACE A AÑADIR FOTO -->
  <fieldset>
    <legend>Gestión de los anuncios</legend>
    <p>
      <a class="enlaces" href="anyadir_foto.php">Añadir foto a un anuncio</a>
    </p>
  </fieldset>
</section>

</main>

<?php
require_once("footer.inc");
?>
