<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$title = "Mis datos";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");

$mysqli = obtenerConexion();

// ---- Obtener datos del usuario logueado ----
$id = $_SESSION['idusuario']; // <-- clave corregida

$sql = "SELECT IdUsuario, NomUsuario, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, Estilo
        FROM usuarios
        WHERE IdUsuario = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();

$stmt->close();

// ---- Cargar países ----
$paises = [];
$sql = "SELECT IdPais, NomPais FROM Paises ORDER BY NomPais";
$res = $mysqli->query($sql);
while ($fila = $res->fetch_assoc()) {
    $paises[] = $fila;
}
$res->free();

$mysqli->close();
?>

<section>
  <article>
    
    <form class="form-registro" method="post" action="#" enctype="multipart/form-data">

      <fieldset>
        <legend>Datos de acceso</legend>

        <label>Nombre de usuario:</label>
        <input type="text" name="usuario"
               value="<?php echo htmlspecialchars($usuario['NomUsuario']); ?>" autocomplete="username" />

        <label>Contraseña:</label>
        <input type="password" name="clave" value="********" autocomplete="current-password" />

        <label>Repetir contraseña:</label>
        <input type="password" name="clave2" value="********" autocomplete="new-password" />
      </fieldset>

      <fieldset>
        <legend>Datos personales</legend>

        <label>Correo electrónico:</label>
        <input type="email" name="email"
               value="<?php echo htmlspecialchars($usuario['Email']); ?>" autocomplete="email" />

        <p>Sexo:</p>
        <label><input type="radio" name="sexo" value="1"
               <?php echo ($usuario['Sexo'] == 1) ? 'checked' : ''; ?>> Hombre</label>

        <label><input type="radio" name="sexo" value="2"
               <?php echo ($usuario['Sexo'] == 2) ? 'checked' : ''; ?>> Mujer</label>

        <label>Fecha de nacimiento:</label>
        <input type="text" name="fecha"
               value="<?php echo htmlspecialchars($usuario['FNacimiento']); ?>" />

        <label>Ciudad:</label>
        <input type="text" name="ciudad"
               value="<?php echo htmlspecialchars($usuario['Ciudad']); ?>" />

        <label>País:</label>
        <select name="pais">
          <option value="">Seleccione un país</option>
          <?php foreach ($paises as $pais): ?>
            <option value="<?php echo $pais['IdPais']; ?>"
              <?php echo ($usuario['Pais'] == $pais['IdPais']) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($pais['NomPais']); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Foto actual:</label>
        <img src="<?php echo htmlspecialchars($usuario['Foto']); ?>" width="100">

        <label>Subir nueva foto:</label>
        <input type="file" name="foto">
      </fieldset>

      <p>
        <input type="submit" class="button" value="Guardar cambios" disabled>
        <!-- botón deshabilitado porque NO debe guardar -->
      </p>

    </form>
  </article>
</section>

<?php require_once("footer.inc"); ?>
