<?php
$cookie_name = "ultimos_anuncios";

if (isset($_COOKIE[$cookie_name])) {
    $ultimos = json_decode($_COOKIE[$cookie_name], true);

    if (!empty($ultimos)) {
        echo "<section class='panel-ultimos'>";
        echo "<h3>Últimos anuncios visitados</h3>";
        echo "<div class='panel-grid'>";

        foreach ($ultimos as $anuncio) {
            $id = htmlspecialchars($anuncio['id']);
            $foto = htmlspecialchars($anuncio['foto']);
            $tipo = htmlspecialchars($anuncio['tipo_vivienda']);
            $ciudad = htmlspecialchars($anuncio['ciudad']);
            $pais = htmlspecialchars($anuncio['pais']);
            $precio = htmlspecialchars($anuncio['precio']);
            $pagina = $anuncio['pagina'] ?? 'detalle'; 

            $href = ($pagina === 'ver') 
                ? "ver_anuncio.php?id={$id}" 
                : "detalle_anuncio.php?id={$id}";

            echo "<article class='card-anuncio'>";
            echo "  <div class='card-img'>";
            echo "    <img src='{$foto}' alt='Foto anuncio'>";
            echo "  </div>";
            echo "  <div class='card-info'>";
            echo "    <h4>{$tipo}</h4>";
            echo "    <p><span>Ciudad:</span> {$ciudad}</p>";
            echo "    <p><span>País:</span> {$pais}</p>";
            echo "    <p><span>Precio:</span> {$precio}</p>";
            echo "    <a class='btn-ver' href='{$href}'>Ver detalle</a>";
            echo "  </div>";
            echo "</article>";
        }

        echo "</div>";
        echo "</section>";
    }
}
?>
