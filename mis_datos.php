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
require_once('flashdata.php');

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
    <?php
      $errores = get_flash('errores_mis_datos');
      $ok = get_flash('ok_mis_datos');
      if ($errores):
    ?>
      <div class="mensaje-error">
        <p><strong>Errores detectados:</strong></p>
        <ul>
          <?php foreach ($errores as $m): ?>
            <li><?php echo htmlspecialchars($m); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php elseif ($ok): ?>
      <div class="mensaje-ok">
        <p><?php echo htmlspecialchars($ok); ?></p>
      </div>
    <?php endif; ?>
    
    <form class="form-registro" method="post" action="respuesta_mis_datos.php" enctype="multipart/form-data">

      <fieldset>
        <legend>Datos de acceso</legend>

        <label>Nombre de usuario:</label>
        <input type="text" name="usuario"
               value="<?php echo htmlspecialchars($usuario['NomUsuario']); ?>" autocomplete="username" />

        <p>Para confirmar los cambios debes introducir tu <strong>contraseña actual</strong>.</p>
        <label>Contraseña actual:</label>
        <input type="password" name="clave_actual" value="" autocomplete="current-password" />

        <label>Nueva contraseña (opcional):</label>
        <input type="password" name="nueva_clave" value="" autocomplete="new-password" />

        <label>Repetir nueva contraseña:</label>
        <input type="password" name="nueva_clave2" value="" autocomplete="new-password" />
      </fieldset>

      <fieldset>
        <legend>Datos personales</legend>

         <label>Correo electrónico:</label>
         <input type="text" name="email"
           value="<?php echo htmlspecialchars($usuario['Email']); ?>" autocomplete="email" />
         <small>El correo se valida en el servidor; introduce un email válido (ej: usuario@dominio.com).</small>

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
        <?php
          $fotoActual = $usuario['Foto'] ?: 'img/user_default.svg';
        ?>
        <img src="<?php echo htmlspecialchars($fotoActual); ?>" width="100" alt="Foto de perfil">

        <label>Subir nueva foto:</label>
        <input type="file" name="foto" accept="image/*">

        <label style="margin-top:10px; display:block;">
          <input type="checkbox" name="eliminar_foto" value="1"> Eliminar mi foto de perfil y usar el icono por defecto
        </label>
      </fieldset>

      <p>
        <input type="submit" class="button" value="Guardar cambios">
      </p>

    </form>
  </article>
</section>

<?php require_once("footer.inc"); ?>
