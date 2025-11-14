<?php 
$title = "Iniciar sesión";
require_once("cabecera.inc");
require_once("inicio2.inc");
require_once("flashdata.php");

ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once("bd.php"); // Conexión a la BD

// ---------- Restaurar sesión desde cookies ----------
if (isset($_COOKIE['usuario']) && isset($_COOKIE['password'])) {
    $mysqli = obtenerConexion();

    $stmt = $mysqli->prepare("SELECT NomUsuario, Clave FROM usuarios WHERE NomUsuario = ?");
    $stmt->bind_param("s", $_COOKIE['usuario']);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        if ($_COOKIE['password'] === $fila['Clave']) {
            $_SESSION['usuario'] = $fila['NomUsuario'];
            header("Location: index.php");
            exit;
        }
    }

    $stmt->close();
    $mysqli->close();
}

// ---------- Recuperar valores flash ----------
$flash_error = get_flash('error');
$flash_user  = get_flash('user');
$valorUsuario = $_COOKIE['usuario'] ?? $flash_user ?? '';

?>
<section>
  <fieldset>
    <legend>Iniciar sesión</legend>

    <?php if ($flash_error): ?>
      <p class="mensaje-error"><?php echo htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form id="formLogin" action="control_acceso.php" method="post" novalidate>
      <label for="usuario">Usuario</label><br>
      <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($valorUsuario, ENT_QUOTES, 'UTF-8'); ?>"><br>

      <label for="password">Contraseña</label><br>
      <input type="password" id="password" name="password" value="<?php echo isset($_COOKIE['password']) ? htmlspecialchars($_COOKIE['password'], ENT_QUOTES, 'UTF-8') : ''; ?>"><br><br>

      <label>
        <input type="checkbox" name="recordarme" <?php if (isset($_COOKIE['usuario'])) echo "checked"; ?>>
        Recordarme en este equipo
      </label><br><br>

      <button type="submit" class="button">Acceder</button>
      <a href="signup.php" class="enlaces">Crear cuenta</a>
    </form>
  </fieldset>
</section>

</main>
<?php require_once("footer.inc"); ?>
