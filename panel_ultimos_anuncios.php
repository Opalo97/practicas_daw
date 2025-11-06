<?php
$cookie_name = "ultimos_anuncios";

if (isset($_COOKIE[$cookie_name])) {
    $ultimos = json_decode($_COOKIE[$cookie_name], true);

    if (!empty($ultimos)) {
        echo "<section>";
        echo "<h3>Últimos anuncios visitados</h3>";
        echo "<div style='display:flex; flex-wrap:wrap; gap:20px;'>";

        foreach ($ultimos as $anuncio) {
            $id = htmlspecialchars($anuncio['id']);
            $foto = htmlspecialchars($anuncio['foto']);
            $tipo = htmlspecialchars($anuncio['tipo_vivienda']);
            $ciudad = htmlspecialchars($anuncio['ciudad']);
            $pais = htmlspecialchars($anuncio['pais']);
            $precio = htmlspecialchars($anuncio['precio']);
            $pagina = $anuncio['pagina'] ?? 'detalle'; 

            // Determinar el enlace correcto
            $href = ($pagina === 'ver') 
                ? "ver_anuncio.php?id={$id}" 
                : "detalle_anuncio.php?id={$id}";

            echo "<article style='width:220px; border:1px solid #ccc; border-radius:8px; padding:10px;'>";
            echo "  <div class='imagen_principal'>";
            echo "    <img src='{$foto}' alt='Foto anuncio' style='width:100%; height:auto; border-radius:8px;'>";
            echo "  </div>";
            echo "  <h4>{$tipo}</h4>";
            echo "  <p><strong>Ciudad:</strong> {$ciudad}</p>";
            echo "  <p><strong>País:</strong> {$pais}</p>";
            echo "  <p><strong>Precio:</strong> {$precio}</p>";
            echo "  <p><a class='enlaces' href='{$href}'>Ver detalle</a></p>";
            echo "</article>";
        }


        echo "</div>";
        echo "</section>";
    }
}
?>
