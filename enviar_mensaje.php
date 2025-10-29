<?php
$title = "Enviar mensaje al propietario";
require_once("cabecera.inc");
require_once("inicio.inc");
?>

<article>
  <section>

    <fieldset>
      <legend>Información</legend>
      <dl>
        <dt>Anuncio:</dt>
        <dd>Piso luminoso en el centro</dd>

        <dt>Mensaje a:</dt>
        <dd>jose@gmail.com</dd>
      </dl>
    </fieldset>
  </section>

  <section aria-labelledby="form-mensaje">
    <fieldset>
      <legend>Formulario</legend>

      <form class="enviar-mensaje" action="en-mensaje.php" method="post">
        <input type="hidden" name="id_anuncio" value="1234">

        <!-- Tipo de mensaje -->
        <div class="grupo-radios">
          <label><input type="radio" name="tipo" value="info"> Más información</label>
          <label><input type="radio" name="tipo" value="cita"> Solicitar una cita</label>
          <label><input type="radio" name="tipo" value="oferta"> Comunicar una oferta</label>
        </div>

        <!-- Mensaje -->
        <label for="texto">Escribe tu mensaje:</label>
        <textarea id="texto" name="texto" rows="7" cols="60"
          placeholder="Hola, estoy interesado/a en su anuncio..."></textarea>

        <!-- Botones -->
        <div class="acciones">
          <button class="button" type="submit">Enviar</button>
          <button class="button" type="reset">Limpiar</button>
          <a class="enlaces" href="detalle_anuncio.php">Cancelar</a>
        </div>
      </form>
    </fieldset>
  </section>
</article>

</main>

<?php
require_once("footer.inc");
?>
