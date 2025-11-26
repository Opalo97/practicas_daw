<?php
session_start();
require_once('bd.php');
require_once('flashdata.php');

if (!isset($_SESSION['usuario']) || !isset($_SESSION['idusuario'])) {
    header('Location: login.php');
    exit;
}

$idfoto = isset($_GET['idfoto']) ? (int)$_GET['idfoto'] : (isset($_POST['idfoto']) ? (int)$_POST['idfoto'] : 0);
$returnAnuncio = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($idfoto <= 0) {
    set_flash('error', 'Foto no válida.');
    header('Location: ver_fotos_priv.php?id=' . $returnAnuncio);
    exit;
}

$mysqli = obtenerConexion();

// Obtener info de la foto y anuncio, y propietario
$sql = "SELECT f.Foto AS Fichero, f.Anuncio AS AnuncioId, a.Usuario AS Propietario
        FROM Fotos f
        LEFT JOIN Anuncios a ON f.Anuncio = a.IdAnuncio
        WHERE f.IdFoto = ? LIMIT 1";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('i', $idfoto);
$stmt->execute();
$res = $stmt->get_result();
if (!$fila = $res->fetch_assoc()) {
    $stmt->close();
    $mysqli->close();
    set_flash('error', 'No se encontró la foto.');
    header('Location: ver_fotos_priv.php?id=' . $returnAnuncio);
    exit;
}
$stmt->close();

$anuncioId = (int)$fila['AnuncioId'];
$propietario = (int)$fila['Propietario'];
$rutaFoto = $fila['Fichero'];

// Comprobar que el usuario actual es el propietario del anuncio
if ($propietario !== (int)$_SESSION['idusuario']) {
    $mysqli->close();
    set_flash('error', 'No tienes permisos para eliminar esta foto.');
    header('Location: ver_fotos_priv.php?id=' . $anuncioId);
    exit;
}

// Si el método es POST -> realizar borrado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recolectar el id desde POST por seguridad
    $postId = isset($_POST['idfoto']) ? (int)$_POST['idfoto'] : 0;
    if ($postId !== $idfoto) {
        set_flash('error', 'Identificadores no coinciden.');
        header('Location: ver_fotos_priv.php?id=' . $anuncioId);
        exit;
    }

    // Intentar borrar fichero (si existe y está dentro del proyecto)
    $rutaAbs = realpath(__DIR__ . DIRECTORY_SEPARATOR . $rutaFoto);
    $base = realpath(__DIR__);
    if ($rutaAbs && strpos($rutaAbs, $base) === 0 && is_file($rutaAbs)) {
        @unlink($rutaAbs);
    }

    // Borrar registro en BD
    $del = $mysqli->prepare('DELETE FROM Fotos WHERE IdFoto = ?');
    $del->bind_param('i', $idfoto);
    $ok = $del->execute();
    $del->close();
    $mysqli->close();

    if ($ok) {
        set_flash('ok', 'Foto eliminada correctamente.');
    } else {
        set_flash('error', 'No se pudo eliminar la foto de la base de datos.');
    }
    header('Location: ver_fotos_priv.php?id=' . $anuncioId);
    exit;
}

$mysqli->close();

// Mostrar página de confirmación
$title = 'Confirmar eliminación de foto';
require_once('cabecera.inc');
require_once('inicio.inc');
?>

<section>
  <article>
    <h2>Eliminar foto</h2>
    <p>¿Estás seguro de que deseas eliminar esta foto? Esta operación no se puede deshacer.</p>
    <div class="foto-confirm">
      <img src="<?php echo htmlspecialchars($rutaFoto); ?>" alt="Foto a eliminar" style="max-width:300px;">
    </div>

    <form method="post" action="eliminar_foto.php">
      <input type="hidden" name="idfoto" value="<?php echo $idfoto; ?>">
      <input type="hidden" name="id" value="<?php echo $anuncioId; ?>">
      <p>
        <input type="submit" value="Confirmar eliminación" class="button">
        <a class="enlaces" href="ver_fotos_priv.php?id=<?php echo $anuncioId; ?>">Cancelar</a>
      </p>
    </form>
  </article>
</section>

</main>

<?php require_once('footer.inc'); ?>
