<?php
// Página que procesa el envío de un mensaje y lo inserta en la tabla `Mensajes`.
session_start();
require_once('bd.php');
// Título para la cabecera
$title = 'Enviar mensaje - Confirmación';
require_once('cabecera.inc');
require_once('inicio.inc');

$idAnuncio = isset($_POST['id_anuncio']) ? (int)$_POST['id_anuncio'] : 0;
$tipo = isset($_POST['tipo_mensaje']) ? (int)$_POST['tipo_mensaje'] : 0;
$texto = trim($_POST['texto'] ?? '');

$errores = [];
if ($idAnuncio <= 0) $errores[] = 'Anuncio no válido.';
if ($texto === '' || mb_strlen($texto) < 3) $errores[] = 'El mensaje no puede estar vacío.';

// UsuOrigen: si el usuario está logueado, usar su IdUsuario, si no, 0
$usuOrigen = isset($_SESSION['idusuario']) ? (int)$_SESSION['idusuario'] : 0;

// Conectar y comprobar anuncio + obtener usuario destino
$mysqli = obtenerConexion();
$usuarioDestino = 0;
if (empty($errores)) {
    $stmt = $mysqli->prepare('SELECT Usuario FROM Anuncios WHERE IdAnuncio = ? LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('i', $idAnuncio);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $usuarioDestino = (int)$row['Usuario'];
        } else {
            $errores[] = 'Anuncio no encontrado.';
        }
        $stmt->close();
    } else {
        $errores[] = 'Error interno.';
    }
}

if (empty($errores)) {
    $stmtIns = $mysqli->prepare('INSERT INTO Mensajes (TMensaje, Texto, Anuncio, UsuOrigen, UsuDestino) VALUES (?, ?, ?, ?, ?)');
    if ($stmtIns) {
        $stmtIns->bind_param('isiii', $tipo, $texto, $idAnuncio, $usuOrigen, $usuarioDestino);
        $ok = $stmtIns->execute();
        $stmtIns->close();
        if ($ok) {
            ?>
            <section>
              <fieldset>
                <legend>Mensaje enviado correctamente</legend>
                <dl>
                  <dt>Anuncio</dt>
                  <dd><?php echo htmlspecialchars($idAnuncio); ?></dd>
                  <dt>Mensaje</dt>
                  <dd><?php echo nl2br(htmlspecialchars($texto)); ?></dd>
                  <dt>Usuario origen</dt>
                  <dd><?php echo $usuOrigen > 0 ? htmlspecialchars($_SESSION['usuario']) : 'Anónimo'; ?></dd>
                </dl>
              </fieldset>
              <p><a class="enlaces" href="detalle_anuncio.php?id=<?php echo (int)$idAnuncio; ?>">Volver al anuncio</a></p>
            </section>
            </main>
            <?php
            require_once('footer.inc');
            $mysqli->close();
            exit;
        } else {
            $errores[] = 'No se pudo guardar el mensaje.';
        }
    } else {
        $errores[] = 'Error preparando la inserción.';
    }
}

// Si hay errores, mostrarlos
?>
<section>
  <div class="mensaje-error">
    <p><strong>Errores detectados:</strong></p>
    <ul>
      <?php foreach ($errores as $e): ?>
        <li><?php echo htmlspecialchars($e); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <p><a class="enlaces" href="enviar_mensaje.php?id=<?php echo (int)$idAnuncio; ?>">Volver</a></p>
</section>

</main>

<?php
require_once('footer.inc');
$mysqli->close();
?>
