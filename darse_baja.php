<?php
// Ocultar notices y warnings en la salida
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', '0');
?>

<?php
      // ===================================================================
      // 0) BORRAR TODOS LOS FICHEROS FÍSICOS DEL USUARIO
      // ===================================================================
      // Al darse de baja, eliminar del servidor:
      // 1. Foto de perfil del usuario
      // 2. Todas las fotos de todos sus anuncios
            
      // BORRAR FOTO DE PERFIL
      if (!empty($user['Foto'])) {
        // Obtener ruta absoluta real
        $rutaAbs = realpath(__DIR__ . DIRECTORY_SEPARATOR . $user['Foto']);
        $base = realpath(__DIR__);
        // Validar que está dentro del proyecto (seguridad)
        if ($rutaAbs && strpos($rutaAbs, $base) === 0 && is_file($rutaAbs)) {
          @unlink($rutaAbs); // Borrar fichero
        }
      }

      // BORRAR FOTOS DE TODOS LOS ANUNCIOS DEL USUARIO
      // Consultar todas las fotos de los anuncios del usuario
      $sql = "SELECT f.Foto AS FotoPath
          FROM fotos f
          INNER JOIN anuncios a ON f.Anuncio = a.IdAnuncio
          WHERE a.Usuario = ?";
      $stmt = $mysqli->prepare($sql);
      $stmt->bind_param("i", $idUsuario);
      $stmt->execute();
      $resPaths = $stmt->get_result();
      $base = realpath(__DIR__);
      // Borrar cada fichero de forma segura
      while ($rowP = $resPaths->fetch_assoc()) {
        if (!empty($rowP['FotoPath'])) {
          $rutaAbs = realpath(__DIR__ . DIRECTORY_SEPARATOR . $rowP['FotoPath']);
          // Solo borrar si está dentro del proyecto
          if ($rutaAbs && strpos($rutaAbs, $base) === 0 && is_file($rutaAbs)) {
            @unlink($rutaAbs);
          }
        }
      }
      $stmt->close();

$user = $res->fetch_assoc();
$idUsuario = (int)$user['IdUsuario'];
$fechaRegistro = date("d/m/Y", strtotime($user['FRegistro']));
$stmt->close();


// ================================
// 2) OBTENER RESUMEN DE ANUNCIOS
// ================================
$sql = "SELECT a.IdAnuncio, a.Titulo,
        (SELECT COUNT(*) FROM fotos f WHERE f.Anuncio = a.IdAnuncio) AS numFotos
        FROM anuncios a
        WHERE a.Usuario = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$res = $stmt->get_result();

$anuncios = [];
$totalFotos = 0;

while ($fila = $res->fetch_assoc()) {
    $anuncios[] = $fila;
    $totalFotos += $fila['numFotos'];
}

$numAnuncios = count($anuncios);

$stmt->close();

