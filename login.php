<?php
$title = "Iniciar sesión";
require_once("cabecera.inc");
require_once("inicio2.inc");


ob_start();
session_start();

// Si el usuario ya tiene una cookie válida, saltar el login y redirigir
if (isset($_COOKIE['usuario']) && isset($_COOKIE['password'])) {
    $usuarios = require_once("usuarios.php"); 

    foreach ($usuarios as $u) {
        if ($u['usuario'] === $_COOKIE['usuario'] && $u['password'] === $_COOKIE['password']) {
            $_SESSION['usuario'] = $u['usuario'];
            header("Location: cuenta.php"); // tu página privada principal
            exit;
        }
    }
}

// Recuperar valores por si hay error
$valorUsuario = $_COOKIE['usuario'] ?? ''; // Si hay cookie, precargar
if (isset($_GET['user'])) {
    $valorUsuario = htmlspecialchars($_GET['user'], ENT_QUOTES, 'UTF-8');
}
?>

<section>
  <fieldset>
    <legend>Iniciar sesión</legend>

    <?php
    // Mensajes de error recibidos por GET
    if (isset($_GET['error'])) {
        $err = $_GET['error'];
        switch ($err) {
            case 'vacio':
                echo "<p class='mensaje-error'>Por favor, rellena ambos campos.</p>";
                break;
            case 'credenciales':
                echo "<p class='mensaje-error'>Usuario o contraseña incorrectos.</p>";
                break;
            case 'espacios':
                echo "<p class='mensaje-error'>Los campos no pueden contener solo espacios o tabuladores.</p>";
                break;
            default:
                echo "<p class='mensaje-error'>Error desconocido.</p>";
        }
    }
    ?>

    <!-- novalidate para desactivar validación HTML5 -->
    <form id="formLogin" action="control_acceso.php" method="post" novalidate>
      <label for="usuario">Usuario</label><br>
      <input type="text" id="usuario" name="usuario" value="<?php echo htmlspecialchars($valorUsuario, ENT_QUOTES, 'UTF-8'); ?>"><br>
      <div id="errorUsuario" class="mensaje-error"></div>

      <label for="password">Contraseña</label><br>
      <input type="password" id="password" name="password" value="<?php echo isset($_COOKIE['password']) ? htmlspecialchars($_COOKIE['password'], ENT_QUOTES, 'UTF-8') : ''; ?>"><br>
      <div id="errorPassword" class="mensaje-error"></div><br>

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

<?php
require_once("footer.inc");
?>
