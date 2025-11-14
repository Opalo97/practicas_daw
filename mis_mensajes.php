<?php
error_reporting(E_ALL & ~E_NOTICE); // Reporta todo excepto Notices
session_start();


$title = "Mis Mensajes";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");

session_start();
if (!isset($_SESSION['idusuario'])) {
    header("Location: login.php");
    exit;
}

$idusuario = $_SESSION['idusuario'];
$mysqli = obtenerConexion();
?>

<fieldset>
  <legend>Mensajes Enviados</legend>
  <?php
  $sql_enviados = "
      SELECT m.Texto, m.FRegistro, t.NomTMensaje, u.Email AS destinatario
      FROM mensajes m
      JOIN tiposmensajes t ON m.TMensaje = t.IdTMensaje
      JOIN usuarios u ON m.UsuDestino = u.IdUsuario
      WHERE m.UsuOrigen = ?
      ORDER BY m.FRegistro DESC
  ";
  $stmt = $mysqli->prepare($sql_enviados);
  $stmt->bind_param("i", $idusuario);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows > 0) {
      while ($fila = $res->fetch_assoc()) {
          $texto = htmlspecialchars($fila['Texto'], ENT_QUOTES, 'UTF-8');
          $fecha = date('d/m/Y', strtotime($fila['FRegistro']));
          $tipo  = htmlspecialchars($fila['NomTMensaje'], ENT_QUOTES, 'UTF-8');
          $dest  = htmlspecialchars($fila['destinatario'], ENT_QUOTES, 'UTF-8');
          echo "<dl>
                  <dt>Tipo de mensaje</dt><dd>$tipo</dd>
                  <dt>Texto</dt><dd>$texto</dd>
                  <dt>Fecha</dt><dd>$fecha</dd>
                  <dt>Usuario receptor</dt><dd>$dest</dd>
                </dl><hr>";
      }
  } else {
      echo "<p>No has enviado ningún mensaje.</p>";
  }
  $stmt->close();
  ?>
</fieldset>

<fieldset>
  <legend>Mensajes Recibidos</legend>
  <?php
  $sql_recibidos = "
      SELECT m.Texto, m.FRegistro, t.NomTMensaje, u.Email AS remitente
      FROM mensajes m
      JOIN tiposmensajes t ON m.TMensaje = t.IdTMensaje
      JOIN usuarios u ON m.UsuOrigen = u.IdUsuario
      WHERE m.UsuDestino = ?
      ORDER BY m.FRegistro DESC
  ";
  $stmt = $mysqli->prepare($sql_recibidos);
  $stmt->bind_param("i", $idusuario);
  $stmt->execute();
  $res = $stmt->get_result();

  if ($res->num_rows > 0) {
      while ($fila = $res->fetch_assoc()) {
          $texto = htmlspecialchars($fila['Texto'], ENT_QUOTES, 'UTF-8');
          $fecha = date('d/m/Y', strtotime($fila['FRegistro']));
          $tipo  = htmlspecialchars($fila['NomTMensaje'], ENT_QUOTES, 'UTF-8');
          $remit = htmlspecialchars($fila['remitente'], ENT_QUOTES, 'UTF-8');
          echo "<dl>
                  <dt>Tipo de mensaje</dt><dd>$tipo</dd>
                  <dt>Texto</dt><dd>$texto</dd>
                  <dt>Fecha</dt><dd>$fecha</dd>
                  <dt>Usuario emisor</dt><dd>$remit</dd>
                </dl><hr>";
      }
  } else {
      echo "<p>No has recibido ningún mensaje.</p>";
  }
  $stmt->close();
  $mysqli->close();
  ?>
</fieldset>

</main>

<?php
require_once("footer.inc");
?>
