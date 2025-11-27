<?php
ob_start();
session_start();

$title = "Crear un anuncio nuevo";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");

// ===============================
// CONTROL DE ACCESO
// ===============================
if (!isset($_SESSION['idusuario'])) {
    echo "<p>Error: debes iniciar sesión para crear un anuncio.</p>";
    require_once("footer.inc");
    exit;
}

$mysqli = obtenerConexion();


// ===============================
// INICIALIZAR VARIABLES DEL FORM
// ===============================
$tipo_anuncio  = "";
$tipo_vivienda = "";
$titulo        = "";
$precio        = "";
$descripcion   = "";
$ciudad        = "";
$pais          = "";
$fecha         = "";
$superficie    = "";
$habitaciones  = "";
$banos         = "";
$planta        = "";
$anio          = "";
$alt_foto      = "";
$foto_actual   = "";   // en crear siempre vacío
$mostrar_foto_principal = false; // en modificar = true
$accion        = "crear_anuncio.php";
$boton_texto   = "Crear anuncio";


// ===============================
// SI SE HA ENVIADO EL FORM
// ===============================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Evitar que validar_anuncio exija foto si no toca
    $validar_foto = true;

    require("validar_anuncio.php");  // genera $errores y sanea datos

    if (empty($errores)) {

        // ===============================
        // 1. GUARDAR FOTO PRINCIPAL
        // ===============================
        $nombre_tmp = $_FILES['foto_principal']['tmp_name'];
        $nombre_real = basename($_FILES['foto_principal']['name']);
        $destino = 'img/' . time() . '_' . $nombre_real;

        if (!move_uploaded_file($nombre_tmp, $destino)) {
            echo "<p>Error al guardar la imagen.</p>";
            require_once("footer.inc");
            exit;
        }

        if ($alt_foto === "") {
            $alt_foto = "Foto del anuncio";
        }

        // ===============================
        // 2. INSERTAR ANUNCIO
        // ===============================
        $sql = "INSERT INTO Anuncios 
                (TAnuncio, TVivienda, FPrincipal, Alternativo, Titulo, Precio, Texto,
                 Ciudad, Pais, Superficie, NHabitaciones, NBanyos, Planta, Anyo, FRegistro, Usuario)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param(
            "iisssdsssiiiiiis",
            $tipo_anuncio,
            $tipo_vivienda,
            $destino,
            $alt_foto,
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
            $fecha,
            $_SESSION['idusuario']
        );

        if (!$stmt->execute()) {
            echo "<p>Error al insertar el anuncio: " . $stmt->error . "</p>";
            require_once("footer.inc");
            exit;
        }

        $idAnuncioNuevo = $stmt->insert_id;
        $stmt->close();


        // ===============================
        // 3. INSERTAR FOTO PRINCIPAL EN FOTOS
        // ===============================
        $sqlFoto = "INSERT INTO Fotos (Titulo, Foto, Alternativo, Anuncio)
                    VALUES (?, ?, ?, ?)";

        $stmt2 = $mysqli->prepare($sqlFoto);
        $tituloFoto = null;
        $stmt2->bind_param("sssi", $tituloFoto, $destino, $alt_foto, $idAnuncioNuevo);
        $stmt2->execute();
        $stmt2->close();


        // ===============================
        // 4. MENSAJE DE ÉXITO
        // ===============================
        echo "<h2>Anuncio creado correctamente</h2>";
        echo "<p>Tu anuncio ha sido publicado.</p>";

        echo "<p><a class='button' href='anyadir_foto.php?id=$idAnuncioNuevo'>
                Añadir fotos al anuncio
              </a></p>";

        echo "<p><a class='enlaces' href='ver_anuncio.php?id=$idAnuncioNuevo'>
                Ver anuncio
              </a></p>";

        require_once("footer.inc");
        exit;
    }

    // Si hay errores, el form se vuelve a mostrar con los valores POST
}


// ====================================================================
// MOSTRAR FORMULARIO (si no se ha enviado o hubo errores)
// ====================================================================

// Si hubo POST con errores, mantener valores escritos por el usuario:
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Variables ya saneadas en validar_anuncio.php
}

?>

<section>
  <p>
    Completa el siguiente formulario para publicar un nuevo anuncio.  
    Todos los campos marcados con * son obligatorios.
  </p>

  <?php
  if (!empty($errores)) {
      echo "<div class='mensaje-error'><ul>";
      foreach ($errores as $e) echo "<li>" . htmlspecialchars($e) . "</li>";
      echo "</ul></div>";
  }
  ?>

  <?php require("form_anuncio.inc.php"); ?>
</section>

</main>

<?php
$mysqli->close();
require_once("footer.inc");
?>
