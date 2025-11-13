<?php
$title = "Enviar mensaje al propietario";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");

// ------------------------------------------------------
// 1. Obtener id del anuncio desde la URL
//    (enlace desde detalle_anuncio.php?id=...)
// ------------------------------------------------------
$idAnuncio = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Conexión a la BD
$mysqli = obtenerConexion();

// ------------------------------------------------------
// 2. Cargar información básica del anuncio y email del propietario
// ------------------------------------------------------
$tituloAnuncio = "Anuncio no encontrado";
$emailDestino  = "desconocido";

if ($idAnuncio > 0) {
    $sqlAnuncio = "SELECT 
                        a.IdAnuncio,
                        a.Titulo,
                        u.Email
                   FROM Anuncios a
                   LEFT JOIN Usuarios u ON a.Usuario = u.IdUsuario
                   WHERE a.IdAnuncio = $idAnuncio";

    if ($resA = $mysqli->query($sqlAnuncio)) {
        if ($resA->num_rows > 0) {
            $filaA = $resA->fetch_assoc();
            $tituloAnuncio = $filaA['Titulo'] ?? $tituloAnuncio;
            $emailDestino  = $filaA['Email']  ?? $emailDestino;
        }
        $resA->free();
    }
}

// ------------------------------------------------------
// 3. Cargar tipos de mensaje desde TiposMensajes
// ------------------------------------------------------
$tiposMensajes = [];
$sqlTM = "SELECT IdTMensaje, NomTMensaje 
          FROM TiposMensajes
          ORDER BY IdTMensaje";

if ($resTM = $mysqli->query($sqlTM)) {
    while ($fila = $resTM->fetch_assoc()) {
        $tiposMensajes[] = $fila;
    }
    $resTM->free();
}

?>
<article>
  <section>

    <fieldset>
      <legend>Información</legend>
      <dl>
        <dt>Anuncio:</dt>
        <dd><?php echo htmlspecialchars($tituloAnuncio, ENT_QUOTES, 'UTF-8'); ?></dd>

        <dt>Mensaje a:</dt>
        <dd><?php echo htmlspecialchars($emailDestino, ENT_QUOTES, 'UTF-8'); ?></dd>
      </dl>
    </fieldset>
  </section>

  <section aria-labelledby="form-mensaje">
    <fieldset>
      <legend>Formulario</legend>

      <form class="enviar-mensaje" action="en-mensaje.php" method="post">
        <!-- Pasamos el id del anuncio a la página que procesa el mensaje -->
        <input type="hidden" name="id_anuncio" value="<?php echo (int)$idAnuncio; ?>">

        <!-- Tipo de mensaje (desde la BD) -->
        <label for="tipo_mensaje">Tipo de mensaje:</label>
        <select id="tipo_mensaje" name="tipo_mensaje">
          <option value="">Seleccione...</option>
          <?php if (!empty($tiposMensajes)): ?>
            <?php foreach ($tiposMensajes as $tm): ?>
              <option value="<?php echo (int)$tm['IdTMensaje']; ?>">
                <?php echo htmlspecialchars($tm['NomTMensaje'], ENT_QUOTES, 'UTF-8'); ?>
              </option>
            <?php endforeach; ?>
          <?php else: ?>
            <option value="">(No hay tipos de mensaje en la BD)</option>
          <?php endif; ?>
        </select>

        <!-- Mensaje -->
        <label for="texto">Escribe tu mensaje:</label>
        <textarea id="texto" name="texto" rows="7" cols="60"
          placeholder="Hola, estoy interesado/a en su anuncio..."></textarea>

        <!-- Botones -->
        <div class="acciones">
          <button class="button" type="submit">Enviar</button>
          <button class="button" type="reset">Limpiar</button>
          <a class="enlaces" href="detalle_anuncio.php?id=<?php echo (int)$idAnuncio; ?>">Cancelar</a>
        </div>
      </form>
    </fieldset>
  </section>
</article>

</main>

<?php
$mysqli->close();
require_once("footer.inc");
?>
