<?php
$title = "Confirmación de la solicitud de folleto";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");

// Tarifas
$COSTE_ENVIO = 10;
$TARIFAS_PAGINAS = [
  ['min' => 1, 'max' => 4, 'precio' => 2.0],
  ['min' => 5, 'max' => 10, 'precio' => 1.8],
  ['min' => 11, 'max' => 100, 'precio' => 1.6]
];
$PRECIO_COLOR = 0.5;
$PRECIO_RES_ALTA = 0.2;

// Parámetro configurable: fotos por página
$FOTOS_POR_PAGINA = 3;

// Datos enviados
$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$textoAdicional = trim($_POST['textoAdicional'] ?? '');
$calle = trim($_POST['calle'] ?? '');
$numero = trim($_POST['numero'] ?? '');
$cp = trim($_POST['cp'] ?? '');
$localidad = trim($_POST['localidad'] ?? '');
$provincia = trim($_POST['provincia'] ?? '');
$colorPortada = $_POST['colorPortada'] ?? '#000000';
$copias = max(1, intval($_POST['copias'] ?? 1));
$resolucion = intval($_POST['resolucion'] ?? 150);
$impresionColor = $_POST['impresionColor'] ?? 'bn';
$imprimirPrecio = $_POST['imprimirPrecio'] ?? 'si';
$anuncioUsuario = intval($_POST['anuncioUsuario'] ?? 0);
$fechaRecepcion = $_POST['fechaRecepcion'] ?? '';

// Validaciones básicas
$errores = [];
if ($anuncioUsuario <= 0) $errores[] = 'Debes seleccionar un anuncio válido.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Correo electrónico no válido.';
if (mb_strlen($nombre) < 1) $errores[] = 'Nombre inválido.';
if ($copias < 1 || $copias > 99) $errores[] = 'Número de copias fuera de rango.';
if ($resolucion < 150 || $resolucion > 900) $errores[] = 'Resolución fuera de rango.';

// Conexión para contar fotos y verificar anuncio
$mysqli = obtenerConexion();
$stmtCheck = $mysqli->prepare('SELECT Usuario FROM Anuncios WHERE IdAnuncio = ? LIMIT 1');
$stmtCheck->bind_param('i', $anuncioUsuario);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();
if (!$rowCheck = $resCheck->fetch_assoc()) {
  $stmtCheck->close();
  $mysqli->close();
  $errores[] = 'Anuncio no encontrado.';
} else {
  $stmtCheck->close();
  // Contar fotos reales del anuncio
  $stmtFotos = $mysqli->prepare('SELECT COUNT(*) AS n FROM Fotos WHERE Anuncio = ?');
  $stmtFotos->bind_param('i', $anuncioUsuario);
  $stmtFotos->execute();
  $resFotos = $stmtFotos->get_result();
  $nFotos = 0;
  if ($filaF = $resFotos->fetch_assoc()) $nFotos = (int)$filaF['n'];
  $stmtFotos->close();

  // Calcular páginas a partir del número de fotos
  $paginas = (int)ceil($nFotos / max(1, $FOTOS_POR_PAGINA));
  $fotos = $nFotos;
}

// Calcular coste unitario
// Determinar precio por página según tramo
$precioPagina = 0;
foreach($TARIFAS_PAGINAS as $tramo){
    if($paginas >= $tramo['min'] && $paginas <= $tramo['max']){
        $precioPagina = $tramo['precio'];
        break;
    }
}

$costeUnitario = $COSTE_ENVIO + ($precioPagina * $paginas);
if($impresionColor === 'color') $costeUnitario += $fotos * $PRECIO_COLOR;
if($resolucion > 300) $costeUnitario += $fotos * $PRECIO_RES_ALTA;

// Calcular coste total
$costeTotal = $costeUnitario * $copias;

