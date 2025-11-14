<?php
session_start();
require_once("bd.php");

// ------------------------------------------------------
// 1. Control de acceso: solo usuarios registrados
// ------------------------------------------------------
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$title = "Mensajes del anuncio";
require_once("cabecera.inc");
require_once("inicio.inc");

// ------------------------------------------------------
// 2. Obtener id del anuncio
// ------------------------------------------------------
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<p>Anuncio no válido.</p>";
    require_once("footer.inc");
    exit;
}

$mysqli = obtenerConexion();

// ------------------------------------------------------
// 3. Obtener información básica del anuncio
// ------------------------------------------------------
$sqlAnuncio = "SELECT Titulo, Ciudad, Precio FROM Anuncios WHERE IdAnuncio = ?";
$stmt = $mysqli->prepare($sqlAnuncio);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$anuncio = $res->fetch_assoc();
$stmt->close();

if (!$anuncio) {
    echo "<p>No se ha encontrado el anuncio.</p>";
    $mysqli->close();
    require_once("footer.inc");
    exit;
}

// ------------------------------------------------------
// 4. Obtener mensajes recibidos para el anuncio
// ------------------------------------------------------
// JOIN con Usuarios para obtener el nombre del remitente
$sqlMensajes = "
SELECT u.NomUsuario AS Remitente, m.Texto, m.FRegistro
FROM mensajes m
JOIN Usuarios u ON m.UsuOrigen = u.IdUsuario
WHERE m.Anuncio = ?
ORDER BY m.FRegistro DESC
";
$stmt = $mysqli->prepare($sqlMensajes);
$stmt->bind_param("i", $id);
$stmt->execute();
$resMensajes = $stmt->get_result();

$mensajes = [];
while ($fila = $resMensajes->fetch_assoc()) {
    $mensajes[] = $fila;
}

$stmt->close();
$mysqli->close();
?>

<section>
  <article>
    <h2>Mensajes recibidos</h2>
    <p>
      <strong>Anuncio:</strong> <?php echo htmlspecialchars($anuncio['Titulo']); ?> | 
      <strong>Ciudad:</strong> <?php echo htmlspecialchars($anuncio['Ciudad']); ?> | 
      <strong>Precio:</strong> <?php echo number_format($anuncio['Precio'], 2, ',', '.') . ' €'; ?>
    </p>
    <p><strong>Total de mensajes:</strong> <?php echo count($mensajes); ?></p>

    <?php if (!empty($mensajes)): ?>
      <ul class="lista-mensajes">
        <?php foreach ($mensajes as $m): ?>
          <li>
            <p><strong>Remitente:</strong> <?php echo htmlspecialchars($m['Remitente']); ?></p>
            <p><strong>Mensaje:</strong> <?php echo nl2br(htmlspecialchars($m['Texto'])); ?></p>
            <p><em>Fecha:</em> <?php echo date('d/m/Y H:i', strtotime($m['FRegistro'])); ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No hay mensajes para este anuncio.</p>
    <?php endif; ?>
  </article>
</section>

<?php require_once("footer.inc"); ?>
