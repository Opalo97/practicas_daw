<?php
$title = "Crear un anuncio nuevo";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php"); // conexión a la BD

$mysqli = obtenerConexion();
?>

<section>
  <p>
    Completa el siguiente formulario para publicar un nuevo anuncio de inmueble. 
    Todos los campos marcados con * son obligatorios.
  </p>

  <form action="#" method="post" enctype="multipart/form-data">
    <!-- DATOS PRINCIPALES -->
    <fieldset>
      <legend>Datos principales</legend>

      <label for="tipo_anuncio">Tipo de anuncio *</label><br>
      <select id="tipo_anuncio" name="tipo_anuncio" required>
        <option value="">— Selecciona —</option>
        <?php
        $res = $mysqli->query("SELECT IdTAnuncio, NomTAnuncio FROM TiposAnuncios ORDER BY NomTAnuncio");
        while ($row = $res->fetch_assoc()) {
            echo '<option value="'.htmlspecialchars($row['IdTAnuncio']).'">'.htmlspecialchars($row['NomTAnuncio']).'</option>';
        }
        $res->free();
        ?>
      </select><br><br>

      <label for="tipo_vivienda">Tipo de vivienda *</label><br>
      <select id="tipo_vivienda" name="tipo_vivienda" required>
        <option value="">— Selecciona —</option>
        <?php
        $res = $mysqli->query("SELECT IdTVivienda, NomTVivienda FROM TiposViviendas ORDER BY NomTVivienda");
        while ($row = $res->fetch_assoc()) {
            echo '<option value="'.htmlspecialchars($row['IdTVivienda']).'">'.htmlspecialchars($row['NomTVivienda']).'</option>';
        }
        $res->free();
        ?>
      </select><br><br>

      <label for="titulo">Título *</label><br>
      <input type="text" id="titulo" name="titulo" maxlength="200" required placeholder="Ej. Piso céntrico en Madrid"><br><br>

      <label for="precio">Precio (€) *</label><br>
      <input type="number" id="precio" name="precio" min="0" step="0.01" required placeholder="Ej. 250000"><br><br>

      <label for="descripcion">Descripción *</label><br>
      <textarea id="descripcion" name="descripcion" rows="6" maxlength="2000" required
        placeholder="Describe el inmueble..."></textarea>
    </fieldset>

    <!-- UBICACIÓN -->
    <fieldset>
      <legend>Ubicación</legend>

      <label for="ciudad">Ciudad *</label><br>
      <input type="text" id="ciudad" name="ciudad" maxlength="100" required><br><br>

      <label for="pais">País *</label><br>
      <select id="pais" name="pais" required>
        <option value="">Seleccione un país</option>
        <?php
        $res = $mysqli->query("SELECT IdPais, NomPais FROM Paises ORDER BY NomPais");
        while ($row = $res->fetch_assoc()) {
            echo '<option value="'.htmlspecialchars($row['IdPais']).'">'.htmlspecialchars($row['NomPais']).'</option>';
        }
        $res->free();
        ?>
      </select><br><br>

      <label for="fecha">Fecha de publicación *</label><br>
      <input type="date" id="fecha" name="fecha" required>
    </fieldset>

    <!-- CARACTERÍSTICAS -->
    <fieldset>
      <legend>Características del inmueble</legend>

      <label for="superficie">Superficie (m²)</label><br>
      <input type="number" id="superficie" name="superficie" min="0" step="1"><br><br>

      <label for="habitaciones">Habitaciones</label><br>
      <input type="number" id="habitaciones" name="habitaciones" min="0" step="1"><br><br>

      <label for="banos">Baños</label><br>
      <input type="number" id="banos" name="banos" min="0" step="1"><br><br>

      <label for="planta">Planta</label><br>
      <input type="text" id="planta" name="planta" maxlength="50"><br><br>

      <label for="anio">Año de construcción</label><br>
      <input type="number" id="anio" name="anio" min="1800" max="2025"><br><br>

    </fieldset>

    <!-- FOTO PRINCIPAL -->
    <fieldset>
      <legend>Foto principal</legend>
      <label for="foto_principal">Selecciona una imagen *</label><br>
      <input type="file" id="foto_principal" name="foto_principal" accept="image/*" required><br><br>

      <label for="alt_foto">Texto alternativo</label><br>
      <input type="text" id="alt_foto" name="alt_foto" maxlength="200" placeholder="Ej. Fachada del edificio">
    </fieldset>

    <!-- BOTONES -->
    <p>
      <button type="submit" class="button">Crear anuncio</button>
      <button type="reset" class="button">Limpiar</button>
      <a href="cuenta.php" class="enlaces">Cancelar</a>
    </p>
  </form>
</section>

</main>

<?php
$mysqli->close();
require_once("footer.inc");
?>
