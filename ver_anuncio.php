<?php
ob_start();
session_start();

$title = "Ver anuncio";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php");

// ------------------------------------------------------
// 1. Control de acceso: solo usuarios registrados
// ------------------------------------------------------
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

// ------------------------------------------------------
// 2. Obtener id del anuncio
// ------------------------------------------------------
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<p>Anuncio no válido.</p>";
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
// 4. Cargar fotos secundarias
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
// 5. Preparar variables para mostrar
// ------------------------------------------------------
$titulo        = htmlspecialchars($anuncio['Titulo'] ?? '', ENT_QUOTES, 'UTF-8');
$tipoAnuncio   = htmlspecialchars($anuncio['NomTAnuncio'] ?? '', ENT_QUOTES, 'UTF-8');
$tipoViviendaH = htmlspecialchars($anuncio['NomTVivienda'] ?? '', ENT_QUOTES, 'UTF-8');
$texto         = htmlspecialchars($anuncio['Texto'] ?? '', ENT_QUOTES, 'UTF-8');

$fechaRaw      = $anuncio['FRegistro'];
$fechaPub      = $fechaRaw ? date('d/m/Y', strtotime($fechaRaw)) : '';

$ciudadH       = htmlspecialchars($anuncio['Ciudad'] ?? '', ENT_QUOTES, 'UTF-8');
$paisH         = htmlspecialchars($anuncio['NomPais'] ?? '', ENT_QUOTES, 'UTF-8');

$precio        = $anuncio['Precio'];
$precioH       = is_null($precio) ? '' : number_format($precio, 2, ',', '.') . ' €';
$usuarioPub    = htmlspecialchars($anuncio['NomUsuario'] ?? '', ENT_QUOTES, 'UTF-8');

$superficie    = $anuncio['Superficie'];
$habitaciones  = $anuncio['NHabitaciones'];
$banyos        = $anuncio['NBanyos'];
$planta        = $anuncio['Planta'];
$anyo          = $anuncio['Anyos'] ?? $anuncio['Anyo'];

$fotoPrincipalH = htmlspecialchars($anuncio['FPrincipal'] ?? '', ENT_QUOTES, 'UTF-8');
$altPrincipal   = htmlspecialchars($anuncio['Alternativo'] ?? 'Foto principal del anuncio', ENT_QUOTES, 'UTF-8');

$enlaceMensaje = true;
$enlaceFotos   = true;

// ------------------------------------------------------
// 6. Incluir plantilla de mostrar anuncio
// ------------------------------------------------------
require("mostrar_anuncio.inc.php");

$mysqli->close();
require_once("footer.inc");
?>
