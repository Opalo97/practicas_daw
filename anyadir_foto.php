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

  <form action="respuesta_anyadir_foto.php" method="post" enctype="multipart/form-data">
    <fieldset>
      <legend>Datos de la nueva foto</legend>

      <!-- Campo de subida -->
      <label for="foto">Selecciona la foto:</label><br>
      <input type="file" id="foto" name="foto" accept="image/*"><br><br>

      <!-- Texto alternativo -->
      <label for="alt">Texto alternativo (para accesibilidad):</label><br>
      <input type="text" id="alt" name="alt" maxlength="200" placeholder="Ej. Fachada del edificio" required><br><br>

      <!-- Título de la foto -->
      <label for="titulo_foto">Título de la foto:</label><br>
      <input type="text" id="titulo_foto" name="titulo_foto" maxlength="200" placeholder="Ej. Salón principal" required><br><br>

      <!-- Selección de anuncio -->
      <label for="anuncio">Anuncio asociado:</label><br>

      <?php
        // Si se recibe anuncio_id por GET lo usamos para preseleccionar, pero permitimos cambiarlo.
        $sel = $anuncio_id ? (int)$anuncio_id : 0;
      ?>

      <select id="anuncio" name="anuncio" required>
        <option value="">— Selecciona un anuncio —</option>
        <?php
          // Cargar anuncios del usuario desde la base de datos
          require_once('bd.php');
          $userId = $_SESSION['idusuario'] ?? null;
          if ($userId) {
              $mysqli = obtenerConexion();
              $sql = 'SELECT IdAnuncio, Titulo FROM Anuncios WHERE Usuario = ? ORDER BY IdAnuncio DESC';
              if ($stmt = $mysqli->prepare($sql)) {
                  $stmt->bind_param('i', $userId);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($a = $res->fetch_assoc()) {
                      $aid = (int)$a['IdAnuncio'];
                      $titulo = $a['Titulo'] ?? ('Anuncio ' . $aid);
                      $selAttr = ($sel === $aid) ? ' selected' : '';
                      echo '<option value="' . htmlspecialchars($aid) . '"' . $selAttr . '>' . htmlspecialchars($titulo) . ' (id ' . htmlspecialchars($aid) . ')</option>';
                  }
                  $stmt->close();
              }
              $mysqli->close();
          } else {
              // No hay sesión; mostrar opción por defecto
              echo '<option value="">(debes iniciar sesión para ver tus anuncios)</option>';
          }
        ?>
      </select>

      <?php if ($sel > 0): ?>
        <p><em>* Se ha preseleccionado el anuncio nº <?php echo htmlspecialchars($sel); ?>; puedes cambiar la selección si lo deseas.</em></p>
        <input type="hidden" name="preseleccionado" value="1">
      <?php endif; ?>
    </fieldset>

    <?php require_once('flashdata.php');
      $errores = get_flash('errores');
      $ok = get_flash('ok');
      if ($errores): ?>
    <div class="mensaje-error">
      <p><strong>Errores detectados:</strong></p>
      <ul>
        <?php foreach ($errores as $msg): ?>
          <li><?php echo htmlspecialchars($msg); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php elseif ($ok): ?>
    <div class="mensaje-ok">
      <p><?php echo htmlspecialchars($ok); ?></p>
    </div>
    <?php endif; ?>

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