// Si no hay errores, insertar la solicitud en la BD
if (empty($errores)) {
  $dirCompleta = trim($calle . ' ' . $numero . ', ' . $cp . ' ' . $localidad . ', ' . $provincia);
  $iColor = ($impresionColor === 'color') ? 1 : 0;
  $iPrecio = ($imprimirPrecio === 'si') ? 1 : 0;

  $ins = $mysqli->prepare('INSERT INTO Solicitudes (Anuncio, Texto, Nombre, Email, Direccion, Telefono, Color, Copias, Resolucion, Fecha, IColor, IPrecio, Coste) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
  if ($ins) {
      $ins->bind_param('issssssiisiid', $anuncioUsuario, $textoAdicional, $nombre, $email, $dirCompleta, $telefono, $colorPortada, $copias, $resolucion, $fechaRecepcion, $iColor, $iPrecio, $costeTotal);
    $execOk = $ins->execute();
    $ins->close();
    if (!$execOk) $errores[] = 'No se pudo guardar la solicitud en la base de datos.';
  } else {
    $errores[] = 'Error interno preparando la inserción.';
  }
  $mysqli->close();
}
?>

<section aria-labelledby="conf">
  <p>
    Hemos recibido tu solicitud para el envío postal de un folleto publicitario impreso.
    A continuación se muestra el resumen de la información facilitada. Si algún dato no es correcto,
    puedes volver al formulario y enviarlo de nuevo.
  </p>
</section>

<section aria-labelledby="resumen">
  <h3 id="resumen">Resumen de la solicitud</h3>

  <fieldset>
    <legend>Datos de contacto</legend>
    <dl>
      <dt>Nombre y apellidos</dt><dd><?= htmlspecialchars($nombre) ?></dd>
      <dt>Correo electrónico</dt><dd><?= htmlspecialchars($email) ?></dd>
      <dt>Teléfono</dt><dd><?= htmlspecialchars($telefono) ?></dd>
      <dt>Texto adicional</dt><dd><?= nl2br(htmlspecialchars($textoAdicional)) ?></dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Dirección de envío</legend>
    <dl>
      <dt>Calle / Vía</dt><dd><?= htmlspecialchars($calle) ?></dd>
      <dt>Número</dt><dd><?= htmlspecialchars($numero) ?></dd>
      <dt>Código Postal</dt><dd><?= htmlspecialchars($cp) ?></dd>
      <dt>Localidad</dt><dd><?= htmlspecialchars($localidad) ?></dd>
      <dt>Provincia</dt><dd><?= htmlspecialchars($provincia) ?></dd>
      <dt>País</dt><dd>España</dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Opciones de impresión</legend>
    <dl>
      <dt>Color de la portada</dt>
      <dd><?= htmlspecialchars($colorPortada) ?> &nbsp;<input type="color" value="<?= htmlspecialchars($colorPortada) ?>" disabled></dd>
      <dt>Número de copias</dt><dd><?= $copias ?></dd>
      <dt>Resolución (DPI)</dt><dd><?= $resolucion ?></dd>
      <dt>Impresión</dt><dd><?= $impresionColor === 'color' ? 'Color' : 'Blanco y negro' ?></dd>
      <dt>Imprimir precio</dt><dd><?= $imprimirPrecio === 'si' ? 'Sí' : 'No' ?></dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Anuncio y logística</legend>
    <dl>
      <dt>Anuncio seleccionado</dt><dd><?= htmlspecialchars($anuncioUsuario) ?></dd>
      <dt>Fecha aproximada de recepción</dt><dd><?= htmlspecialchars($fechaRecepcion) ?></dd>
    </dl>
  </fieldset>
</section>

<section aria-labelledby="coste">
  <h3 id="coste">Coste del folleto</h3>
  <fieldset>
    <legend>Importe total</legend>
    <dt><?= number_format($costeTotal, 2) ?> €</dt>
  </fieldset>
  <?php if (!empty($errores)): ?>
    <div class="mensaje-error">
      <p><strong>Errores detectados:</strong></p>
      <ul>
        <?php foreach ($errores as $e): ?>
          <li><?php echo htmlspecialchars($e); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
</section>

<section aria-labelledby="acciones">
  <p><a class="enlaces" href="solicitar_folleto.php">Volver al formulario</a></p>
</section>

</main>

<?php
require_once("footer.inc");
?>
