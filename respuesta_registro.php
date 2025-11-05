<?php
// Desactiva warnings por campos vacíos opcionales
error_reporting(E_ALL & ~E_NOTICE);

// Recogemos los valores del formulario
$usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : "";
$clave = isset($_POST['clave']) ? trim($_POST['clave']) : "";
$clave2 = isset($_POST['clave2']) ? trim($_POST['clave2']) : "";
$email = isset($_POST['email']) ? trim($_POST['email']) : "";
$sexo = isset($_POST['sexo']) ? $_POST['sexo'] : "";
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : "";
$ciudad = isset($_POST['ciudad']) ? trim($_POST['ciudad']) : "";
$pais = isset($_POST['pais']) ? $_POST['pais'] : "";

 
// validacioness
 
$errores = [];

if ($usuario === "" || ctype_space($usuario)) {
  $errores[] = "El nombre de usuario es obligatorio.";
}

if ($clave === "" || ctype_space($clave)) {
  $errores[] = "La contraseña es obligatoria.";
}

if ($clave2 === "" || ctype_space($clave2)) {
  $errores[] = "Debes repetir la contraseña.";
}

if ($clave !== "" && $clave2 !== "" && $clave !== $clave2) {
  $errores[] = "Las contraseñas no coinciden.";
}

 
// SI HAY ERRORES → REDIRECCIÓN A signup.php
 
if (!empty($errores)) {
  // Codificamos los mensajes de error como query string
  $errorString = urlencode(implode("||", $errores));
  header("Location: signup.php?error=" . $errorString);
  exit();
}

 
// SI TODO ESTÁ CORRECTO → MOSTRAR CONFIRMACIÓN
 
$title = "Confirmación de registro";
require_once("cabecera.inc");
require_once("inicio.inc");
?>

<section>
  
  <p>Estos son los datos introducidos:</p>

  <fieldset>
    <legend>Datos de acceso</legend>
    <dl>
      <dt>Nombre de usuario:</dt>
      <dd><?php echo htmlspecialchars($usuario); ?></dd>

      <dt>Contraseña:</dt>
      <dd><?php echo str_repeat("•", strlen($clave)); // No mostrar texto real ?></dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Datos personales</legend>
    <dl>
      <dt>Correo electrónico:</dt>
      <dd><?php echo htmlspecialchars($email ?: "No especificado"); ?></dd>

      <dt>Sexo:</dt>
      <dd><?php echo $sexo ? ucfirst(htmlspecialchars($sexo)) : "No indicado"; ?></dd>

      <dt>Fecha de nacimiento:</dt>
      <dd><?php echo htmlspecialchars($fecha ?: "No indicada"); ?></dd>

      <dt>Ciudad:</dt>
      <dd><?php echo htmlspecialchars($ciudad ?: "No indicada"); ?></dd>

      <dt>País:</dt>
      <dd><?php echo htmlspecialchars($pais ?: "No indicado"); ?></dd>
    </dl>
  </fieldset>

  <p><a class="enlaces" href="index.php">Volver a la página principal</a></p>
</section>

</main>

<?php
require_once("footer.inc");
?>
