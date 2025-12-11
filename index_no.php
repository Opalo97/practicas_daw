<?php
$title = "Bienvenido";
require_once("cabecera.inc");
require_once("inicio2.inc");
require_once("bd.php"); // conexión con la BD

// ---------- ANUNCIO RECOMENDADO ALEATORIO ----------
// Función para obtener el anuncio escogido aleatoriamente desde el fichero
function obtenerAnuncioEscogido() {
    $fichero = 'anuncios_escogidos.txt';  // Ruta del fichero de anuncios escogidos
    $lineas = file($fichero, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);  // Leer el archivo
    if ($lineas === false) {
        return false;  // Si no se puede leer el archivo, retornar false
    }

    // Elegir una línea aleatoria del fichero
    $anuncioAleatorio = $lineas[array_rand($lineas)];

    // Dividir la línea por '|' para obtener el IdAnuncio, NombrePersona y Comentario
    list($idAnuncio, $nombrePersona, $comentario) = explode('|', $anuncioAleatorio);

    // Eliminar espacios innecesarios
    $idAnuncio = trim($idAnuncio);
    $nombrePersona = trim($nombrePersona);
    $comentario = trim($comentario);

    // Verificar si el IdAnuncio existe en la base de datos
    if (!existeAnuncioEnBD($idAnuncio)) {
        // Si no existe, elegir otra opción : mostrar anuncio genérico
        return obtenerAnuncioGenerico();
    }

    // Obtener más información del anuncio desde la base de datos
    return obtenerDetallesAnuncio($idAnuncio, $nombrePersona, $comentario);
}

// Función para comprobar si el IdAnuncio existe en la base de datos
function existeAnuncioEnBD($idAnuncio) {
    // Conectar a la base de datos
    $mysqli = obtenerConexion();

    // Consultar si el anuncio con ese IdAnuncio existe
    $sql = "SELECT COUNT(*) FROM Anuncios WHERE IdAnuncio = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $idAnuncio);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    // Si el contador es mayor que 0, el anuncio existe
    return $count > 0;
}

// Función para obtener un anuncio genérico si el IdAnuncio no existe
function obtenerAnuncioGenerico() {
    // Aquí definimos un anuncio genérico con un mensaje
    return [
        'id' => 0,
        'titulo' => 'Anuncio no disponible',
        'foto' => 'img/foto_piso.jpg',  // Imagen predeterminada
        'ciudad' => 'Desconocida',
        'pais' => 'Desconocido',
        'precio' => 0,
        'nombrePersona' => 'Experto',
        'comentario' => 'Este anuncio ya no está disponible, por favor consulte otros anuncios.'
    ];
}

// Función para obtener los detalles del anuncio desde la base de datos
function obtenerDetallesAnuncio($idAnuncio, $nombrePersona, $comentario) {
    // Conectar a la base de datos
    $mysqli = obtenerConexion();

    // Consultar los detalles del anuncio
    $sql = "SELECT a.IdAnuncio, a.Titulo, a.FPrincipal, a.Ciudad, p.NomPais AS Pais, a.Precio
            FROM Anuncios a
            LEFT JOIN Paises p ON a.Pais = p.IdPais
            WHERE a.IdAnuncio = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $idAnuncio);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        
        // Recoger los datos del anuncio
        $anuncio = [
            'id' => $fila['IdAnuncio'],
            'titulo' => $fila['Titulo'],
            'foto' => $fila['FPrincipal'],
            'ciudad' => $fila['Ciudad'],
            'pais' => $fila['Pais'],
            'precio' => $fila['Precio'],
            'nombrePersona' => $nombrePersona,
            'comentario' => $comentario
        ];
        
        return $anuncio;
    } else {
        return false;  // Si no hay resultados, retornar false
    }
}

// ---------- CONSEJOS COMPRA/VENTA ----------

// Función para obtener un consejo aleatorio del fichero JSON
function obtenerConsejoAleatorio() {
    $fichero = 'consejos.json';  // Ruta del fichero JSON con los consejos
    $consejos = json_decode(file_get_contents($fichero), true);  // Leer el archivo JSON y convertirlo en un array

    if ($consejos === null) {
        return false;  // Si el fichero no puede ser leído o tiene un formato incorrecto, retornar false
    }

    // Elegir un consejo aleatorio
    $consejoAleatorio = $consejos[array_rand($consejos)];

    return $consejoAleatorio;
}

?>



<section>
  <h3>¿Quieres hacer una búsqueda más concreta y completa?</h3>
  <form action="f.busqueda.php" method="get">
    <button type="submit" class="button">Búsqueda completa</button>
  </form>
</section>

<!-- Búsqueda rápida -->
<section>
  <h3>Búsqueda rápida</h3>
  <form action="resultado.php" method="get">
    
    <input
      type="text"
      id="busqueda_rapida"
      name="ciudad_rapida"
      placeholder="Ej: vivienda alquiler alicante"
    >
    <button type="submit" class="button">Buscar</button>
  </form>
