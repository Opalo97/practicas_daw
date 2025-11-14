<?php
$title = "Mis anuncios";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php"); // conexión a la DB

// 🔹 Iniciar sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔹 Comprobar usuario logueado
if (!isset($_SESSION['idusuario'])) {
    header("Location: index_no.php");
    exit;
}

$idUsuario = (int)$_SESSION['idusuario'];

// 🔹 Obtener anuncios del usuario
$mysqli = obtenerConexion();

$sql = "SELECT a.IdAnuncio, a.Titulo, a.FPrincipal, a.Ciudad, p.NomPais AS Pais, a.Precio
        FROM Anuncios a
        LEFT JOIN Paises p ON a.Pais = p.IdPais
        WHERE a.Usuario = ?
        ORDER BY a.FRegistro DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $idUsuario);
$stmt->execute();
$resultado = $stmt->get_result();

$totalAnuncios = $resultado->num_rows;
?>

<section>
  <p>A continuación se muestran tus anuncios activos en la plataforma:</p>
  <p><strong>Total de anuncios:</strong> <?php echo $totalAnuncios; ?></p>

  <?php if ($totalAnuncios > 0): ?>
      <?php while ($fila = $resultado->fetch_assoc()): ?>
          <fieldset>
            <legend><?php echo htmlspecialchars($fila['Titulo'], ENT_QUOTES, 'UTF-8'); ?></legend>
            <article class="anuncio-item">
              <div class="imagen_principal">
                <img src="<?php echo htmlspecialchars($fila['FPrincipal'] ?: 'img/foto_piso.jpg', ENT_QUOTES, 'UTF-8'); ?>" 
                     alt="<?php echo htmlspecialchars($fila['Titulo'], ENT_QUOTES, 'UTF-8'); ?>">
              </div>
              <div class="info-anuncio">
                <h4><?php echo htmlspecialchars($fila['Titulo'], ENT_QUOTES, 'UTF-8'); ?></h4>
                <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($fila['Ciudad'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>País:</strong> <?php echo htmlspecialchars($fila['Pais'], ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Precio:</strong> <?php echo number_format($fila['Precio'], 2, ',', '.'); ?> €</p>
                <a class="enlaces" href="ver_anuncio.php?id=<?php echo (int)$fila['IdAnuncio']; ?>">Ver detalle</a>
              </div>
            </article>
          </fieldset>
      <?php endwhile; ?>
  <?php else: ?>
      <p>No tienes anuncios actualmente.</p>
  <?php endif; ?>

  <!-- ENLACE A AÑADIR FOTO -->
  <fieldset>
    <legend>Gestión de los anuncios</legend>
    <p>
      <a class="enlaces" href="anyadir_foto.php">Añadir foto a un anuncio</a>
    </p>
  </fieldset>
</section>

</main>

<?php
$stmt->close();
$mysqli->close();
require_once("footer.inc");
?>

