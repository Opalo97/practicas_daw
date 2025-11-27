<?php
ob_start();
session_start();

$title = "Modificar anuncio";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");

// =======================================
// CONTROL DE ACCESO
// =======================================
if (!isset($_SESSION['idusuario'])) {
    echo "<p>Error: debes iniciar sesión para modificar un anuncio.</p>";
    require_once("footer.inc");
    exit;
}

$idUsuario = $_SESSION['idusuario'];

// =======================================
// OBTENER ID DEL ANUNCIO
// =======================================
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo "<p>Error: anuncio no válido.</p>";
    require_once("footer.inc");
    exit;
}

$mysqli = obtenerConexion();

// =======================================
// COMPROBAR QUE EL ANUNCIO PERTENECE AL USUARIO
// =======================================
$sql = "SELECT *
        FROM Anuncios 
        WHERE IdAnuncio = ? AND Usuario = ?
        LIMIT 1";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $id, $idUsuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>Error: no tienes permiso para modificar este anuncio.</p>";
    require_once("footer.inc");
    exit;
}

$anuncio = $result->fetch_assoc();
$stmt->close();


// =======================================
// CARGAR VALORES ACTUALES EN VARIABLES
// =======================================

$tipo_anuncio  = $anuncio['TAnuncio'];
$tipo_vivienda = $anuncio['TVivienda'];
$titulo        = $anuncio['Titulo'];
$precio        = $anuncio['Precio'];
$descripcion   = $anuncio['Texto'];
$ciudad        = $anuncio['Ciudad'];
$pais          = $anuncio['Pais'];
$fecha         = substr($anuncio['FRegistro'], 0, 10); // formato date

$superficie    = $anuncio['Superficie'];
$habitaciones  = $anuncio['NHabitaciones'];
$banos         = $anuncio['NBanyos'];
$planta        = $anuncio['Planta'];
$anio          = $anuncio['Anyo'];

$foto_actual   = $anuncio['FPrincipal'];
$alt_foto      = $anuncio['Alternativo'];

$mostrar_foto_principal = true;
$accion      = "respuesta_mod_anuncio.php?id=$id";
$boton_texto = "Guardar cambios";


// =======================================
// MOSTRAR FORMULARIO
// =======================================

?>
<section>
   
    <p>A continuación puedes editar los datos de tu anuncio.  
       Los campos marcados con * son obligatorios.</p>

    <?php
    // mostrar errores si vienen de respuesta_mod_anuncio
    if (isset($_SESSION['errores_mod'])) {
        echo "<div class='mensaje-error'><ul>";
        foreach ($_SESSION['errores_mod'] as $e) {
            echo "<li>" . htmlspecialchars($e) . "</li>";
        }
        echo "</ul></div>";
        unset($_SESSION['errores_mod']);
    }
    ?>

    <?php require("form_anuncio.inc.php"); ?>
</section>

</main>

<?php
$mysqli->close();
require_once("footer.inc");
?>
