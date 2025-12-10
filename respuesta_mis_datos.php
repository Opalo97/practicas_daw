<?php
session_start();
require_once('bd.php');
require_once('filtros.php');
require_once('flashdata.php');

if (!isset($_SESSION['usuario']) || !isset($_SESSION['idusuario'])) {
    header('Location: login.php');
    exit;
}

$usuarioAntes = $_SESSION['usuario'];
$idUsuario = (int)$_SESSION['idusuario'];

$usuarioNuevo = $_POST['usuario'] ?? '';
$claveActual = $_POST['clave_actual'] ?? '';
$nuevaClave = $_POST['nueva_clave'] ?? '';
$nuevaClave2 = $_POST['nueva_clave2'] ?? '';
$email = $_POST['email'] ?? '';
$sexo = isset($_POST['sexo']) ? (int)$_POST['sexo'] : null;
$fecha = $_POST['fecha'] ?? '';
$ciudad = $_POST['ciudad'] ?? '';
$pais = $_POST['pais'] ?? '';
$eliminarFoto = isset($_POST['eliminar_foto']) && $_POST['eliminar_foto'] == '1';

$errores = [];

// Validaciones básicas
$usuarioVal = validar_usuario($usuarioNuevo);
if ($usuarioVal === false) $errores[] = 'Nombre de usuario no válido (mínimo 3 caracteres).';

$emailVal = validar_email($email);
if ($emailVal === false) $errores[] = 'Correo electrónico no válido.';

$fechaVal = validar_fecha($fecha);
if ($fechaVal === false) $errores[] = 'Fecha de nacimiento no válida.';

$clavesVal = validar_claves_nuevas($nuevaClave, $nuevaClave2);
if ($clavesVal === false) $errores[] = 'Las nuevas contraseñas no coinciden o son incorrectas (mínimo 4 caracteres).';

if (trim($claveActual) === '') $errores[] = 'Debes introducir tu contraseña actual para confirmar los cambios.';

if (!empty($errores)) {
    set_flash('errores_mis_datos', $errores);
    header('Location: mis_datos.php');
    exit;
}

$mysqli = obtenerConexion();

// Obtener contraseña actual de la BD
$stmt = $mysqli->prepare('SELECT Clave, Foto FROM usuarios WHERE IdUsuario = ? LIMIT 1');
$stmt->bind_param('i', $idUsuario);
$stmt->execute();
$res = $stmt->get_result();
if (!$row = $res->fetch_assoc()) {
    $stmt->close();
    $mysqli->close();
    set_flash('errores_mis_datos', ['Usuario no encontrado.']);
    header('Location: mis_datos.php');
    exit;
}
$claveBD = $row['Clave'];
$fotoActual = $row['Foto'];
$stmt->close();

// ======================================================
// VERIFICAR CONTRASEÑA ACTUAL
// ======================================================
// Usar password_verify() para comparar:
// - $claveActual: texto plano introducido por el usuario
// - $claveBD: hash almacenado en la base de datos
if (!password_verify($claveActual, $claveBD)) {
    $mysqli->close();
    set_flash('errores_mis_datos', ['Contraseña actual incorrecta.']);
    header('Location: mis_datos.php');
    exit;
}

// Procesar foto si hay
$fotoRes = procesar_foto_subida($_FILES['foto'] ?? null, __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'usuarios');
if (!$fotoRes['ok']) {
    set_flash('errores_mis_datos', [$fotoRes['error']]);
    header('Location: mis_datos.php');
    exit;
}

// Comprobar si el nombre de usuario ya existe (y no es el propio)
$stmt = $mysqli->prepare('SELECT IdUsuario FROM usuarios WHERE NomUsuario = ? AND IdUsuario <> ? LIMIT 1');
$stmt->bind_param('si', $usuarioVal, $idUsuario);
$stmt->execute();
$res = $stmt->get_result();
if ($res->fetch_assoc()) {
    $stmt->close();
    $mysqli->close();
    set_flash('errores_mis_datos', ['El nombre de usuario ya está en uso.']);
    header('Location: mis_datos.php');
    exit;
}
$stmt->close();

// Preparar UPDATE
$campos = [];
$params = [];
$types = '';

$campos[] = 'NomUsuario = ?'; $types .= 's'; $params[] = $usuarioVal;
$campos[] = 'Email = ?'; $types .= 's'; $params[] = $emailVal ?? null;
$campos[] = 'Sexo = ?'; $types .= 'i'; $params[] = $sexo ?: null;
$campos[] = 'FNacimiento = ?'; $types .= 's'; $params[] = $fechaVal ?? null;
$campos[] = 'Ciudad = ?'; $types .= 's'; $params[] = limpiar_texto($ciudad) ?: null;
$campos[] = 'Pais = ?'; $types .= 'i'; $params[] = $pais ?: null;

