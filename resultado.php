<?php
$title = "Resultados de la búsqueda";
require_once("cabecera.inc");
require_once("inicio.inc");

// Recoger datos del formulario
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'No especificado';
$vivienda = isset($_GET['vivienda']) ? $_GET['vivienda'] : 'No especificado';
$ciudad = isset($_GET['ciudad']) ? $_GET['ciudad'] : 'No especificado';
$pais = isset($_GET['pais']) ? $_GET['pais'] : 'No especificado';
$precio = isset($_GET['precio']) ? $_GET['precio'] : 'No especificado';
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : 'No especificado';
?>

<section>
  <h3>Criterios de búsqueda introducidos</h3>
  <dl>
    <dt>Tipo de anuncio:</dt>
    <dd><?php echo htmlspecialchars($tipo); ?></dd>

    <dt>Tipo de vivienda:</dt>
    <dd><?php echo htmlspecialchars($vivienda); ?></dd>

    <dt>Ciudad:</dt>
    <dd><?php echo htmlspecialchars($ciudad); ?></dd>

    <dt>País:</dt>
    <dd><?php echo htmlspecialchars($pais); ?></dd>

    <dt>Precio máximo:</dt>
    <dd><?php echo htmlspecialchars($precio); ?> €</dd>

    <dt>Fecha:</dt>
    <dd><?php echo htmlspecialchars($fecha); ?></dd>
  </dl>
</section>

<section>
  <h3>Resultados de búsqueda (anuncios estáticos)</h3>

  <article>
    <div class="imagen_principal">
      <img src="img/foto_piso.jpg" alt="Piso en Madrid centro">
    </div>
    <h4>Piso en Madrid centro</h4>
    <p>Vivienda luminosa y moderna con 3 habitaciones, 2 baños y balcón con vistas al centro.</p>
    <p><a class="enlaces" href="detalle_anuncio.php?id=1">Ver detalle</a></p>
  </article>

  <article>
    <div class="imagen_principal">
      <img src="img/foto_piso1.jpg" alt="Apartamento en París">
    </div>
    <h4>Apartamento en París</h4>
    <p>Acogedor apartamento en el corazón de París, ideal para parejas o estancias cortas.</p>
    <p><a class="enlaces" href="detalle_anuncio.php?id=2">Ver detalle</a></p>
  </article>

  <article>
    <div class="imagen_principal">
      <img src="img/foto_piso2.jpg" alt="Casa en Roma">
    </div>
    <h4>Casa en Roma</h4>
    <p>Amplia casa familiar con jardín privado y excelente comunicación con el centro histórico.</p>
    <p><a class="enlaces" href="detalle_anuncio.php?id=1">Ver detalle</a></p>
  </article>

</section>

</main>

<?php
require_once("footer.inc");
?>
