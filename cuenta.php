<?php
$title = "Mi cuenta";
require_once("cabecera.inc");
require_once("inicio.inc");
?>

<section>
  <ul class="lista">
    <li><a href="perfil_usuario.php">Perfil del usuario</a></li>
    <li><a href="index_no.php">Darme de baja</a></li>
    <li><a href="mis_anuncios.php">Visualizar mis anuncios</a></li>
    <li><a href="anyadir_foto.php">Añadir foto a anuncio</a></li>
    <li><a href="crear_anuncio.php">Crear un anuncio nuevo</a></li>
    <li><a href="mis_mensajes.php">Visualizar mis mensajes</a></li>
    <li><a href="solicitar_folleto.php">Solicitar folleto publicitario</a></li>
    <li><a href="configurar.php">Configurar estilo</a></li>
    <li><a href="mis_datos.php">Mis datos</a></li>
    <li><a href="logout.php">Salir</a></li>
  </ul>
</section>

</main>

<?php
require_once("panel_ultimos_anuncios.php");
require_once("footer.inc");
?>
