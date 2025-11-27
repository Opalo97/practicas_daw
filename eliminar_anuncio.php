<?php
ob_start();
session_start();

$title = "Eliminar anuncio";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");

// ===========================================================
// 1. Comprobar sesión
// ===========================================================
if (!isset($_SESSION['usuario'])) {
    header("Location: index_no.php");
    exit;
}

$mysqli = obtenerConexion();

// ===========================================================
// 2. Validar ID de anuncio recibido por GET
// ===========================================================
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    echo "<p>Error: anuncio no especificado.</p>";
    require_once("footer.inc");
    exit;
}

$idAnuncio = (int)$_GET['id'];

// ===========================================================
// 3. Comprobar que el anuncio existe y pertenece al usuario
// ===========================================================
$sql = "SELECT a.*, u.NomUsuario
        FROM anuncios a
        INNER JOIN usuarios u ON a.Usuario = u.IdUsuario
        WHERE a.IdAnuncio = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $idAnuncio);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows !== 1) {
    echo "<p>Error: anuncio no encontrado.</p>";
    require_once("footer.inc");
    exit;
}

$anuncio = $res->fetch_assoc();
$stmt->close();

// Seguridad: solo el propietario puede eliminar
if ($anuncio['NomUsuario'] !== $_SESSION['usuario']) {
    echo "<p>No tienes permiso para eliminar este anuncio.</p>";
    require_once("footer.inc");
    exit;
}

// Datos del anuncio
$titulo      = htmlspecialchars($anuncio['Titulo'], ENT_QUOTES, 'UTF-8');
$ciudad      = htmlspecialchars($anuncio['Ciudad'], ENT_QUOTES, 'UTF-8');
$precio      = number_format($anuncio['Precio'], 2, ',', '.');
$fotoPrincipal = htmlspecialchars($anuncio['FPrincipal'], ENT_QUOTES, 'UTF-8');


// ===========================================================
// 4. Obtener número de fotos asociadas
// ===========================================================
$sql = "SELECT COUNT(*) AS totalFotos FROM fotos WHERE Anuncio = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $idAnuncio);
$stmt->execute();
$resFotos = $stmt->get_result()->fetch_assoc();
$totalFotos = (int)$resFotos['totalFotos'];
$stmt->close();


// ===========================================================
// 5. Si el usuario confirma el borrado (POST)
// ===========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errores = [];
    $mysqli->begin_transaction();

    try {

        // 1) Borrar fotos del anuncio
        $sql = "DELETE FROM fotos WHERE Anuncio = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $idAnuncio);
        $stmt->execute();
        $stmt->close();

        // 2) Borrar solicitudes asociadas a este anuncio
        $sql = "DELETE FROM solicitudes WHERE Anuncio = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $idAnuncio);
        $stmt->execute();
        $stmt->close();

        // 3) Borrar mensajes asociados a este anuncio
        $sql = "DELETE FROM mensajes WHERE Anuncio = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $idAnuncio);
        $stmt->execute();
        $stmt->close();

        // 4) Borrar el propio anuncio
        $sql = "DELETE FROM anuncios WHERE IdAnuncio = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $idAnuncio);
        $stmt->execute();
        $stmt->close();

        // Confirmar todo
        $mysqli->commit();

        // Mostrar mensaje final
        ?>
        <section>
          <article>
            <fieldset style="max-width:600px; margin:auto; text-align:center;">
              <legend>Anuncio eliminado</legend>
              <p>El anuncio <strong><?= $titulo ?></strong> ha sido eliminado correctamente.</p>
              <p><a class="button" href="mis_anuncios.php">Volver a mis anuncios</a></p>
            </fieldset>
          </article>
        </section>

        </main>
        <?php
        require_once("footer.inc");
        exit;

    } catch (Exception $e) {
        $mysqli->rollback();
        echo "<p>Error al eliminar el anuncio. Inténtalo más tarde.</p>";
        require_once("footer.inc");
        exit;
    }
}
?>

<!-- ===========================================================
     6. Mostrar página de confirmación
     =========================================================== -->

<section>
  <article>
    

    <fieldset style="max-width:650px; margin:auto;">
      <legend>Información del anuncio</legend>

      <p><strong>Título:</strong> <?= $titulo ?></p>
      <p><strong>Ciudad:</strong> <?= $ciudad ?></p>
      <p><strong>Precio:</strong> <?= $precio ?> €</p>
      <p><strong>Número total de fotos:</strong> <?= $totalFotos ?></p>

      <div style="margin-top:10px;">
        <img src="<?= $fotoPrincipal ?>" alt="Foto principal" style="max-width:300px;">
      </div>
    </fieldset>

    <br>

    <fieldset style="max-width:650px; margin:auto; border:2px solid red;">
      <legend>Confirmación</legend>
      <p style="color:red;">
        ¿Seguro que deseas eliminar este anuncio?<br>
        Esta acción eliminará <strong>todas las fotos, solicitudes y mensajes</strong> relacionados.
      </p>

      <form action="eliminar_anuncio.php?id=<?= $idAnuncio ?>" method="post">
        <button type="submit" class="button" style="background:#c00; color:white;">
          Sí, eliminar anuncio
        </button>
        <a href="mis_anuncios.php" class="button">Cancelar</a>
      </form>
    </fieldset>
  </article>
</section>

</main>

<?php require_once("footer.inc"); ?>
