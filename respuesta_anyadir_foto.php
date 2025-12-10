<?php
session_start();
require_once('bd.php');
require_once('filtros.php');
require_once('flashdata.php');

// Título para `inicio.inc` (evita "Undefined variable $title" al incluir el inicio)
$title = 'Añadir foto - Resultado';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['idusuario'])) {
    set_flash('error', 'Debes iniciar sesión para añadir fotos.');
    header('Location: login.php');
    exit;
}

$idAnuncioRaw = $_POST['anuncio'] ?? null;
if (is_array($idAnuncioRaw)) {
    // Tomar el último valor si por alguna razón llega como array
    $idAnuncioRaw = end($idAnuncioRaw);
}
$idAnuncio = $idAnuncioRaw !== null ? (int)filter_var($idAnuncioRaw, FILTER_SANITIZE_NUMBER_INT) : 0;
$titulo = $_POST['titulo_foto'] ?? '';
$alt = $_POST['alt'] ?? '';
$ruta_input = trim($_POST['ruta_foto'] ?? '');
$fileUpload = $_FILES['foto'] ?? null;

$errores = [];

if ($idAnuncio <= 0) $errores[] = 'Debes indicar el anuncio al que pertenece la foto.';

$tituloVal = validar_titulo_foto($titulo);
if ($tituloVal === false) $errores[] = 'El título de la foto es obligatorio.';

$altVal = validar_texto_alternativo($alt);
if ($altVal === false) $errores[] = 'El texto alternativo es obligatorio, mínimo 10 caracteres y no puede empezar por "foto" o "imagen".';

// ======================================================
// PROCESAR SUBIDA DE FOTO DE ANUNCIO
// ======================================================
// Dos opciones:
// 1. Usuario sube fichero con <input type="file"> (preferido)
// 2. Usuario indica ruta de fichero ya existente en servidor (fallback, legacy)
$rutaFoto = null;
if (isset($fileUpload) && $fileUpload['error'] !== UPLOAD_ERR_NO_FILE) {
    // OPCIÓN 1: PROCESAR FICHERO SUBIDO
    // Directorio destino para fotos de anuncios
    $destAnun = __DIR__ . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . 'anuncios';
    $userId = (int)$_SESSION['idusuario'];
    // Usar procesar_foto_anuncio() que:
    // - Valida MIME (jpg/png/gif)
    // - Genera nombre único: anun_{userId}_{anuncioId}_{timestamp}_{random}.ext
    // - Mueve el fichero a img/anuncios/
    $proc = procesar_foto_anuncio($fileUpload, $destAnun, $userId, $idAnuncio);
    if (!$proc['ok']) {
        $errores[] = $proc['error'];
    } else {
        $rutaFoto = $proc['ruta'];
    }
} elseif ($ruta_input !== '') {
    // OPCIÓN 2: VALIDAR RUTA EXISTENTE (solo si no se subió fichero)
    // Este es un fallback para ficheros ya subidos manualmente
    $rutaAbs = realpath(__DIR__ . DIRECTORY_SEPARATOR . $ruta_input);
    $base = realpath(__DIR__);
    if ($rutaAbs && strpos($rutaAbs, $base) === 0 && is_file($rutaAbs)) {
        $rutaFoto = $ruta_input;
    } else {
        $errores[] = 'La ruta indicada no existe en el servidor: ' . htmlspecialchars($ruta_input);
    }
} else {
    $errores[] = 'Debes subir una foto o indicar una ruta válida en el servidor.';
}

if (!empty($errores)) {
    set_flash('errores', $errores);
    // Conservamos la selección del anuncio para facilitar corrección del formulario.
    $redir = 'anyadir_foto.php';
    if ($idAnuncio > 0) {
        $redir .= '?anuncio_id=' . $idAnuncio;
    }
    header('Location: ' . $redir);
    exit;
}

$mysqli = obtenerConexion();

// Comprobar que el anuncio existe y pertenece al usuario
$stmt = $mysqli->prepare('SELECT Usuario FROM Anuncios WHERE IdAnuncio = ? LIMIT 1');
$stmt->bind_param('i', $idAnuncio);
$stmt->execute();
$res = $stmt->get_result();
if (!$row = $res->fetch_assoc()) {
    $stmt->close();
    $mysqli->close();
    set_flash('errores', ['Anuncio no encontrado.']);
    header('Location: anyadir_foto.php');
    exit;
}
$propietario = (int)$row['Usuario'];
$stmt->close();

if ($propietario !== (int)$_SESSION['idusuario']) {
    $mysqli->close();
    set_flash('errores', ['No tienes permisos para añadir fotos a este anuncio.']);
    header('Location: anyadir_foto.php');
    exit;
}

// Insertar en la BD
$ins = $mysqli->prepare('INSERT INTO Fotos (Titulo, Foto, Alternativo, Anuncio) VALUES (?, ?, ?, ?)');
$ins->bind_param('sssi', $tituloVal, $rutaFoto, $altVal, $idAnuncio);
$ok = $ins->execute();
$ins->close();
$mysqli->close();

if ($ok) {
        // Mostrar directamente la página de resultado (evita página intermedia adicional)
        require_once('cabecera.inc');
        require_once('inicio.inc');
        ?>
        <section>
            <div class="mensaje-ok">
                <p><?php echo htmlspecialchars('Foto añadida correctamente.'); ?></p>
            </div>

            <?php
            // Mostrar miniatura si la ruta es una imagen válida en el servidor
            $rutaMostrar = $rutaFoto;
            $rutaAbsImg = __DIR__ . DIRECTORY_SEPARATOR . $rutaMostrar;
            if ($rutaMostrar && is_file($rutaAbsImg) && @getimagesize($rutaAbsImg)):
            ?>
              <div class="foto-preview">
                <p>Miniatura de la foto añadida:</p>
                <img src="<?php echo htmlspecialchars($rutaMostrar); ?>" alt="<?php echo htmlspecialchars($altVal); ?>" style="max-width:300px;max-height:250px;display:block;border:1px solid #ccc;padding:4px;">
              </div>
            <?php endif; ?>

            <p>
                <a class="button" href="ver_fotos_priv.php?id=<?php echo (int)$idAnuncio; ?>">Ver fotos del anuncio</a>
                <a class="enlaces" href="mis_anuncios.php">Volver a mis anuncios</a>
            </p>
        </section>
        </main>
        <?php
        require_once('footer.inc');
        exit;
} else {
        // Mostrar error en esta misma página
        require_once('cabecera.inc');
        require_once('inicio.inc');
        ?>
        <section>
            <div class="mensaje-error">
                <p><strong>Errores:</strong></p>
                <ul>
                    <li><?php echo htmlspecialchars('No se pudo guardar la foto en la base de datos.'); ?></li>
                </ul>
            </div>
            <p>
                <a class="button" href="anyadir_foto.php?anuncio_id=<?php echo (int)$idAnuncio; ?>">Volver a intentar</a>
            </p>
        </section>
        </main>
        <?php
        require_once('footer.inc');
        exit;
}
