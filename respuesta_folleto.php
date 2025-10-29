<?php
$title = "Confirmación de la solicitud de folleto";
require_once("cabecera.inc");
require_once("inicio.inc");
?>

<section aria-labelledby="conf">
  <p>
    Hemos recibido tu solicitud para el envío postal de un folleto publicitario impreso.
    A continuación se muestra el resumen de la información facilitada. Si algún dato no es correcto,
    puedes volver al formulario y enviarlo de nuevo.
  </p>
</section>

<!-- Resumen de datos introducidos (EJEMPLO ESTÁTICO) -->
<section aria-labelledby="resumen">
  <h3 id="resumen">Resumen de la solicitud</h3>

  <fieldset>
    <legend>Datos de contacto</legend>
    <dl>
      <dt>Nombre y apellidos</dt>
      <dd>Ana Pérez García</dd>
      <dt>Correo electrónico</dt>
      <dd>ana.perez@gmail.com</dd>
      <dt>Teléfono</dt>
      <dd>600 123 456</dd>
      <dt>Texto adicional</dt>
      <dd>
        Por favor, incluir en el folleto que el piso está cerca del tranvía y del mercado central.
      </dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Dirección de envío</legend>
    <dl>
      <dt>Calle / Vía</dt>
      <dd>Av. de la Constitución</dd>
      <dt>Número</dt>
      <dd>12</dd>
      <dt>Código Postal</dt>
      <dd>03005</dd>
      <dt>Localidad</dt>
      <dd>Alicante</dd>
      <dt>Provincia</dt>
      <dd>Alicante</dd>
      <dt>País</dt>
      <dd>España</dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Opciones de impresión</legend>
    <dl>
      <dt>Color de la portada</dt>
      <dd>
        #ff0000
        &nbsp;<input type="color" value="#ff0000" disabled>
      </dd>

      <dt>Número de copias</dt>
      <dd>3</dd>
      <dt>Resolución (DPI)</dt>
      <dd>300</dd>
      <dt>Impresión</dt>
      <dd>Color</dd>
      <dt>Imprimir precio</dt>
      <dd>Sí</dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Anuncio y logística</legend>
    <dl>
      <dt>Anuncio seleccionado</dt>
      <dd>Piso céntrico 2 hab. Madrid</dd>
      <dt>Fecha aproximada de recepción</dt>
      <dd>2025-11-15</dd>
    </dl>
  </fieldset>
</section>

<!-- Coste fijo -->
<section aria-labelledby="coste">
  <h3 id="coste">Coste del folleto</h3>
  <fieldset>
    <legend>Importe total</legend>
    <dt>10 €</dt>
  </fieldset>
</section>

<section aria-labelledby="acciones">
  <p>
    <a class="enlaces" href="solicitar_folleto.php">Volver al formulario</a>
  </p>
</section>

</main>

<?php
require_once("footer.inc");
?>
