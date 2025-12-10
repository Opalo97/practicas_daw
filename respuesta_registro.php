<?php
session_start();
$title = "Registro completado";
require_once("bd.php");
require_once("flashdata.php");
require_once("filtros.php");
require_once("cabecera.inc");
require_once("inicio2.inc");


// ---------------------------
// Recoger campos
// ---------------------------
$usuario   = $_POST['usuario'] ?? '';
$clave     = $_POST['clave'] ?? '';
$clave2    = $_POST['clave2'] ?? '';
$email     = $_POST['email'] ?? '';
$sexo      = $_POST['sexo'] ?? '';
$fecha     = $_POST['fecha'] ?? '';
$ciudad    = $_POST['ciudad'] ?? '';
$pais      = $_POST['pais'] ?? '';
$foto      = $_FILES['foto'] ?? null;

$errores = [];

// ======================================================
// VALIDAR NOMBRE DE USUARIO
// ======================================================
$usuario = trim($usuario);

if (!preg_match('/^[A-Za-z][A-Za-z0-9]{2,14}$/', $usuario)) {
    $errores[] = "El nombre de usuario debe empezar por letra, tener 3–15 caracteres y solo usar letras y números.";
}

// ======================================================
// VALIDAR CONTRASEÑA
// ======================================================
$claveValida = validar_password_registro($clave);

if ($claveValida === false) {
    $errores[] = "La contraseña debe tener 6–15 caracteres, incluir minúscula, mayúscula, número y sólo usar letras, números, '-' y '_'.";
}

// Repetir contraseña
if ($clave !== $clave2) {
    $errores[] = "Las contraseñas no coinciden.";
}

// ======================================================
// VALIDAR EMAIL
// ======================================================
$emailValidado = validar_email($email);
if ($emailValidado === false) {
    $errores[] = "El email no es válido según el formato exigido.";
}

// ======================================================
// VALIDAR SEXO
// ======================================================
if ($sexo !== "hombre" && $sexo !== "mujer") {
    $errores[] = "Debes seleccionar un sexo.";
}

// ======================================================
// VALIDAR FECHA DE NACIMIENTO + MAYOR DE 18 AÑOS
// ======================================================
$fecha = trim($fecha);

if (!preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fecha)) {
    $errores[] = "La fecha debe tener formato dd/mm/aaaa.";
} else {
    list($d,$m,$a) = explode('/', $fecha);

    if (!checkdate((int)$m, (int)$d, (int)$a)) {
        $errores[] = "La fecha de nacimiento no es válida.";
    } else {
        // Debe tener 18 años recién cumplidos
        $fechaNacimiento = DateTime::createFromFormat('d/m/Y', $fecha);
        $hoy = new DateTime();

        $edad = $hoy->diff($fechaNacimiento)->y;
        if ($edad < 18) {
            $errores[] = "Debes tener al menos 18 años para registrarte.";
        }
    }
}

// ======================================================
// SI HAY ERRORES -> VOLVER A signup.php
// ======================================================
if (!empty($errores)) {
    set_flash('errores', $errores);
    header("Location: signup.php");
    exit;
}

// ======================================================
// INSERTAR EN BD
// ======================================================
$mysqli = obtenerConexion();

// Comprobar si el usuario existe
$stmt = $mysqli->prepare("SELECT IdUsuario FROM usuarios WHERE NomUsuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->close();
    $errores[] = "El nombre de usuario ya existe.";
    set_flash('errores', $errores);
    header("Location: signup.php");
    exit;
}
$stmt->close();

// ======================================================
// PROCESAR FOTO DE PERFIL (OPCIONAL)
// ======================================================
// Construir ruta absoluta del directorio de fotos de usuario
$destUsuarios = __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'usuarios';
// Procesar subida: valida MIME, genera nombre único, mueve el fichero
$fotoProc = procesar_foto_subida($foto, $destUsuarios);
if (!$fotoProc['ok']) {
    // Si hay error en la subida, volver al formulario
    $errores[] = $fotoProc['error'];
    set_flash('errores', $errores);
    header("Location: signup.php");
    exit;
}
// Obtener ruta relativa para guardar en BD (ej: 'img/usuarios/usr_abc123.jpg')
$fotoRuta = $fotoProc['ruta'] ?? null;

// ======================================================
// HASHEAR LA CONTRASEÑA
// ======================================================
// Usar password_hash() con bcrypt (PASSWORD_DEFAULT)
// Esto genera un hash de 60 caracteres que NO se puede revertir
// Así la contraseña no se ve en texto plano en phpMyAdmin
$claveHash = password_hash($claveValida, PASSWORD_DEFAULT);

// Insertar usuario
$sql = "INSERT INTO usuarios 
        (NomUsuario, Clave, Email, Sexo, FNacimiento, Ciudad, Pais, Foto, FRegistro, Estilo)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 2)";

$stmt2 = $mysqli->prepare($sql);

// Convertir fecha a AAAA-MM-DD
$fechaSQL = $a . "-" . $m . "-" . $d;

// Sexo numérico 1 hombre, 2 mujer (según tu BD)
$sexoBD = ($sexo === "hombre") ? 1 : 2;

$stmt2->bind_param(
    "sssisiss",
    $usuario,
    $claveHash,
    $emailValidado,
    $sexoBD,
    $fechaSQL,
    $ciudad,
    $pais,
    $fotoRuta
);

$stmt2->execute();
$stmt2->close();
$mysqli->close();

// ======================================================
// REGISTRO COMPLETADO
// ======================================================

require_once('cabecera.inc');
require_once('inicio2.inc');
?>
<section>
    <article>
        <h2>Registro completado</h2>
        <p>Tu cuenta ha sido creada correctamente.</p>
        <?php /* Mostrar miniatura de la foto subida para que el usuario verifique que es correcta */ ?>
        <?php if (!empty($fotoRuta)): ?>
            <div class="foto-preview">
                <p>Tu foto de perfil subida:</p>
                <img src="<?php echo htmlspecialchars($fotoRuta); ?>" alt="Foto de perfil" style="max-width:200px; border:1px solid #ccc; padding:4px;">
            </div>
        <?php endif; ?>
        <p><a class="button" href="login.php">Iniciar sesión</a></p>
        <p><a class="enlaces" href="index.php">Volver al inicio</a></p>
    </article>
</section>

</main>

<?php require_once('footer.inc'); ?>
?>
