<?php
ob_start();
session_start();

$title = "Solicitar folleto publicitario impreso";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");

// ------------------------------------------------------
// 1. Comprobar usuario logueado
// ------------------------------------------------------
if (!isset($_SESSION['usuario'])) {
    header("Location: aviso.php");
    exit;
}

$nombreUsuarioSesion = $_SESSION['usuario'];

// ------------------------------------------------------
// 2. Cargar datos del usuario (para saber su IdUsuario)
// ------------------------------------------------------
$mysqli = obtenerConexion();

$nombreEsc = $mysqli->real_escape_string($nombreUsuarioSesion);

$sqlUsuario = "SELECT IdUsuario, NomUsuario
               FROM Usuarios
               WHERE NomUsuario = '$nombreEsc'
               LIMIT 1";

$resUsu = $mysqli->query($sqlUsuario);

if (!$resUsu || $resUsu->num_rows === 0) {
    echo "<p>No se ha encontrado el usuario actual en la base de datos.</p>";
    if ($resUsu) $resUsu->free();
    $mysqli->close();
    require_once("footer.inc");
    exit;
}

$usuario = $resUsu->fetch_assoc();
$resUsu->free();

$idUsuario = (int)$usuario['IdUsuario'];

// ------------------------------------------------------
// 3. Cargar anuncios del usuario
// ------------------------------------------------------
$anunciosUsuario = [];

$sqlAnuncios = "SELECT IdAnuncio, Titulo
                FROM Anuncios
                WHERE Usuario = $idUsuario
                ORDER BY FRegistro DESC";

if ($resAn = $mysqli->query($sqlAnuncios)) {
    while ($fila = $resAn->fetch_assoc()) {
        $anunciosUsuario[] = $fila;
    }
    $resAn->free();
}
?>

<section aria-labelledby="intro">
  <p>
    A través de este formulario puedes solicitar el envío postal de un folleto publicitario impreso
    basado en uno de tus anuncios. Completa los datos de contacto y envío, elige las opciones de
    impresión y confirma la selección del anuncio. El coste de procesamiento y envío es fijo e
    independiente del número de páginas o de copias.
  </p>
</section>

<!-- Tabla de tarifas -->
<section aria-labelledby="tarifas">
  <h3 id="tarifas">Tarifas</h3>
  <table>
    <thead>
      <tr>
        <th>Concepto</th>
        <th>Tarifa</th>
      </tr>
    </thead>
    <tbody>
      <tr><td>Coste procesamiento y envío</td><td>10 €</td></tr>
      <tr><td>&lt; 5 páginas</td><td>2 € por pág.</td></tr>
      <tr><td>entre 5 y 10 páginas</td><td>1.8 € por pág.</td></tr>
      <tr><td>&gt; 10 páginas</td><td>1.6 € por pág.</td></tr>
      <tr><td>Blanco y negro</td><td>0 €</td></tr>
      <tr><td>Color</td><td>0.5 € por foto</td></tr>
      <tr><td>Resolución &lt;= 300 dpi</td><td>0 € por foto</td></tr>
      <tr><td>Resolución &gt; 300 dpi</td><td>0.2 € por foto</td></tr>
    </tbody>
  </table>
</section>

<!-- Tabla PHP de costes -->
<section aria-labelledby="tarifas">
  <h3 id="tarifas">Tabla de posibles costes del folleto</h3>
  <button id="mostrarTabla" class="button">Mostrar tabla</button>
  
  <div id="contenedorTabla" style="display:none; margin-top:1em;">
    <?php
    // Tarifas
    $coste_envio = 10;
    $precio_color = 0.5;
    $precio_res_alta = 0.2;

    echo "<table>";
    echo "<thead><tr><th>Páginas</th><th>Nº fotos</th><th>B/N 150–300 dpi</th><th>B/N >300 dpi</th><th>Color 150–300 dpi</th><th>Color >300 dpi</th></tr></thead>";
    echo "<tbody>";

    for($paginas=1; $paginas<=15; $paginas++) {
        $fotos = $paginas * 3;

        echo "<tr>";
        echo "<td>$paginas</td>";
        echo "<td>$fotos</td>";

        if($paginas < 5) {
            $precio_pagina = 2.0;
        } elseif($paginas <= 10) {
            $precio_pagina = 1.8;
        } else {
            $precio_pagina = 1.6;
        }

        $combinaciones = [
            ['color'=>false,'altaRes'=>false],
            ['color'=>false,'altaRes'=>true],
            ['color'=>true,'altaRes'=>false],
            ['color'=>true,'altaRes'=>true],
        ];

        foreach($combinaciones as $comb) {
            $coste = $coste_envio + $paginas*$precio_pagina;
            if($comb['color']) $coste += $fotos*$precio_color;
            if($comb['altaRes']) $coste += $fotos*$precio_res_alta;
            echo "<td>".number_format($coste,2)." €</td>";
        }

        echo "</tr>";
    }

    echo "</tbody></table>";
    ?>
  </div>
