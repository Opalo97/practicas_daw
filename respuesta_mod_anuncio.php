<?php
ob_start();
session_start();

$title = "Modificar anuncio";


require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php"); 

// -------------------------------------------------------------------
// 1. CONTROL DE ACCESO
// -------------------------------------------------------------------
if (!isset($_SESSION['idusuario'])) {
    echo "<p>Error: debes iniciar sesión.</p>";
    require_once("footer.inc");
    exit;
}

$idUsuario = $_SESSION['idusuario'];

// -------------------------------------------------------------------
// 2. OBTENER ID DEL ANUNCIO A MODIFICAR
// -------------------------------------------------------------------
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo "<p>Error: anuncio no válido.</p>";
    require_once("footer.inc");
    exit;
}

$mysqli = obtenerConexion();

// -------------------------------------------------------------------
// 3. COMPROBAR QUE EL ANUNCIO PERTENECE AL USUARIO
// -------------------------------------------------------------------
$sql = "SELECT FPrincipal FROM Anuncios WHERE IdAnuncio = ? AND Usuario = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $id, $idUsuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<p>Error: no tienes permiso para modificar este anuncio.</p>";
    require_once("footer.inc");
    exit;
}

$anuncio = $res->fetch_assoc();
$foto_actual = $anuncio['FPrincipal'];
$stmt->close();


// -------------------------------------------------------------------
// 4. VALIDACIÓN DEL FORMULARIO (USAMOS ARCHIVO EXTERNO)
// -------------------------------------------------------------------

// En modificar, la foto NO es obligatoria
$validar_foto = false;

// Esto genera: $errores y todas las variables saneadas ($titulo, $precio, etc.)
require("validar_anuncio.php");

if (!empty($errores)) {
    // Guardamos errores en sesión para mostrarlos en modificar_anuncio.php
    $_SESSION['errores_mod'] = $errores;

    // Redirigir de vuelta al formulario
    header("Location: modificar_anuncio.php?id=$id");
    exit;
}


// -------------------------------------------------------------------
// 5. ACTUALIZAR EN BASE DE DATOS
// -------------------------------------------------------------------

// Si se ha subido una nueva foto principal
$nueva_foto = $foto_actual;
$nuevo_alt  = $alt_foto;

if (!$validar_foto && isset($_FILES['foto_principal']) &&
    $_FILES['foto_principal']['error'] === UPLOAD_ERR_OK) {

    $tmp = $_FILES['foto_principal']['tmp_name'];
    $real = basename($_FILES['foto_principal']['name']);
    $dest = 'img/' . time() . '_' . $real;

    if (move_uploaded_file($tmp, $dest)) {
        $nueva_foto = $dest;

        if ($nuevo_alt === "") {
            $nuevo_alt = "Foto principal del anuncio";
        }

        // También actualizar en tabla FOTOS
        $sqlFoto = "INSERT INTO Fotos (Titulo, Foto, Alternativo, Anuncio)
                    VALUES (?, ?, ?, ?)";

        $stmtFoto = $mysqli->prepare($sqlFoto);
        $tituloFotoNull = null;

        $stmtFoto->bind_param("sssi", 
            $tituloFotoNull,
            $nueva_foto,
            $nuevo_alt,
            $id
        );
        $stmtFoto->execute();
        $stmtFoto->close();
    }
}


// -------------------------------------------------------------------
// ACTUALIZAR ANUNCIO
// -------------------------------------------------------------------

$sqlUpdate = "UPDATE Anuncios
              SET TAnuncio = ?, TVivienda = ?, FPrincipal = ?, Alternativo = ?,
                  Titulo = ?, Precio = ?, Texto = ?, Ciudad = ?, Pais = ?,
                  Superficie = ?, NHabitaciones = ?, NBanyos = ?, Planta = ?, Anyo = ?
              WHERE IdAnuncio = ?";

$stmt2 = $mysqli->prepare($sqlUpdate);
$stmt2->bind_param(
    "iisssdsssiiiiii",
    $tipo_anuncio,
    $tipo_vivienda,
    $nueva_foto,
    $nuevo_alt,
    $titulo,
    $precio,
    $descripcion,
    $ciudad,
    $pais,
    $superficie,
    $habitaciones,
    $banos,
    $planta,
    $anio,
    $id
);

if (!$stmt2->execute()) {
    echo "<p>Error al actualizar: " . htmlspecialchars($stmt2->error) . "</p>";
    require_once("footer.inc");
    exit;
}

$stmt2->close();


// -------------------------------------------------------------------
// 6. MENSAJE FINAL
// -------------------------------------------------------------------
?>

<section>
    <h3>Anuncio modificado correctamente</h3>

    <p>Los cambios se han guardado con éxito.</p>

    <p>
        <a class="button" href="ver_anuncio.php?id=<?= $id ?>">Ver anuncio actualizado</a>
    </p>

    <p>
        <a class="enlaces" href="mis_anuncios.php">Volver a mis anuncios</a>
    </p>
</section>

</main>

<?php
require_once("footer.inc");
?>
