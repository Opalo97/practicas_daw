<?php
$title = "Registro de nuevo usuario";
require_once("cabecera.inc");
require_once("inicio2.inc");
?>

<section>
  <article>

    <form id="formRegistro" class="form-registro" action="index.php" method="post" enctype="multipart/form-data" novalidate>
      <fieldset>
        <legend>Datos de acceso</legend>

        <label for="usuario">Nombre de usuario:</label>
        <input type="text" id="usuario" name="usuario" />

        <label for="clave">Contraseña:</label>
        <input type="password" id="clave" name="clave" />

        <label for="clave2">Repetir contraseña:</label>
        <input type="password" id="clave2" name="clave2" />
      </fieldset>

      <fieldset>
        <legend>Datos personales</legend>

        <label for="email">Correo electrónico:</label>
        <input type="text" id="email" name="email" />

        <p>Sexo:</p>
        <label><input type="radio" id="hombre" name="sexo" value="hombre" /> Hombre</label>
        <label><input type="radio" id="mujer" name="sexo" value="mujer" /> Mujer</label>

        <label for="fecha">Fecha de nacimiento:</label>
        <input type="text" id="fecha" name="fecha" placeholder="dd/mm/aaaa" />

        <label for="ciudad">Ciudad:</label>
        <input type="text" id="ciudad" name="ciudad" />

        <label for="pais">País:</label>
        <select id="pais" name="pais">
          <option value="">Seleccione un país</option>
          <option value="espana">España</option>
          <option value="francia">Francia</option>
          <option value="italia">Italia</option>
          <option value="alemania">Alemania</option>
          <option value="portugal">Portugal</option>
          <option value="mexico">México</option>
          <option value="argentina">Argentina</option>
          <option value="chile">Chile</option>
          <option value="colombia">Colombia</option>
          <option value="peru">Perú</option>
        </select>

        <div class="upload-container">
          <label for="foto" class="btn-subir">Subir foto de perfil</label>
          <input type="file" id="foto" name="foto" class="input-foto" />
          <span class="file-name">foto_piso2.jpg</span>
        </div>
      </fieldset>

      <!-- Contenedor para errores -->
      <div id="errores" class="errores"></div>

      <p><input type="submit" class="button" value="Registrarse" /></p>
    </form>
  </article>
</section>

</main>

<?php
require_once("footer.inc");
?>

<!-- JS -->
<script src="registro.js"></script>