// ======================================================
// HASHEAR NUEVA CONTRASEÑA SI HAY CAMBIO
// ======================================================
if ($clavesVal !== null) {
    // Generar hash seguro de la nueva contraseña
    // PASSWORD_DEFAULT usa bcrypt (60 caracteres)
    $claveHash = password_hash($clavesVal, PASSWORD_DEFAULT);
    $campos[] = 'Clave = ?'; $types .= 's'; $params[] = $claveHash;
}

// ======================================================
// GESTIÓN DE FOTO DE PERFIL
// ======================================================
// Tres opciones posibles:
// 1. Usuario marca "eliminar foto" -> borrar fichero físico y poner NULL en BD
// 2. Usuario sube nueva foto -> borrar anterior, subir nueva, actualizar BD
// 3. No hace nada con la foto -> mantener la actual (no tocar el campo Foto)
if ($eliminarFoto) {
    // OPCIÓN 1: ELIMINAR FOTO
    // Borrar el fichero físico del servidor si existe
    if ($fotoActual) {
        // realpath() obtiene la ruta absoluta real del fichero
        $rutaAbs = realpath(__DIR__ . DIRECTORY_SEPARATOR . $fotoActual);
        // Validar que el fichero está dentro del proyecto (seguridad)
        $base = realpath(__DIR__);
        if ($rutaAbs && strpos($rutaAbs, $base) === 0 && is_file($rutaAbs)) {
            // @ suprime warnings si el fichero ya no existe
            @unlink($rutaAbs);
        }
    }
    // Poner NULL en BD para mostrar el icono por defecto
    $campos[] = 'Foto = NULL';
} elseif (!empty($fotoRes['ruta'])) {
    // OPCIÓN 2: REEMPLAZAR CON NUEVA FOTO
    // Primero borrar la foto anterior del servidor
    if ($fotoActual) {
        $rutaAbs = realpath(__DIR__ . DIRECTORY_SEPARATOR . $fotoActual);
        $base = realpath(__DIR__);
        if ($rutaAbs && strpos($rutaAbs, $base) === 0 && is_file($rutaAbs)) {
            @unlink($rutaAbs);
        }
    }
    // Guardar la ruta de la nueva foto en BD
    $campos[] = 'Foto = ?'; $types .= 's'; $params[] = $fotoRes['ruta'];
}
// OPCIÓN 3: Si no se cumple ninguna condición, no se modifica el campo Foto

$params[] = $idUsuario; $types .= 'i';

$sql = 'UPDATE usuarios SET ' . implode(', ', $campos) . ' WHERE IdUsuario = ?';
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    $mysqli->close();
    set_flash('errores_mis_datos', ['Error interno al preparar la consulta.']);
    header('Location: mis_datos.php');
    exit;
}

 $bind_names = [];
 $bind_names[] = $types;
 for ($i = 0; $i < count($params); $i++) {
     $bind_names[] = &$params[$i];
 }
 call_user_func_array([$stmt, 'bind_param'], $bind_names);

$ok = $stmt->execute();
$stmt->close();
$mysqli->close();

if (!$ok) {
    set_flash('errores_mis_datos', ['No se pudieron guardar los cambios.']);
    header('Location: mis_datos.php');
    exit;
}

// Gestión de cookie 'Recuérdame': por seguridad, es preferible invalidarla si el usuario ha cambiado credenciales
if ($clavesVal !== null || $usuarioVal !== $usuarioAntes) {
    // eliminar cookie (dejarla expirada)
    setcookie('recuerdame', '', time() - 3600, '/', '', false, true);
}

// Actualizar sesión (nombre de usuario y foto si cambiaron)
$_SESSION['usuario'] = $usuarioVal;
// Actualizar la foto en la sesión según la acción realizada
if ($eliminarFoto) {
    // Si se eliminó, quitar de la sesión para que se use el icono por defecto
    unset($_SESSION['foto']);
} elseif (!empty($fotoRes['ruta'])) {
    // Si se subió nueva, actualizar la sesión con la nueva ruta
    $_SESSION['foto'] = $fotoRes['ruta'];
}

set_flash('ok_mis_datos', 'Tus datos se han actualizado correctamente.');
// Mostrar la confirmación
$title = 'Datos actualizados';
require_once('cabecera.inc');
require_once('inicio.inc');
?>

<section>
  <article>
    <h2>Datos actualizados</h2>
    <p>Tus datos se han guardado correctamente.</p>
    <p><a class="enlaces" href="index.php">Volver a inicio</a></p>
    <p><a class="enlaces" href="mis_datos.php">Volver a Mis datos</a></p>
  </article>
</section>

</main>

<?php require_once('footer.inc'); ?>
