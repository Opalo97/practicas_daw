<?php
ob_start();
session_start();

$title = "Crear un anuncio nuevo";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");
require_once("filtros.php");

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

        // ===================================================================
        // ===============================
        // 1. INSERTAR ANUNCIO PRIMERO
        // ===============================
        // Necesitamos el IdAnuncio para generar el nombre único de la foto
        // Por eso insertamos el anuncio con FPrincipal = NULL temporalmente
        // ===================================================================
        $sql = "INSERT INTO Anuncios 
                (TAnuncio, TVivienda, FPrincipal, Alternativo, Titulo, Precio, Texto,
                 Ciudad, Pais, Superficie, NHabitaciones, NBanyos, Planta, Anyo, FRegistro, Usuario)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $mysqli->prepare($sql);
        // Usar valores temporales para FPrincipal y Alternativo, los actualizaremos después
        $fprincipalTemp = NULL;
        $alternativoTemp = $alt_foto ?: "Foto del anuncio";
        
        $stmt->bind_param(
            "iisssdsssiiiiiis",
            $tipo_anuncio,
            $tipo_vivienda,
            $fprincipalTemp,
            $alternativoTemp,
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


        // ===================================================================
        // ===============================
        // 2. PROCESAR FOTO PRINCIPAL
        // ===============================
        // Si el usuario subió una foto, procesarla y actualizar el anuncio
        // Usa la misma estrategia anti-colisiones que "añadir foto a anuncio"
        // ===================================================================
        $destino = NULL;
        if (isset($_FILES['foto_principal']) && $_FILES['foto_principal']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Directorio destino
            $destAnun = __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'anuncios';
            $userId = (int)$_SESSION['idusuario'];
            // Procesar subida con nombre único: anun_{userId}_{anuncioId}_{timestamp}_{random}.ext
            $proc = procesar_foto_anuncio($_FILES['foto_principal'], $destAnun, $userId, $idAnuncioNuevo);
            
            if (!$proc['ok']) {
                // Si falla la subida, hacer rollback: eliminar el anuncio que acabamos de crear
                $mysqli->query("DELETE FROM Anuncios WHERE IdAnuncio = $idAnuncioNuevo");
                echo "<p>Error al guardar la foto: " . htmlspecialchars($proc['error']) . "</p>";
                require_once("footer.inc");
                exit;
            }
            
            $destino = $proc['ruta'];
            
            // Actualizar el anuncio con la ruta real de la foto
            $sqlUpdate = "UPDATE Anuncios SET FPrincipal = ? WHERE IdAnuncio = ?";
            $stmtUpd = $mysqli->prepare($sqlUpdate);
            $stmtUpd->bind_param("si", $destino, $idAnuncioNuevo);
            $stmtUpd->execute();
            $stmtUpd->close();
        }


        // ===================================================================
        // ===============================
        // 3. INSERTAR EN TABLA FOTOS
        // ===============================
        // Además de FPrincipal, guardar también en la tabla Fotos
        // para que aparezca en el listado de fotos del anuncio
        // ===================================================================
        if ($destino) {
            $sqlFoto = "INSERT INTO Fotos (Titulo, Foto, Alternativo, Anuncio)
                        VALUES (?, ?, ?, ?)";

            $stmt2 = $mysqli->prepare($sqlFoto);
            $tituloFoto = $titulo; // Usar el título del anuncio como título de la foto
            $alternativoFoto = $alt_foto ?: "Foto del anuncio";
            $stmt2->bind_param("sssi", $tituloFoto, $destino, $alternativoFoto, $idAnuncioNuevo);
            $stmt2->execute();
            $stmt2->close();
        }


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
