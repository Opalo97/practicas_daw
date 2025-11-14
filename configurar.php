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

    <form method="post" action="#" class="form-configurar">
      <p>Estilo actual: 
        <strong><?php echo htmlspecialchars($_SESSION['estilo']); ?></strong>
      </p>

      <ul>
        <?php foreach ($estilos as $estilo): ?>
          <li>
            <label>
              <input 
                type="radio" 
                name="estilo" 
                value="<?php echo $estilo['Fichero']; ?>"
                <?php echo ($_SESSION['estilo'] === $estilo['Fichero']) ? 'checked' : ''; ?>
                data-bloqueado="1"
              >
              <?php echo htmlspecialchars($estilo['Nombre']); ?> -
              <?php echo htmlspecialchars($estilo['Descripcion']); ?>
            </label>
          </li>
        <?php endforeach; ?>
      </ul>

      <p>
        <input type="submit" value="Guardar cambios" style="opacity:0.6; pointer-events:none;">
      </p>
    </form>
  </article>
</section>

<?php require_once("footer.inc"); ?>
