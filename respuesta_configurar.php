<?php
session_start();
require_once("bd.php");
require_once("flashdata.php");

// Asegurar que el usuario está autenticado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['idusuario'])) {
    header("Location: login.php");
    exit;
}

$idUsuario = (int)($_SESSION['idusuario']);
$estiloId = isset($_POST['estilo']) ? (int)$_POST['estilo'] : 0;

if ($estiloId <= 0) {
    // Valor no válido, volver a configurar
    set_flash('error_config', "Selección de estilo no válida.");
    header("Location: configurar.php");
    exit;
}

$mysqli = obtenerConexion();

// Comprobar que el estilo existe
$stmt = $mysqli->prepare("SELECT Fichero, Nombre FROM estilos WHERE IdEstilo = ? LIMIT 1");
$stmt->bind_param("i", $estiloId);
$stmt->execute();
$res = $stmt->get_result();

if (!$fila = $res->fetch_assoc()) {
    $stmt->close();
    $mysqli->close();
    set_flash('error_config', "El estilo seleccionado no existe.");
    header("Location: configurar.php");
    exit;
}

$fichero = $fila['Fichero'];
$nombre = $fila['Nombre'];
$stmt->close();

// Actualizar la preferencia del usuario en la base de datos
$upd = $mysqli->prepare("UPDATE usuarios SET Estilo = ? WHERE IdUsuario = ?");
$upd->bind_param("ii", $estiloId, $idUsuario);
$ok = $upd->execute();
$upd->close();
$mysqli->close();

if (!$ok) {
  set_flash('error_config', "No se pudo guardar la configuración. Inténtalo de nuevo.");
  header("Location: configurar.php");
  exit;
}

// Actualizar sesión y cookie para que la respuesta se muestre con el nuevo estilo
$_SESSION['estilo'] = $fichero;
setcookie('estilo', $fichero, time() + (90 * 24 * 60 * 60), '/', '', false, true);

// Mostrar página de confirmación con el estilo aplicado
$title = "Estilo guardado";
require_once("cabecera.inc");
require_once("inicio.inc");
?>

<section>
  <article>
    <h2>Preferencia guardada</h2>
    <p>Has seleccionado el estilo: <strong><?php echo htmlspecialchars($nombre); ?></strong>.</p>
    <p>La selección se ha guardado en tu perfil y se aplicará en próximas visitas.</p>
    <p><a class="enlaces" href="index.php">Volver a la página principal</a></p>
    <p><a class="enlaces" href="configurar.php">Volver a Configurar</a></p>
  </article>
</section>

</main>

<?php require_once("footer.inc"); ?>
