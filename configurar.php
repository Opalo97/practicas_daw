<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$title = "Configurar estilo";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");
require_once("flashdata.php");

$mysqli = obtenerConexion();

// ---- Cargar estilos ----
$estilos = [];
$sql = "SELECT IdEstilo, Nombre, Descripcion, Fichero FROM estilos ORDER BY IdEstilo";
$res = $mysqli->query($sql);
while ($fila = $res->fetch_assoc()) {
    $estilos[] = $fila;
}
$res->free();
$mysqli->close();

// Determinar nombre del estilo actual (si es posible)
$estiloActualNombre = null;
foreach ($estilos as $e) {
  if (isset($_SESSION['estilo']) && $_SESSION['estilo'] === $e['Fichero']) {
    $estiloActualNombre = $e['Nombre'];
    break;
  }
}
if ($estiloActualNombre === null) $estiloActualNombre = $_SESSION['estilo'] ?? "basic.css";
?>

<style>
/* Permite ver los radios sin poder modificarlos */
input[type="radio"][data-bloqueado="1"] {
    pointer-events: none;
    opacity: 0.6;
}
</style>

<section>
  <article>

    <?php $err = get_flash('error_config'); if ($err): ?>
      <div class="mensaje-error">
        <p><?php echo htmlspecialchars($err); ?></p>
      </div>
    <?php endif; ?>

    <form method="post" action="respuesta_configurar.php" class="form-configurar">
      <p>Estilo actual: 
        <strong><?php echo htmlspecialchars($estiloActualNombre); ?></strong>
      </p>

      <ul>
        <?php foreach ($estilos as $estilo): ?>
          <li>
            <label>
              <input 
                type="radio" 
                name="estilo" 
                value="<?php echo (int)$estilo['IdEstilo']; ?>"
                <?php echo (isset($_SESSION['estilo']) && $_SESSION['estilo'] === $estilo['Fichero']) ? 'checked' : ''; ?>
              >
              <?php echo htmlspecialchars($estilo['Nombre']); ?> -
              <?php echo htmlspecialchars($estilo['Descripcion']); ?>
            </label>
          </li>
        <?php endforeach; ?>
      </ul>

      <p>
        <input type="submit" value="Guardar cambios">
      </p>
    </form>
  </article>
</section>

<?php require_once("footer.inc"); ?>
