<?php
$title = "Confirmación de la solicitud de folleto";
require_once("cabecera.inc");
require_once("inicio.inc");

// Tarifas
$COSTE_ENVIO = 10;
$TARIFAS_PAGINAS = [
    ['min' => 1, 'max' => 4, 'precio' => 2.0],
    ['min' => 5, 'max' => 10, 'precio' => 1.8],
    ['min' => 11, 'max' => 100, 'precio' => 1.6] // Asumimos máximo 100 páginas
];
$PRECIO_COLOR = 0.5;
$PRECIO_RES_ALTA = 0.2;

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
$anuncioUsuario = $_POST['anuncioUsuario'] ?? '';
$fechaRecepcion = $_POST['fechaRecepcion'] ?? '';

// Valores ficticios
$paginas = 5; // Número de páginas ficticio
$fotos = 3;   // Número de fotos ficticio

// Calcular coste unitario
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

$costeTotal = $costeUnitario * $copias;
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
</section>

<section aria-labelledby="acciones">
  <p><a class="enlaces" href="solicitar_folleto.php">Volver al formulario</a></p>
</section>

</main>

<?php
require_once("footer.inc");
?>
