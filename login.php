<?php
$title = "";
require_once("cabecera.inc");
require_once("inicio2.inc");
?>

<section>
  <fieldset>
    <legend>Iniciar sesión</legend>

    <?php
    // Mensajes de error recibidos por GET
    if (isset($_GET['error'])) {
        $err = $_GET['error'];
        if ($err === 'vacio') {
            echo "<p class='mensaje-error'>Por favor, rellena ambos campos.</p>";
        } elseif ($err === 'credenciales') {
            echo "<p class='mensaje-error'>Usuario o contraseña incorrectos.</p>";
        } elseif ($err === 'espacios') {
            echo "<p class='mensaje-error'>Los campos no pueden contener solo espacios o tabuladores.</p>";
        } else {
            echo "<p class='mensaje-error'>Error desconocido.</p>";
        }
    }

    // Reponer el usuario en caso de error (no reponer la contraseña por seguridad)
    $valorUsuario = '';
    if (isset($_GET['user'])) {
        $valorUsuario = htmlspecialchars($_GET['user'], ENT_QUOTES, 'UTF-8');
    }
    ?>

    <!-- novalidate para desactivar validación HTML5 (la validación la hará PHP) -->
    <form id="formLogin" action="control_acceso.php" method="post" novalidate>
      <label for="usuario">Usuario</label><br>
      <input type="text" id="usuario" name="usuario" value="<?php echo $valorUsuario; ?>"><br>
      <div id="errorUsuario" class="mensaje-error"></div>

      <label for="password">Contraseña</label><br>
      <input type="password" id="password" name="password"><br>
      <div id="errorPassword" class="mensaje-error"></div><br>

      <button type="submit" class="button">Acceder</button>
      <a href="signup.php" class="enlaces">Crear cuenta</a>
    </form>
  </fieldset>
</section>

</main>

<?php
require_once("footer.inc");
?>

<!-- <script src="login.js"></script>-->