</section>

<section aria-labelledby="formulario">
  <h3 id="formulario">Formulario de solicitud</h3>

  <form action="respuesta_folleto.php" method="post">
    <!-- Datos de contacto -->
    <fieldset>
      <legend>Datos de contacto</legend>

      <article>
        <label for="nombre">Nombre y apellidos (máx. 200) *</label><br />
        <input id="nombre" name="nombre" type="text" maxlength="200" required placeholder="Nombre" />
      </article>

      <article>
        <label for="email">Correo electrónico (máx. 200) *</label><br />
        <input id="email" name="email" type="email" maxlength="200" required placeholder="Tu correo" />
      </article>

      <article>
        <label for="telefono">Teléfono (opcional)</label><br />
        <input id="telefono" name="telefono" type="tel" inputmode="tel" placeholder="### ## ## ##" />
      </article>

      <article>
        <label for="textoAdicional">Texto adicional (máx. 4000, opcional)</label><br />
        <textarea id="textoAdicional" name="textoAdicional" maxlength="4000" rows="6" placeholder="..."></textarea>
      </article>
    </fieldset>

    <!-- Dirección postal -->
    <fieldset>
      <legend>Dirección de envío</legend>

      <label for="dir_calle">Dirección</label>
      &nbsp;

      <input id="dir_calle" name="calle" type="text" required placeholder="Calle" size="26" maxlength="200">
      &nbsp;
      <input id="dir_numero" name="numero" type="text" required placeholder="Número" size="6" maxlength="10">
      &nbsp;
      <input id="dir_cp" name="cp" type="text" required placeholder="CP" size="6" maxlength="10">
      &nbsp;
      <input id="dir_localidad" name="localidad" type="text" required placeholder="Localidad" size="6" maxlength="10">
      &nbsp;
      <input id="dir_provincia" name="provincia" type="text" required placeholder="Provincia" size="6" maxlength="10">
      &nbsp;(*)
    </fieldset>

    <!-- Opciones de impresión -->
    <fieldset>
      <legend>Opciones de impresión</legend>

      <article>
        <label for="colorPortada">Color de la portada (por defecto negro)</label><br />
        <input id="colorPortada" name="colorPortada" type="color" value="#000000" />
      </article>

      <article>
        <label for="copias">Número de copias (1–99)</label><br />
        <input id="copias" name="copias" type="number" min="1" max="99" value="1" />
      </article>

      <article>
        <label for="resolucion">Resolución de las fotos (150–900 DPI)</label><br>
        <input id="resolucion" name="resolucion" type="number" min="150" max="900" step="150" value="150"
          inputmode="numeric" placeholder="150"> DPI
      </article>

      <article>
        <p>Impresión a color *</p>
        <input type="radio" id="bn" name="impresionColor" value="bn" required />
        <label for="bn">Blanco y negro</label>
        <input type="radio" id="color" name="impresionColor" value="color" />
        <label for="color">Color</label>
      </article>

      <article>
        <p>Impresión del precio *</p>
        <input type="radio" id="precioSi" name="imprimirPrecio" value="si" required />
        <label for="precioSi">Con precio</label>
        <input type="radio" id="precioNo" name="imprimirPrecio" value="no" />
        <label for="precioNo">Sin precio</label>
      </article>
    </fieldset>

    <!-- Selección de anuncio -->
    <fieldset>
      <legend>Anuncio del usuario (obligatorio)</legend>
      <p>Selecciona el anuncio propio en el que se basará el folleto publicitario impreso.</p>
      <label for="anuncioUsuario">Anuncio *</label><br />
      <select id="anuncioUsuario" name="anuncioUsuario" required>
        <option value="" selected>— Selecciona tu anuncio —</option>
        <?php if (!empty($anunciosUsuario)): ?>
          <?php foreach ($anunciosUsuario as $an): 
            $idAnuncio  = (int)$an['IdAnuncio'];
            $tituloAn   = htmlspecialchars($an['Titulo'] ?? '', ENT_QUOTES, 'UTF-8');
          ?>
            <option value="<?php echo $idAnuncio; ?>">
              <?php echo $tituloAn; ?>
            </option>
          <?php endforeach; ?>
        <?php else: ?>
          <option value="">(No tienes anuncios publicados)</option>
        <?php endif; ?>
      </select>
    </fieldset>

    <!-- Logística de envío -->
    <fieldset>
      <legend>Logística de envío</legend>
      <label for="fechaRecepcion">Fecha aproximada de recepción (opcional)</label><br />
      <input id="fechaRecepcion" name="fechaRecepcion" type="date" />
    </fieldset>

    <!-- Envío -->
    <section>
      <article>
        <button type="submit" class="button">Solicitar folleto</button>
        <button type="reset" class="button">Limpiar formulario</button>
      </article>
    </section>
  </form>
</section>

</main>

<?php
$mysqli->close();
require_once("footer.inc");
?>
