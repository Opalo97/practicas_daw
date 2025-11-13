<?php
ob_start();
session_start();

$title = "Perfil de usuario";
require_once("cabecera.inc");

// A partir de aquí, que inicio.inc NO muestre "Perfil de usuario"
$title = "";

require_once("inicio.inc");
require_once("bd.php");


// ------------------------------------------------------
// 1. Comprobar que hay usuario en sesión
// ------------------------------------------------------
if (!isset($_SESSION['usuario'])) {
    header("Location: aviso.php");
    exit;
}

$nombreUsuarioSesion = $_SESSION['usuario'];

// ------------------------------------------------------
// 2. Cargar datos del usuario desde la BD
// ------------------------------------------------------
$mysqli = obtenerConexion();

$nombreEsc = $mysqli->real_escape_string($nombreUsuarioSesion);

$sqlUsuario = "SELECT IdUsuario, NomUsuario, FRegistro, Foto
               FROM Usuarios
               WHERE NomUsuario = '$nombreEsc'
               LIMIT 1";

$resUsu = $mysqli->query($sqlUsuario);

if (!$resUsu || $resUsu->num_rows === 0) {
    echo "<p>No se ha encontrado el perfil del usuario actual.</p>";
    if ($resUsu) $resUsu->free();
    $mysqli->close();
    require_once("footer.inc");
    exit;
}

$usuario = $resUsu->fetch_assoc();
$resUsu->free();

$idUsuario    = (int)$usuario['IdUsuario'];
$nomUsuario   = htmlspecialchars($usuario['NomUsuario'] ?? '', ENT_QUOTES, 'UTF-8');
$fotoPerfil   = $usuario['Foto'] ?? null;
$fotoPerfilH  = $fotoPerfil ? htmlspecialchars($fotoPerfil, ENT_QUOTES, 'UTF-8') : '';
$fechaRegRaw  = $usuario['FRegistro'];
$fechaAlta    = $fechaRegRaw ? date('d/m/Y', strtotime($fechaRegRaw)) : 'Desconocida';

// ------------------------------------------------------
// 3. Cargar anuncios de este usuario
// ------------------------------------------------------
$anunciosUsuario = [];

$sqlAnuncios = "SELECT 
                    a.IdAnuncio,
                    a.Titulo,
                    a.Ciudad,
                    p.NomPais AS Pais,
                    a.Precio,
                    a.FRegistro
                FROM Anuncios a
                LEFT JOIN Paises p ON a.Pais = p.IdPais
                WHERE a.Usuario = $idUsuario
                ORDER BY a.FRegistro DESC";

if ($resAn = $mysqli->query($sqlAnuncios)) {
    while ($fila = $resAn->fetch_assoc()) {
        $anunciosUsuario[] = $fila;
    }
    $resAn->free();
}

?>

<section>
  <article>
    <h2>Perfil de <?php echo $nomUsuario; ?></h2>

    <fieldset>
      <legend>Datos del usuario</legend>
      <div class="perfil-usuario">
        <?php if (!empty($fotoPerfilH)): ?>
          <div class="foto-perfil">
            <img src="<?php echo $fotoPerfilH; ?>" alt="Foto de perfil de <?php echo $nomUsuario; ?>">
          </div>
        <?php else: ?>
          <p>Este usuario no tiene foto de perfil.</p>
        <?php endif; ?>

        <dl>
          <dt>Nombre de usuario:</dt>
          <dd><?php echo $nomUsuario; ?></dd>

          <dt>Fecha de incorporación:</dt>
          <dd><?php echo htmlspecialchars($fechaAlta, ENT_QUOTES, 'UTF-8'); ?></dd>
        </dl>
      </div>
    </fieldset>
  </article>
</section>

<section>
  <article>
    <fieldset>
      <legend>Anuncios del usuario</legend>

      <?php if (!empty($anunciosUsuario)): ?>
        <ul class="lista-anuncios-usuario">
          <?php foreach ($anunciosUsuario as $an): 
            $idAnuncio = (int)$an['IdAnuncio'];
            $titulo    = htmlspecialchars($an['Titulo'] ?? '', ENT_QUOTES, 'UTF-8');
            $ciudad    = htmlspecialchars($an['Ciudad'] ?? '', ENT_QUOTES, 'UTF-8');
            $pais      = htmlspecialchars($an['Pais'] ?? '', ENT_QUOTES, 'UTF-8');
            $precio    = $an['Precio'];
            $precioFmt = is_null($precio) ? '' : number_format($precio, 2, ',', '.') . ' €';
            $fRegRaw   = $an['FRegistro'];
            $fechaReg  = $fRegRaw ? date('d/m/Y', strtotime($fRegRaw)) : '';
          ?>
            <li>
              <h3><?php echo $titulo; ?></h3>
              <p>
                Fecha: <?php echo htmlspecialchars($fechaReg, ENT_QUOTES, 'UTF-8'); ?><br>
                Ciudad: <?php echo $ciudad; ?><br>
                País: <?php echo $pais; ?><br>
                Precio: <?php echo htmlspecialchars($precioFmt, ENT_QUOTES, 'UTF-8'); ?>
              </p>
              <p>
                <a class="enlaces" href="detalle_anuncio.php?id=<?php echo $idAnuncio; ?>">
                  Ver detalle
                </a>
              </p>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>Este usuario no tiene anuncios publicados.</p>
      <?php endif; ?>

    </fieldset>
  </article>
</section>

</main>

<?php
$mysqli->close();
require_once("panel_ultimos_anuncios.php");
require_once("footer.inc");
?>
