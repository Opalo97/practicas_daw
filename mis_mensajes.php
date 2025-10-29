<?php
$title = "Mis Mensajes";
require_once("cabecera.inc");
require_once("inicio.inc");
?>

<fieldset>
  <legend>Mensaje Enviado</legend>
  <dl>
    <dt>Tipo de mensaje</dt>
    <dd>Solicitar una cita</dd>

    <dt>Texto</dt>
    <dd>
      Buenos días, me pongo en contacto con usted porque estoy interesado en el piso anunciado en Madrid centro
      y me gustaría concertar una cita para poder visitarlo en persona; quedo a su disposición para adaptarme
      al horario que le resulte más conveniente y le agradezco de antemano su atención.
    </dd>

    <dt>Fecha</dt>
    <dd>16/05/2025</dd>

    <dt>Usuario receptor</dt>
    <dd>lolita@gmail.com</dd>
  </dl>
</fieldset>

<fieldset>
  <legend>Mensaje Recibido</legend>
  <dl>
    <dt>Tipo de mensaje</dt>
    <dd>Comunicar una oferta</dd>

    <dt>Texto</dt>
    <dd>
      Estimada Petunia, le agradezco mucho su interés en mi vivienda y quería informarle de que, teniendo en cuenta
      que el baño secundario cuenta con unas dimensiones algo más reducidas que el baño principal de la casa, he
      decidido aplicar una rebaja en el precio del alquiler como gesto de transparencia y para que la oferta
      resulte aún más atractiva; quedo a su disposición para cualquier consulta adicional o para concretar una
      visita cuando le venga bien.
    </dd>

    <dt>Fecha</dt>
    <dd>23/05/2025</dd>

    <dt>Usuario emisor</dt>
    <dd>pere@gmail.com</dd>
  </dl>
</fieldset>

</main>

<?php
require_once("footer.inc");
?>
