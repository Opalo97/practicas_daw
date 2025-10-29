<?php
$title = "";
require_once("cabecera.inc");
require_once("inicio2.inc");
?>

<section>
  <fieldset>
    <legend>Iniciar sesión</legend>

    <form id="formLogin" action="index.php" method="post" novalidate>
      <label for="usuario">Usuario</label><br>
      <input type="text" id="usuario" name="usuario"><br>
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

<script src="login.js"></script>