</section>

<!-- Anuncio recomendado aleatorio -->

<?php
// Llamada a la función que obtiene el anuncio seleccionado
$anuncio = obtenerAnuncioEscogido();

if ($anuncio !== false) {
    // Mostrar la información del anuncio
    ?>
    <section>
        <h3>Anuncio seleccionado por los expertos</h3>
        <div class="anuncio-escogido">
            <div class="imagen">
                <img src="<?php echo $anuncio['foto']; ?>" alt="Foto del anuncio">
            </div>
            <div class="informacion">
                <h4><?php echo $anuncio['titulo']; ?></h4>
                <p>Ciudad: <?php echo $anuncio['ciudad']; ?>, País: <?php echo $anuncio['pais']; ?></p>
                <p>Precio: <?php echo number_format($anuncio['precio'], 2, ',', '.'); ?> €</p>
                <p><strong>Seleccionado por:</strong> <?php echo $anuncio['nombrePersona']; ?></p>
                <p><em>Comentario:</em> <?php echo $anuncio['comentario']; ?></p>
            </div>
        </div>
    </section>
    <?php
} else {
    echo "<p>No se pudo obtener el anuncio seleccionado.</p>";
}
?>


<!-- Consejo de compra/venta aleatorio -->
 <?php
// Llamada a la función que obtiene el consejo seleccionado
$consejo = obtenerConsejoAleatorio();

if ($consejo !== false) {
    // Mostrar la información del consejo
    ?>
    <section>
        <h3>Consejo de compra/venta</h3>
        <div class="consejo">
            <p><strong>Categoría:</strong> <?php echo htmlspecialchars($consejo['categoria'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Importancia:</strong> <?php echo htmlspecialchars($consejo['importancia'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($consejo['descripcion'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </section>
    <?php
} else {
    echo "<p>No se pudo obtener un consejo de compra/venta.</p>";
}
?>



<section>
  <h3>Últimos anuncios publicados</h3>

  <?php
  // Obtenemos los 5 últimos anuncios según FECHA DE REGISTRO
  $mysqli = obtenerConexion();

  $sql = "SELECT 
              a.IdAnuncio,
              a.Titulo,
              a.FPrincipal,
              a.Alternativo,
              a.FRegistro,
              a.Ciudad,
              p.NomPais AS Pais,
              a.Precio
          FROM Anuncios a
          LEFT JOIN Paises p ON a.Pais = p.IdPais
          ORDER BY a.FRegistro DESC
          LIMIT 5";

  if ($resultado = $mysqli->query($sql)) {
      if ($resultado->num_rows > 0) {
          while ($fila = $resultado->fetch_assoc()) {
              $id        = (int)$fila['IdAnuncio'];
              $titulo    = htmlspecialchars($fila['Titulo'] ?? '', ENT_QUOTES, 'UTF-8');
              $foto      = htmlspecialchars($fila['FPrincipal'] ?? '', ENT_QUOTES, 'UTF-8');
              $alt       = htmlspecialchars($fila['Alternativo'] ?? '', ENT_QUOTES, 'UTF-8');
              $fechaRaw  = $fila['FRegistro'];
              $fecha     = $fechaRaw ? date('d/m/Y', strtotime($fechaRaw)) : '';
              $ciudad    = htmlspecialchars($fila['Ciudad'] ?? '', ENT_QUOTES, 'UTF-8');
              $pais      = htmlspecialchars($fila['Pais'] ?? '', ENT_QUOTES, 'UTF-8');
              $precio    = $fila['Precio'];
              $precioFmt = is_null($precio) ? '' : number_format($precio, 2, ',', '.');
              ?>
              
              <article>
                <div class="imagen_principal">
                  <?php if (!empty($foto)) { ?>
                    <img src="<?php echo $foto; ?>" alt="<?php echo $alt; ?>">
                  <?php } else { ?>
                    <img src="img/foto_piso.jpg" alt="Sin imagen disponible">
                  <?php } ?>
                </div>
                <h4><?php echo $titulo; ?></h4>
                <p>
                  Fecha: <?php echo $fecha; ?><br>
                  Ciudad: <?php echo $ciudad; ?><br>
                  País: <?php echo $pais; ?><br>
                  Precio: <?php echo $precioFmt; ?> €
                </p>
                <p><a class="enlaces" href="detalle_anuncio.php?id=<?php echo $id; ?>">Ver detalle</a></p>
              </article>

              <?php
          }
      } else {
          echo "<p>No hay anuncios publicados todavía.</p>";
      }
      $resultado->free();
  } else {
      echo "<p>Error al obtener los anuncios.</p>";
  }

  $mysqli->close();
  ?>

</section>

</main>

<?php
require_once("panel_ultimos_anuncios.php");
require_once("footer.inc");
?>
