<?php
$title = "Búsqueda de anuncios";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php"); 

// ---------------- Cargar datos de la BD ----------------
$mysqli = obtenerConexion();

// Tipos de anuncio
$tiposAnuncio = [];
$sqlTA = "SELECT IdTAnuncio, NomTAnuncio FROM TiposAnuncios ORDER BY NomTAnuncio";
if ($resTA = $mysqli->query($sqlTA)) {
    while ($fila = $resTA->fetch_assoc()) {
        $tiposAnuncio[] = $fila;
    }
    $resTA->free();
}

// Tipos de vivienda
$tiposVivienda = [];
$sqlTV = "SELECT IdTVivienda, NomTVivienda FROM TiposViviendas ORDER BY NomTVivienda";
if ($resTV = $mysqli->query($sqlTV)) {
    while ($fila = $resTV->fetch_assoc()) {
        $tiposVivienda[] = $fila;
    }
    $resTV->free();
}

// Países
$paises = [];
$sqlP = "SELECT IdPais, NomPais FROM Paises ORDER BY NomPais";
if ($resP = $mysqli->query($sqlP)) {
    while ($fila = $resP->fetch_assoc()) {
        $paises[] = $fila;
    }
    $resP->free();
}

$mysqli->close();
?>

<section>
  <article>

    <form action="resultado.php" method="get">
      <fieldset>
        <legend>Tipo de anuncio</legend>

        <label for="tipo">Tipo de anuncio:</label>
        <select id="tipo" name="tipo">
          <option value="">Seleccione...</option>
          <?php if (!empty($tiposAnuncio)): ?>
            <?php foreach ($tiposAnuncio as $ta): ?>
              <option value="<?php echo (int)$ta['IdTAnuncio']; ?>">
                <?php echo htmlspecialchars($ta['NomTAnuncio'], ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="">(No hay tipos de anuncio en la BD)</option>
          <?php endif; ?>
        </select>
      </fieldset>

      <fieldset>
        <legend>Datos de la vivienda</legend>

        <label for="vivienda">Tipo de vivienda:</label>
        <select id="vivienda" name="vivienda">
          <option value="">Seleccione...</option>
          <?php if (!empty($tiposVivienda)): ?>
            <?php foreach ($tiposVivienda as $tv): ?>
              <option value="<?php echo (int)$tv['IdTVivienda']; ?>">
                <?php echo htmlspecialchars($tv['NomTVivienda'], ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="">(No hay tipos de vivienda en la BD)</option>
          <?php endif; ?>
        </select>

        <label for="ciudad">Ciudad:</label>
        <input type="text" id="ciudad" name="ciudad" />

        <label for="pais">País:</label>
        <select id="pais" name="pais">
          <option value="">Seleccione un país</option>
          <?php if (!empty($paises)): ?>
            <?php foreach ($paises as $pais): ?>
              <option value="<?php echo (int)$pais['IdPais']; ?>">
                <?php echo htmlspecialchars($pais['NomPais'], ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="">(No hay países en la BD)</option>
          <?php endif; ?>
        </select>

        <!-- CAMBIO: de slider a campo numérico -->
        <label for="precio">Precio máximo (€):</label>
        <input
          type="number"
          id="precio"
          name="precio"
          min="0"
          step="10000"
          placeholder="Ej: 300000"
        >

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
