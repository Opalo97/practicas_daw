<?php
?>

<form action="<?= htmlspecialchars($accion) ?>" method="post" enctype="multipart/form-data">

  <!-- DATOS PRINCIPALES -->
  <fieldset>
    <legend>Datos principales</legend>

    <label for="tipo_anuncio">Tipo de anuncio *</label><br>
    <select id="tipo_anuncio" name="tipo_anuncio" required>
      <option value="">— Selecciona —</option>
      <?php
      $res = $mysqli->query("SELECT IdTAnuncio, NomTAnuncio FROM TiposAnuncios ORDER BY NomTAnuncio");
      while ($row = $res->fetch_assoc()):
          $sel = ($row['IdTAnuncio'] == $tipo_anuncio) ? "selected" : "";
          ?>
          <option value="<?= $row['IdTAnuncio'] ?>" <?= $sel ?>>
            <?= htmlspecialchars($row['NomTAnuncio']) ?>
          </option>
      <?php endwhile; ?>
    </select><br><br>

    <label for="tipo_vivienda">Tipo de vivienda *</label><br>
    <select id="tipo_vivienda" name="tipo_vivienda" required>
      <option value="">— Selecciona —</option>
      <?php
      $res = $mysqli->query("SELECT IdTVivienda, NomTVivienda FROM TiposViviendas ORDER BY NomTVivienda");
      while ($row = $res->fetch_assoc()):
          $sel = ($row['IdTVivienda'] == $tipo_vivienda) ? "selected" : "";
          ?>
          <option value="<?= $row['IdTVivienda'] ?>" <?= $sel ?>>
            <?= htmlspecialchars($row['NomTVivienda']) ?>
          </option>
      <?php endwhile; ?>
    </select><br><br>

    <label for="titulo">Título *</label><br>
    <input type="text" id="titulo" name="titulo" maxlength="200"
           value="<?= htmlspecialchars($titulo) ?>" required><br><br>

    <label for="precio">Precio (€) *</label><br>
    <input type="number" id="precio" name="precio" min="0" step="0.01"
           value="<?= htmlspecialchars($precio) ?>" required><br><br>

    <label for="descripcion">Descripción *</label><br>
    <textarea id="descripcion" name="descripcion" rows="6" maxlength="2000" required><?= htmlspecialchars($descripcion) ?></textarea>
  </fieldset>


  <!-- UBICACIÓN -->
  <fieldset>
    <legend>Ubicación</legend>

    <label for="ciudad">Ciudad *</label><br>
    <input type="text" id="ciudad" name="ciudad" maxlength="100"
           value="<?= htmlspecialchars($ciudad) ?>" required><br><br>

    <label for="pais">País *</label><br>
    <select id="pais" name="pais" required>
      <option value="">Seleccione un país</option>
      <?php
      $res = $mysqli->query("SELECT IdPais, NomPais FROM Paises ORDER BY NomPais");
      while ($row = $res->fetch_assoc()):
          $sel = ($row['IdPais'] == $pais) ? "selected" : "";
          ?>
          <option value="<?= $row['IdPais'] ?>" <?= $sel ?>>
            <?= htmlspecialchars($row['NomPais']) ?>
          </option>
      <?php endwhile; ?>
    </select><br><br>

    <label for="fecha">Fecha de publicación *</label><br>
    <input type="date" id="fecha" name="fecha"
           value="<?= htmlspecialchars($fecha) ?>" required>
  </fieldset>


  <!-- CARACTERÍSTICAS -->
  <fieldset>
    <legend>Características del inmueble</legend>

    <label for="superficie">Superficie (m²)</label><br>
    <input type="number" id="superficie" name="superficie" min="0"
           value="<?= htmlspecialchars($superficie) ?>"><br><br>

    <label for="habitaciones">Habitaciones</label><br>
    <input type="number" id="habitaciones" name="habitaciones" min="0"
           value="<?= htmlspecialchars($habitaciones) ?>"><br><br>

    <label for="banos">Baños</label><br>
    <input type="number" id="banos" name="banos" min="0"
           value="<?= htmlspecialchars($banos) ?>"><br><br>

    <label for="planta">Planta</label><br>
    <input type="text" id="planta" name="planta" maxlength="50"
           value="<?= htmlspecialchars($planta) ?>"><br><br>

    <label for="anio">Año de construcción</label><br>
    <input type="number" id="anio" name="anio" min="1800" max="2025"
           value="<?= htmlspecialchars($anio) ?>"><br><br>
  </fieldset>


  <!-- FOTO PRINCIPAL -->
  <fieldset>
    <legend>Foto principal *</legend>

    <?php if ($mostrar_foto_principal && !empty($foto_actual)): ?>
        <p>Foto actual:</p>
        <img src="<?= htmlspecialchars($foto_actual) ?>" 
             alt="Foto principal"
             style="width:200px;border:1px solid #999;margin-bottom:10px;">
        <br><br>
    <?php endif; ?>

    <label for="foto_principal">Selecciona una imagen <?= $mostrar_foto_principal ? "(opcional)" : "*" ?></label><br>
    <input type="file" id="foto_principal" name="foto_principal" accept="image/*"><br><br>

    <label for="alt_foto">Texto alternativo</label><br>
    <input type="text" id="alt_foto" name="alt_foto" maxlength="200"
           value="<?= htmlspecialchars($alt_foto) ?>">
  </fieldset>


  <p>
    <button type="submit" class="button"><?= htmlspecialchars($boton_texto) ?></button>
    <a href="cuenta.php" class="enlaces">Cancelar</a>
  </p>

</form>

