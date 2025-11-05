<?php
ob_start();
session_start();

$title = "Inicio";
require_once("cabecera.inc");
require_once("inicio.inc");

 
// CONTROL DE ACCESO
 
if (!isset($_SESSION['usuario'])) {
    // Si no hay sesión, intentar restaurarla con cookies válidas
    if (isset($_COOKIE['usuario']) && isset($_COOKIE['password'])) {
        $usuarios_validos = include("usuarios.php");
        foreach ($usuarios_validos as $u) {
            if ($u['usuario'] === $_COOKIE['usuario'] && $u['password'] === $_COOKIE['password']) {
                $_SESSION['usuario'] = $u['usuario'];
                break;
            }
        }
    }
}

// Si sigue sin haber sesión, redirigir parte publica
if (!isset($_SESSION['usuario'])) {
    header("Location: index_no.php");
    exit;
}

 
// saludo al usuario que toca
 
$usuario = htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8');
$hora = date("H");
if ($hora >= 6 && $hora < 12) {
    $saludo = "Buenos días";
} elseif ($hora >= 12 && $hora < 16) {
    $saludo = "Hola";
} elseif ($hora >= 16 && $hora < 20) {
    $saludo = "Buenas tardes";
} else {
    $saludo = "Buenas noches";
}

// Mostrar saludo
echo "<h3 style='text-align:right; padding:10px;'>{$saludo}, <strong>{$usuario}</strong></h3>";
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
    <label>Ciudad:
      <input type="text" name="ciudad_rapida" placeholder="Ciudad">
    </label>
    <button type="submit" class="button">Buscar</button>
  </form>
</section>

<section>
  <h3>Últimos anuncios publicados</h3>

  <article>
    <div class="imagen_principal">
      <img src="img/foto_piso.jpg" alt="Piso en Madrid centro">
    </div>
    <h4>Piso en Madrid centro</h4>
    <p>Vivienda luminosa y moderna con 3 habitaciones, 2 baños y balcón con vistas al centro.</p>
    <p><a class="enlaces" href="detalle_anuncio.php?id=1">Ver detalle</a></p>
  </article>

  <article>
    <div class="imagen_principal">
      <img src="img/foto_piso1.jpg" alt="Apartamento en París">
    </div>
    <h4>Apartamento en París</h4>
    <p>Acogedor apartamento en el corazón de París, ideal para parejas o estancias cortas.</p>
    <p><a class="enlaces" href="detalle_anuncio.php?id=2">Ver detalle</a></p>
  </article>

  <article>
    <div class="imagen_principal">
      <img src="img/foto_piso2.jpg" alt="Casa en Roma">
    </div>
    <h4>Casa en Roma</h4>
    <p>Amplia casa familiar con jardín privado y excelente comunicación con el centro histórico.</p>
    <p><a class="enlaces" href="detalle_anuncio.php?id=1">Ver detalle</a></p>
  </article>

  <article>
    <div class="imagen_principal">
      <img src="img/foto_piso5.jpeg" alt="Ático en Lisboa">
    </div>
    <h4>Ático en Lisboa</h4>
    <p>Elegante ático con vistas al río Tajo, terraza privada y excelente iluminación natural.</p>
    <p><a class="enlaces" href="detalle_anuncio.php?id=2">Ver detalle</a></p>
  </article>

  <article>
    <div class="imagen_principal">
      <img src="img/foto_piso6.jpeg" alt="Chalet en Berlín">
    </div>
    <h4>Chalet en Berlín</h4>
    <p>Acogedor chalet rodeado de zonas verdes, ideal para familias y con fácil acceso al centro urbano.</p>
    <p><a class="enlaces" href="detalle_anuncio.php?id=1">Ver detalle</a></p>
  </article>
</section>

</main>

<?php
require_once("footer.inc");
?>
