<?php
$title = "Búsqueda de anuncios";
require_once("cabecera.inc");
require_once("inicio.inc");
?>

<section>
  <article>

    <form action="resultado.php" method="get">
      <fieldset>
        <legend>Tipo de anuncio</legend>
        <div class="radio-group">
          <input type="radio" id="venta" name="tipo" value="venta">
          <label for="venta">Venta</label>

          <input type="radio" id="alquiler" name="tipo" value="alquiler">
          <label for="alquiler">Alquiler</label>
        </div>
      </fieldset>

      <fieldset>
        <legend>Datos de la vivienda</legend>

        <label for="vivienda">Tipo de vivienda:</label>
        <select id="vivienda" name="vivienda">
          <option value="">Seleccione...</option>
          <option value="obra-nueva">Obra nueva</option>
          <option value="vivienda">Vivienda</option>
          <option value="oficina">Oficina</option>
          <option value="local">Local</option>
          <option value="garaje">Garaje</option>
        </select>

        <label for="ciudad">Ciudad:</label>
        <input type="text" id="ciudad" name="ciudad" />

        <label for="pais">País:</label>
        <select id="pais" name="pais">
          <option value="">Seleccione un país</option>
          <option value="espana">España</option>
          <option value="francia">Francia</option>
          <option value="italia">Italia</option>
          <option value="alemania">Alemania</option>
          <option value="portugal">Portugal</option>
          <option value="mexico">México</option>
          <option value="argentina">Argentina</option>
          <option value="chile">Chile</option>
          <option value="colombia">Colombia</option>
          <option value="peru">Perú</option>
        </select>

        <label>Precio:
          <input type="range" name="precio" min="50" max="2000">
        </label>

        <label>Fecha:
          <input type="text" name="fecha" placeholder="mm/aaaa"
            pattern="(0[1-9]|1[0-2])\/[0-9]{4}"
            title="Formato válido: mm/aaaa">
        </label>
      </fieldset>

      <input class="button" type="submit" value="Buscar" formaction="resultado.php" />
    </form>
  </article>
</section>

</main>

<?php
require_once("footer.inc");
?>
