<?php
ob_start();
session_start();

$title = "Inicio";
require_once("cabecera.inc");
require_once("inicio.inc");
require_once("bd.php"); // conexión con la BD

// ---------- CONTROL DE ACCESO / RESTAURACIÓN DESDE COOKIES ----------

$restaurado_por_cookie = false;

if (!isset($_SESSION['usuario'])) {
    if (isset($_COOKIE['usuario']) && isset($_COOKIE['password'])) {
        $usuarios_validos = include("usuarios.php");
        if (is_array($usuarios_validos)) {
            foreach ($usuarios_validos as $u) {
                if ($u['usuario'] === $_COOKIE['usuario'] && $u['password'] === $_COOKIE['password']) {
                    // Restauramos sesión automáticamente
                    $_SESSION['usuario'] = $u['usuario'];
                    $restaurado_por_cookie = true;
                    break;
                }
            }
        }
    }
}

// Si no hay sesión válida, redirigir a la parte pública
if (!isset($_SESSION['usuario'])) {
    header("Location: index_no.php");
    exit;
}

// ---------- MENSAJE DE SALUDO SEGÚN HORA ----------
$usuario = htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8');
$hora = intval(date("H"));
if ($hora >= 6 && $hora < 12) {
    $saludo = "Buenos días";
} elseif ($hora >= 12 && $hora < 16) {
    $saludo = "Hola";
} elseif ($hora >= 16 && $hora < 20) {
    $saludo = "Buenas tardes";
} else {
    $saludo = "Buenas noches";
}

// ---------- MOSTRAR ÚLTIMA VISITA (solo si restaurado por cookie) ----------
if ($restaurado_por_cookie) {
    if (isset($_COOKIE['ultima_visita'])) {
        $ultima = htmlspecialchars($_COOKIE['ultima_visita'], ENT_QUOTES, 'UTF-8');
        echo "<p style='text-align:right; margin:10px; font-style:italic;'>
                $saludo, <strong>$usuario</strong>. Tu última visita fue el <strong>$ultima</strong>.
              </p>";
    } else {
        echo "<p style='text-align:right; margin:10px; font-style:italic;'>
                $saludo, <strong>$usuario</strong>. Esta es tu primera visita recordada.
              </p>";
    }

    // Actualizar cookie para la próxima visita
    setcookie('ultima_visita', date('d/m/Y H:i:s'), time() + (90 * 24 * 60 * 60), '/', '', false, true);
} else {
  // Mostrar saludo siempre
  echo "<h3 style='text-align:right; padding:10px;'>$saludo, <strong>$usuario</strong></h3>";

  // Mostrar última visita si la cookie existe (aunque no haya sido restaurado)
  if (isset($_COOKIE['ultima_visita'])) {
      $ultima = htmlspecialchars($_COOKIE['ultima_visita'], ENT_QUOTES, 'UTF-8');
      echo "<p style='text-align:right; margin:10px; font-style:italic;'>
              Tu última visita fue el <strong>$ultima</strong>.
            </p>";
  }
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


<section>
  <h3>Últimos anuncios publicados</h3>

  <?php
  // Obtenemos los 5 últimos anuncios usando la FECHA DE REGISTRO
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
                  Fecha registro: <?php echo $fecha; ?><br>
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
