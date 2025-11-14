<?php
session_start();
require_once("bd.php");

$title = "Ver fotos";
require_once("cabecera.inc");
require_once("inicio.inc");

// ------------------------------------------------------
// 1. Comprobar acceso (usuarios registrados)
// ------------------------------------------------------
if (!isset($_SESSION['usuario'])) {
    header("Location: aviso.php");
    exit;
}

// ------------------------------------------------------
// 2. Obtener ID del anuncio
// ------------------------------------------------------
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<p>ID de anuncio no válido.</p>";
    require_once("footer.inc");
    exit;
}

// ------------------------------------------------------
// 3. Conectar a BD y obtener fotos
// ------------------------------------------------------
$mysqli = obtenerConexion();

// Foto principal
$sql = "SELECT FPrincipal, Alternativo FROM Anuncios WHERE IdAnuncio = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$anuncio = $result->fetch_assoc();
$stmt->close();

// Fotos secundarias
$fotos_secundarias = [];
$sqlFotos = "SELECT Foto, Alternativo FROM Fotos WHERE Anuncio = ? ORDER BY IdFoto";
$stmt2 = $mysqli->prepare($sqlFotos);
$stmt2->bind_param("i", $id);
$stmt2->execute();
$resFotos = $stmt2->get_result();
while ($fila = $resFotos->fetch_assoc()) {
    $fotos_secundarias[] = $fila;
}
$stmt2->close();

$mysqli->close();
?>

<!-- Contenedor principal -->
<section class="ver_fotos_container">

    <!-- Foto principal -->
    <div class="foto_principal">
        <?php if (!empty($anuncio['FPrincipal'])): ?>
            <img src="<?php echo htmlspecialchars($anuncio['FPrincipal']); ?>" 
                 alt="<?php echo htmlspecialchars($anuncio['Alternativo'] ?? 'Foto principal'); ?>">
        <?php else: ?>
            <img src="img/foto_piso.jpg" alt="Foto principal no disponible">
        <?php endif; ?>
    </div>

    <!-- Miniaturas -->
    <div class="fotos_secundarias">
        <?php if (!empty($fotos_secundarias)): ?>
            <p>Total de fotos: <?php echo count($fotos_secundarias); ?></p>
            <?php foreach ($fotos_secundarias as $f): ?>
                <img class="miniatura_foto" 
                     src="<?php echo htmlspecialchars($f['Foto']); ?>" 
                     alt="<?php echo htmlspecialchars($f['Alternativo'] ?? 'Foto adicional'); ?>">
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay fotos adicionales.</p>
        <?php endif; ?>
    </div>

</section>

<?php require_once("footer.inc"); ?>
