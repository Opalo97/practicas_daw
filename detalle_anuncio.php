<?php
ob_start();
session_start();

$title = "Detalle del anuncio";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");

// ------------------------------------------------------
// 1. Control de acceso
// ------------------------------------------------------

// Si viene directamente desde index_no.php → parte pública, no permitido
if (isset($_SERVER['HTTP_REFERER'])) {
    $pagina_anterior = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
    if ($pagina_anterior === 'index_no.php') {
        header("Location: aviso.php");
        exit;
    }
}

// Solo usuarios registrados
if (!isset($_SESSION['usuario'])) {
    // Intentar restaurar sesión con cookies válidas
    if (isset($_COOKIE['usuario']) && isset($_COOKIE['password'])) {
        $usuarios_validos = include("usuarios.php");
        if (is_array($usuarios_validos)) {
            foreach ($usuarios_validos as $u) {
                if ($u['usuario'] === $_COOKIE['usuario'] && $u['password'] === $_COOKIE['password']) {
                    $_SESSION['usuario'] = $u['usuario'];
                    break;
                }
            }
        }
    }
}

// Si sigue sin haber sesión → redirigir a aviso
if (!isset($_SESSION['usuario'])) {
    header("Location: aviso.php");
    exit;
}

// ------------------------------------------------------
// 2. Obtener el id del anuncio
// ------------------------------------------------------
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<p>Identificador de anuncio no válido.</p>";
    require_once("footer.inc");
    exit;
}

// ------------------------------------------------------
// 3. Cargar datos del anuncio desde la BD
// ------------------------------------------------------
$mysqli = obtenerConexion();

$sql = "SELECT 
            a.IdAnuncio,
            a.TAnuncio,
            ta.NomTAnuncio,
            a.TVivienda,
            tv.NomTVivienda,
            a.FPrincipal,
            a.Alternativo,
            a.Titulo,
            a.Texto,
            a.FRegistro,
            a.Ciudad,
            p.NomPais AS NomPais,
            a.Precio,
            a.Superficie,
            a.NHabitaciones,
            a.NBanyos,
            a.Planta,
            a.Anyo,
            a.Usuario,
            u.NomUsuario
        FROM Anuncios a
        LEFT JOIN TiposAnuncios  ta ON a.TAnuncio  = ta.IdTAnuncio
        LEFT JOIN TiposViviendas tv ON a.TVivienda = tv.IdTVivienda
        LEFT JOIN Paises         p  ON a.Pais      = p.IdPais
        LEFT JOIN Usuarios       u  ON a.Usuario   = u.IdUsuario
        WHERE a.IdAnuncio = $id";

$result = $mysqli->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "<p>No se ha encontrado el anuncio solicitado.</p>";
    if ($result) $result->free();
    $mysqli->close();
    require_once("footer.inc");
    exit;
}

$anuncio = $result->fetch_assoc();
$result->free();

// ------------------------------------------------------
// 4. Cargar fotos secundarias (miniaturas) desde la tabla Fotos
// ------------------------------------------------------
$fotos_secundarias = [];
$sqlFotos = "SELECT Foto, Alternativo 
             FROM Fotos 
             WHERE Anuncio = $id
             ORDER BY IdFoto";

if ($resFotos = $mysqli->query($sqlFotos)) {
    while ($filaFoto = $resFotos->fetch_assoc()) {
        $fotos_secundarias[] = $filaFoto;
    }
    $resFotos->free();
}


// ------------------------------------------------------
// 5. Guardar el anuncio en la cookie de últimos anuncios visitados
// ------------------------------------------------------
$max_anuncios = 4; // guarda los 4 últimos
$cookie_name  = "ultimos_anuncios";

$fotoPrincipal = $anuncio['FPrincipal'] ?? '';
$tipoVivienda  = $anuncio['NomTVivienda'] ?? '';
$ciudad        = $anuncio['Ciudad'] ?? '';
$pais          = $anuncio['NomPais'] ?? '';
$precio        = $anuncio['Precio'];

$precioFmt = is_null($precio) ? '' : number_format($precio, 2, ',', '.') . ' €';

$anuncio_actual = [
    'id'            => $id,
    'foto'          => $fotoPrincipal,
    'tipo_vivienda' => $tipoVivienda,
    'ciudad'        => $ciudad,
    'pais'          => $pais,
    'precio'        => $precioFmt
];

// Leer cookie existente (si la hay)
$ultimos = isset($_COOKIE[$cookie_name]) ? json_decode($_COOKIE[$cookie_name], true) : [];
if (!is_array($ultimos)) {
    $ultimos = [];
}

// Eliminar si ya estaba para evitar duplicados
$ultimos = array_filter($ultimos, function ($a) use ($id) {
    return isset($a['id']) && (int)$a['id'] !== $id;
});

// Insertar el nuevo al principio
array_unshift($ultimos, $anuncio_actual);

// Limitar a 4 elementos
$ultimos = array_slice($ultimos, 0, $max_anuncios);

// Guardar cookie (1 semana)
setcookie($cookie_name, json_encode($ultimos), time() + 7 * 24 * 60 * 60, '/', '', false, true);