// ================================
// 3) SI EL FORMULARIO SE HA ENVIADO
// ================================
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $claveIntroducida = $_POST['clave'] ?? '';

    // Validar contraseña usando password_verify()
    // Compara el texto plano con el hash de la BD
    if (!password_verify($claveIntroducida, $user['Clave'])) {
        $errores[] = "La contraseña introducida no es correcta.";
    }

    if (empty($errores)) {

        // =====================
        // 4) TRANSACCIÓN SEGURA
        // =====================
        $mysqli->begin_transaction();

        try {
          // 0) Borrar ficheros físicos de todas las fotos de sus anuncios y su foto de perfil
          // Foto de perfil
          if (!empty($user['Foto'])) {
            $rutaAbs = realpath(__DIR__ . DIRECTORY_SEPARATOR . $user['Foto']);
            $base = realpath(__DIR__);
            if ($rutaAbs && strpos($rutaAbs, $base) === 0 && is_file($rutaAbs)) {
              @unlink($rutaAbs);
            }
          }

          // Fotos de anuncios del usuario
          $sql = "SELECT f.Foto AS FotoPath
              FROM fotos f
              INNER JOIN anuncios a ON f.Anuncio = a.IdAnuncio
              WHERE a.Usuario = ?";
          $stmt = $mysqli->prepare($sql);
          $stmt->bind_param("i", $idUsuario);
          $stmt->execute();
          $resPaths = $stmt->get_result();
          $base = realpath(__DIR__);
          while ($rowP = $resPaths->fetch_assoc()) {
            if (!empty($rowP['FotoPath'])) {
              $rutaAbs = realpath(__DIR__ . DIRECTORY_SEPARATOR . $rowP['FotoPath']);
              if ($rutaAbs && strpos($rutaAbs, $base) === 0 && is_file($rutaAbs)) {
                @unlink($rutaAbs);
              }
            }
          }
          $stmt->close();

            // -------------------------
            // 4.1) BORRAR MENSAJES
            // campos reales → UsuOrigen / UsuDestino
            // -------------------------
            $sql = "DELETE FROM mensajes WHERE UsuOrigen = ? OR UsuDestino = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ii", $idUsuario, $idUsuario);
            $stmt->execute();
            $stmt->close();

            // -------------------------
            // 4.2) BORRAR FOTOS DE SUS ANUNCIOS
            // -------------------------
            $sql = "DELETE f FROM fotos f 
                    INNER JOIN anuncios a ON f.Anuncio = a.IdAnuncio
                    WHERE a.Usuario = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $stmt->close();

            // -------------------------
            // 4.3) BORRAR SOLICITUDES RELACIONADAS
            // -------------------------
            $sql = "DELETE s FROM solicitudes s
                    INNER JOIN anuncios a ON s.Anuncio = a.IdAnuncio
                    WHERE a.Usuario = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $stmt->close();

            // -------------------------
            // 4.4) BORRAR ANUNCIOS
            // -------------------------
            $sql = "DELETE FROM anuncios WHERE Usuario = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $stmt->close();

            // -------------------------
            // 4.5) BORRAR USUARIO
            // -------------------------
            $sql = "DELETE FROM usuarios WHERE IdUsuario = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $stmt->close();

            // -------------------------
            // 4.6) CONFIRMAR TRANSACCIÓN
            // -------------------------
            $mysqli->commit();

            // Cerrar sesión y redirigir
            session_unset();
            session_destroy();

            header("Location: index_no.php?baja=ok");
            exit;

        } catch (Exception $e) {
            // ERROR → REVERTIR TODO
            $mysqli->rollback();
            $errores[] = "Se ha producido un error al eliminar tu cuenta. Inténtalo más tarde.";
        }
    }
}

?>


<h2>Confirmar baja de la cuenta</h2>

<!-- ======================= -->
<!-- DATOS DEL USUARIO      -->
<!-- ======================= -->

<fieldset style="max-width:650px; margin:auto;">
  <legend>Datos de la cuenta</legend>
  <p><strong>Usuario:</strong> <?= htmlspecialchars($usuarioLog) ?></p>
  <p><strong>Fecha de incorporación:</strong> <?= $fechaRegistro ?></p>
</fieldset>

<br>

<!-- ======================= -->
<!-- RESUMEN DE ANUNCIOS     -->
<!-- ======================= -->

<fieldset style="max-width:650px; margin:auto;">
  <legend>Resumen de tus anuncios</legend>

  <?php if ($numAnuncios === 0): ?>
      <p>No tienes anuncios publicados.</p>
  <?php else: ?>
      <table style="width:100%; border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left;">Título</th>
            <th style="text-align:left;">Nº de fotos</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($anuncios as $a): ?>
          <tr>
            <td><?= htmlspecialchars($a['Titulo']) ?></td>
            <td><?= $a['numFotos'] ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th>Total</th>
            <th><?= $totalFotos ?> fotos en <?= $numAnuncios ?> anuncios</th>
          </tr>
        </tfoot>
      </table>
  <?php endif; ?>
</fieldset>

<br>

<!-- ======================= -->
<!-- ERRORES                 -->
<!-- ======================= -->

<?php if (!empty($errores)): ?>
<fieldset style="max-width:650px; margin:auto; border:2px solid red;">
  <legend>Errores</legend>
  <?php foreach ($errores as $e): ?>
    <p style="color:red;"><?= htmlspecialchars($e) ?></p>
  <?php endforeach; ?>
</fieldset>
<br>
<?php endif; ?>

<!-- ======================= -->
<!-- CONFIRMACIÓN DE BAJA    -->
<!-- ======================= -->

<fieldset style="max-width:650px; margin:auto;">
  <legend>Confirmación de baja</legend>

  <p>Para confirmar la eliminación definitiva de tu cuenta, introduce tu contraseña actual.</p>

  <form action="" method="post">
    <label><strong>Contraseña actual:</strong></label><br>
    <input type="password" name="clave" style="width:90%; padding:6px; margin:10px 0;" required>

    <br>

    <button class="button" type="submit">Confirmar baja</button>
    <a href="cuenta.php" class="button">Cancelar</a>
  </form>
</fieldset>

</main>

<?php require_once("footer.inc"); ?>
