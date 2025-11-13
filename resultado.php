<?php
$title = "Resultados de la búsqueda";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");

// ----------------------------------------------------
// Conexión a la BD
// ----------------------------------------------------
$mysqli = obtenerConexion();

// Para mostrar un resumen de criterios:
$criterioTipoTexto     = 'No especificado';
$criterioViviendaTexto = 'No especificado';
$criterioCiudadTexto   = 'No especificado';
$criterioPaisTexto     = 'No especificado';
$criterioPrecioTexto   = 'No especificado';
$criterioFechaTexto    = 'No especificado';

// Para construir la consulta
$where = [];

// ----------------------------------------------------
// 1. Cargar mapas de tipos y viviendas (nombre -> id)
//    para poder usarlos en la búsqueda rápida.
// ----------------------------------------------------
$tiposAnuncioPorNombre = [];
$resTA = $mysqli->query("SELECT IdTAnuncio, NomTAnuncio FROM TiposAnuncios");
if ($resTA) {
    while ($row = $resTA->fetch_assoc()) {
        $clave = mb_strtolower($row['NomTAnuncio'], 'UTF-8'); // ej: 'venta', 'alquiler'
        $tiposAnuncioPorNombre[$clave] = (int)$row['IdTAnuncio'];
    }
    $resTA->free();
}

$tiposViviendaPorNombre = [];
$resTV = $mysqli->query("SELECT IdTVivienda, NomTVivienda FROM TiposViviendas");
if ($resTV) {
    while ($row = $resTV->fetch_assoc()) {
        $clave = mb_strtolower($row['NomTVivienda'], 'UTF-8'); // ej: 'vivienda', 'oficina'
        $tiposViviendaPorNombre[$clave] = (int)$row['IdTVivienda'];
    }
    $resTV->free();
}

// ----------------------------------------------------
// 2. Detectar si venimos de búsqueda rápida o del formulario
// ----------------------------------------------------
$idTipoAnuncio = null;
$idTVivienda   = null;
$ciudad        = '';
$idPais        = null;
$precioMax     = null;
$mesFiltro     = null;
$anyoFiltro    = null;

if (isset($_GET['ciudad_rapida']) && trim($_GET['ciudad_rapida']) !== '') {
    // ====================================================
    // MODO A: BÚSQUEDA RÁPIDA (texto libre)
    // ====================================================
    $texto = trim($_GET['ciudad_rapida']);
    $criterioCiudadTexto = $texto; // mostramos lo que escribió el usuario

    // Normalizamos: todo en minúsculas
    $textoMin = mb_strtolower($texto, 'UTF-8');

    // Eliminamos artículos y preposiciones sencillas
    $stopwords = ['una', 'un', 'en', 'de', 'del', 'el', 'la', 'al', 'por', 'para'];
    $tokens = preg_split('/\s+/', $textoMin);
    $tokensFiltrados = [];
    foreach ($tokens as $t) {
        if ($t === '') continue;
        if (in_array($t, $stopwords, true)) continue;
        $tokensFiltrados[] = $t;
    }

    $ciudadTokens = [];

    foreach ($tokensFiltrados as $t) {
        if ($idTipoAnuncio === null && isset($tiposAnuncioPorNombre[$t])) {
            $idTipoAnuncio      = $tiposAnuncioPorNombre[$t];
            $criterioTipoTexto  = ucfirst($t);
        } elseif ($idTVivienda === null && isset($tiposViviendaPorNombre[$t])) {
            $idTVivienda            = $tiposViviendaPorNombre[$t];
            $criterioViviendaTexto  = ucfirst($t);
        } else {
            // Si no es ni tipo de anuncio ni tipo de vivienda, lo interpretamos como ciudad
            $ciudadTokens[] = $t;
        }
    }

    if (!empty($ciudadTokens)) {
        $ciudad = implode(' ', $ciudadTokens);
        $criterioCiudadTexto = $ciudad;
    }

} else {
    // ====================================================
    // MODO B: FORMULARIO COMPLETO (f.busqueda.php)
    // ====================================================
    // Tipo de anuncio (IdTAnuncio)
    if (!empty($_GET['tipo'])) {
        $idTipoAnuncio = (int) $_GET['tipo'];
        $res = $mysqli->query("SELECT NomTAnuncio FROM TiposAnuncios WHERE IdTAnuncio = " . $idTipoAnuncio);
        if ($res && $row = $res->fetch_assoc()) {
            $criterioTipoTexto = $row['NomTAnuncio'];
        }
        if ($res) $res->free();
    }

    // Tipo de vivienda (IdTVivienda)
    if (!empty($_GET['vivienda'])) {
        $idTVivienda = (int) $_GET['vivienda'];
        $res = $mysqli->query("SELECT NomTVivienda FROM TiposViviendas WHERE IdTVivienda = " . $idTVivienda);
        if ($res && $row = $res->fetch_assoc()) {
            $criterioViviendaTexto = $row['NomTVivienda'];
        }
        if ($res) $res->free();
    }

    // Ciudad (texto)
    if (!empty($_GET['ciudad'])) {
        $ciudad = trim($_GET['ciudad']);
        $criterioCiudadTexto = $ciudad;
    }

    // País (IdPais)
    if (!empty($_GET['pais'])) {
        $idPais = (int) $_GET['pais'];
        $res = $mysqli->query("SELECT NomPais FROM Paises WHERE IdPais = " . $idPais);
        if ($res && $row = $res->fetch_assoc()) {
            $criterioPaisTexto = $row['NomPais'];
        }
        if ($res) $res->free();
    }

    // Precio máximo (tal como viene del campo numérico)
    if (isset($_GET['precio']) && $_GET['precio'] !== '') {
        $precioMax = (float) $_GET['precio'];
        if ($precioMax > 0) {
            // Formato 300.000,00 €
            $criterioPrecioTexto = number_format($precioMax, 2, ',', '.') . ' €';
        }
    }

    // Fecha mm/aaaa
    if (!empty($_GET['fecha'])) {
        $fechaTexto = trim($_GET['fecha']);
        $criterioFechaTexto = $fechaTexto;
        if (preg_match('#^(0[1-9]|1[0-2])\/([0-9]{4})$#', $fechaTexto, $m)) {
            $mesFiltro  = (int)$m[1];
            $anyoFiltro = (int)$m[2];
        }
    }
}