// ------------------------------------------------------
// 6. Preparar datos formateados para la vista
// ------------------------------------------------------
$titulo        = htmlspecialchars($anuncio['Titulo']        ?? '', ENT_QUOTES, 'UTF-8');
$tipoAnuncio   = htmlspecialchars($anuncio['NomTAnuncio']   ?? '', ENT_QUOTES, 'UTF-8');
$tipoViviendaH = htmlspecialchars($tipoVivienda             ?? '', ENT_QUOTES, 'UTF-8');
$texto         = htmlspecialchars($anuncio['Texto']         ?? '', ENT_QUOTES, 'UTF-8');

$fechaRaw      = $anuncio['FRegistro'];
$fechaPub      = $fechaRaw ? date('d/m/Y', strtotime($fechaRaw)) : '';

$ciudadH       = htmlspecialchars($ciudad ?? '', ENT_QUOTES, 'UTF-8');
$paisH         = htmlspecialchars($pais   ?? '', ENT_QUOTES, 'UTF-8');

$precioH       = $precioFmt;

$usuarioPub    = htmlspecialchars($anuncio['NomUsuario'] ?? '', ENT_QUOTES, 'UTF-8');

$superficie    = $anuncio['Superficie'];
$habitaciones  = $anuncio['NHabitaciones'];
$banyos        = $anuncio['NBanyos'];
$planta        = $anuncio['Planta'];
$anyo          = $anuncio['Anyo'];

$fotoPrincipalH = htmlspecialchars($fotoPrincipal ?? '', ENT_QUOTES, 'UTF-8');
$altPrincipal   = htmlspecialchars($anuncio['Alternativo'] ?? 'Foto principal del anuncio', ENT_QUOTES, 'UTF-8');
?>

<article>
  <h2><?php echo $titulo; ?></h2>

  <div class="imagenes">
    <div class="imagen_principal">
      <?php if (!empty($fotoPrincipalH)) { ?>
        <img src="<?php echo $fotoPrincipalH; ?>" alt="<?php echo $altPrincipal; ?>">
      <?php } else { ?>
        <img src="img/foto_piso.jpg" alt="Foto principal no disponible">
      <?php } ?>
    </div>

    <div class="imagen_secundaria">
      <?php if (!empty($fotos_secundarias)): ?>
        <?php foreach ($fotos_secundarias as $f): 
          $fichero = htmlspecialchars($f['Foto'] ?? '', ENT_QUOTES, 'UTF-8');
          $altSec  = htmlspecialchars($f['Alternativo'] ?? 'Foto adicional', ENT_QUOTES, 'UTF-8');
        ?>
          <img src="<?php echo $fichero; ?>" alt="<?php echo $altSec; ?>">
        <?php endforeach; ?>

      <?php else: ?>
        <p>No hay fotos adicionales para este anuncio.</p>
      <?php endif; ?>
    </div>
  </div>

  <fieldset>
    <legend>Descripción</legend>
    <dl>
      <dt>Tipo de anuncio</dt>
      <dd><?php echo $tipoAnuncio; ?></dd>

      <dt>Tipo de vivienda</dt>
      <dd><?php echo $tipoViviendaH; ?></dd>

      <dt>Texto del anuncio</dt>
      <dd><?php echo nl2br($texto); ?></dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Información del anuncio</legend>
    <dl>
      <dt>Fecha de publicación</dt>
      <dd><?php echo htmlspecialchars($fechaPub, ENT_QUOTES, 'UTF-8'); ?></dd>

      <dt>Ciudad</dt>
      <dd><?php echo $ciudadH; ?></dd>

      <dt>País</dt>
      <dd><?php echo $paisH; ?></dd>

      <dt>Precio</dt>
      <dd><?php echo htmlspecialchars($precioH, ENT_QUOTES, 'UTF-8'); ?></dd>

      <dt>Publicado por</dt>
      <dd><?php echo $usuarioPub; ?></dd>
    </dl>
  </fieldset>

  <fieldset>
    <legend>Características</legend>
    <dl>
      <?php if (!is_null($superficie)): ?>
        <dt>Superficie</dt>
        <dd><?php echo htmlspecialchars(number_format($superficie, 2, ',', '.') . ' m²', ENT_QUOTES, 'UTF-8'); ?></dd>
      <?php endif; ?>

      <?php if (!is_null($habitaciones)): ?>
        <dt>Habitaciones</dt>
        <dd><?php echo (int)$habitaciones; ?></dd>
      <?php endif; ?>

      <?php if (!is_null($banyos)): ?>
        <dt>Baños</dt>
        <dd><?php echo (int)$banyos; ?></dd>
      <?php endif; ?>

      <?php if (!is_null($planta)): ?>
        <dt>Planta</dt>
        <dd><?php echo htmlspecialchars($planta, ENT_QUOTES, 'UTF-8'); ?></dd>
      <?php endif; ?>

      <?php if (!is_null($anyo)): ?>
        <dt>Año de construcción</dt>
        <dd><?php echo (int)$anyo; ?></dd>
      <?php endif; ?>
    </dl>
  </fieldset>

  <fieldset>
    <legend>¿Interesado?</legend>
    <p>
      <a class="enlaces" href="enviar_mensaje.php?id=<?php echo $id; ?>">
        Enviar mensaje al anunciante
      </a>
    </p>
  </fieldset>
</article>

</main>

<?php
$mysqli->close();
require_once("footer.inc");
?>
