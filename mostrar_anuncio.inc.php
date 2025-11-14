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

  <?php if(isset($enlaceMensaje) && $enlaceMensaje): ?>
  <fieldset>
    <legend>¿Interesado?</legend>
    <p>
      <a class="enlaces" href="enviar_mensaje.php?id=<?php echo $id; ?>">
        Enviar mensaje al anunciante
      </a>
    </p>
  </fieldset>
  <?php endif; ?>

  <!-- NUEVO BOTÓN para ver mensajes del anuncio -->
  <fieldset>
    <legend>Mensajes del anuncio</legend>
    <p>
      <a class="enlaces" href="mensajes.php?id=<?php echo $id; ?>">
        Ver todos los mensajes de este anuncio
      </a>
    </p>
  </fieldset>

  <?php if(isset($enlaceFotos) && $enlaceFotos): ?>
  <fieldset>
    <legend>Galería de fotos</legend>
    <p>
      <a class="enlaces" href="ver_fotos.php?id=<?php echo $id; ?>">Ver todas las fotos del anuncio</a>
    </p>
  </fieldset>
  <?php endif; ?>
</article>
