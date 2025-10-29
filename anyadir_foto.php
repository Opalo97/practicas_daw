<?php
$title = "Añadir foto a mi anuncio";
require_once("cabecera.inc");
require_once("inicio.inc");

// Comprobamos si viene desde ver_anuncio o mis_anuncios
$anuncio_id = isset($_GET['anuncio_id']) ? $_GET['anuncio_id'] : null;
?>

<section>

  <p>
    Completa el siguiente formulario para añadir una nueva imagen a uno de tus anuncios publicados.
  </p>

  <form action="#" method="post" enctype="multipart/form-data">
    <fieldset>
      <legend>Datos de la nueva foto</legend>

      <!-- Campo de subida -->
      <label for="foto">Selecciona la foto:</label><br>
      <input type="file" id="foto" name="foto" accept="image/*" required><br><br>

      <!-- Texto alternativo -->
      <label for="alt">Texto alternativo (para accesibilidad):</label><br>
      <input type="text" id="alt" name="alt" maxlength="200" placeholder="Ej. Fachada del edificio" required><br><br>

      <!-- Título de la foto -->
      <label for="titulo_foto">Título de la foto:</label><br>
      <input type="text" id="titulo_foto" name="titulo_foto" maxlength="200" placeholder="Ej. Salón principal" required><br><br>

      <!-- Selección de anuncio -->
      <label for="anuncio">Anuncio asociado:</label><br>

      <?php if ($anuncio_id): ?>
        <!-- Modo: acceso directo (anuncio bloqueado) -->
        <select id="anuncio" name="anuncio" disabled style="color:#4b2b99;">
          <option  value="<?php echo htmlspecialchars($anuncio_id); ?>" selected>
            Anuncio nº <?php echo htmlspecialchars($anuncio_id); ?> — Piso luminoso en Madrid
          </option>
        </select>
        <input type="hidden" name="anuncio" value="<?php echo htmlspecialchars($anuncio_id); ?>">
        <p>* Este anuncio ha sido preseleccionado automáticamente.</p>
      <?php else: ?>
        <!-- Modo: acceso desde el menú -->
        <select id="anuncio" name="anuncio" required>
          <option value="">— Selecciona un anuncio —</option>
          <option value="1">Piso céntrico en Madrid</option>
          <option value="2">Apartamento en París</option>
          <option value="3">Ático en Lisboa</option>
        </select>
      <?php endif; ?>
    </fieldset>

    <p>
      <button type="submit" class="button">Guardar foto</button>
      <button type="reset" class="button">Limpiar</button>
      <a href="mis_anuncios.php" class="enlaces">Cancelar</a>
    </p>
  </form>
</section>

</main>

<?php
require_once("footer.inc");
?>