// ----------------------------------------------------
// 3. Construir WHERE de la consulta en función de lo anterior
// ----------------------------------------------------
if ($idTipoAnuncio !== null) {
    $where[] = "a.TAnuncio = " . (int)$idTipoAnuncio;
}
if ($idTVivienda !== null) {
    $where[] = "a.TVivienda = " . (int)$idTVivienda;
}
if ($ciudad !== '') {
    $ciudadEsc = $mysqli->real_escape_string($ciudad);
    // LIKE y collation ya case-insensitive
    $where[] = "a.Ciudad LIKE '%" . $ciudadEsc . "%'";
}
if ($idPais !== null) {
    $where[] = "a.Pais = " . (int)$idPais;
}
if ($precioMax !== null) {
    $where[] = "a.Precio <= " . $precioMax;
}
if ($mesFiltro !== null && $anyoFiltro !== null) {
    $where[] = "MONTH(a.FRegistro) = " . $mesFiltro . " AND YEAR(a.FRegistro) = " . $anyoFiltro;
}

// Montar la sentencia final
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
        LEFT JOIN Paises p ON a.Pais = p.IdPais";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY a.FRegistro DESC";

$resultadoBusqueda = $mysqli->query($sql);
?>

<section>
  <h3>Criterios de búsqueda introducidos</h3>
  <dl>
    <dt>Tipo de anuncio:</dt>
    <dd><?php echo htmlspecialchars($criterioTipoTexto, ENT_QUOTES, 'UTF-8'); ?></dd>

    <dt>Tipo de vivienda:</dt>
    <dd><?php echo htmlspecialchars($criterioViviendaTexto, ENT_QUOTES, 'UTF-8'); ?></dd>

    <dt>Ciudad:</dt>
    <dd><?php echo htmlspecialchars($criterioCiudadTexto, ENT_QUOTES, 'UTF-8'); ?></dd>

    <dt>País:</dt>
    <dd><?php echo htmlspecialchars($criterioPaisTexto, ENT_QUOTES, 'UTF-8'); ?></dd>

    <dt>Precio máximo:</dt>
    <dd><?php echo htmlspecialchars($criterioPrecioTexto, ENT_QUOTES, 'UTF-8'); ?></dd>

    <dt>Fecha:</dt>
    <dd><?php echo htmlspecialchars($criterioFechaTexto, ENT_QUOTES, 'UTF-8'); ?></dd>
  </dl>
</section>

<section>
  <h3>Resultados de búsqueda</h3>

  <?php
  if ($resultadoBusqueda && $resultadoBusqueda->num_rows > 0) {
      while ($fila = $resultadoBusqueda->fetch_assoc()) {
          $id        = (int)$fila['IdAnuncio'];
          $titulo    = htmlspecialchars($fila['Titulo'] ?? '', ENT_QUOTES, 'UTF-8');
          $foto      = htmlspecialchars($fila['FPrincipal'] ?? '', ENT_QUOTES, 'UTF-8');
          $alt       = htmlspecialchars($fila['Alternativo'] ?? '', ENT_QUOTES, 'UTF-8');
          $fechaRaw  = $fila['FRegistro'];
          $fecha     = $fechaRaw ? date('d/m/Y', strtotime($fechaRaw)) : '';
          $ciudadRes = htmlspecialchars($fila['Ciudad'] ?? '', ENT_QUOTES, 'UTF-8');
          $paisRes   = htmlspecialchars($fila['Pais'] ?? '', ENT_QUOTES, 'UTF-8');
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
              Ciudad: <?php echo $ciudadRes; ?><br>
              País: <?php echo $paisRes; ?><br>
              Precio: <?php echo $precioFmt; ?> €
            </p>
            <p><a class="enlaces" href="detalle_anuncio.php?id=<?php echo $id; ?>">Ver detalle</a></p>
          </article>

          <?php
      }
      $resultadoBusqueda->free();
  } else {
      echo "<p>No se han encontrado anuncios que coincidan con los criterios de búsqueda.</p>";
  }

  $mysqli->close();
  ?>
</section>

</main>

<?php
require_once("panel_ultimos_anuncios.php");
require_once("footer.inc");
?>
