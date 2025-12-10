# RESUMEN DE CAMBIOS REALIZADOS HOY
## Fecha: 10 de diciembre de 2025

---

## 1. GESTIÓN DE FOTOS DE USUARIO (Perfil)

### Archivos modificados:
- `filtros.php` - Nueva función `procesar_foto_subida()`
- `respuesta_registro.php` - Subida de foto al registrarse
- `mis_datos.php` - Interfaz para modificar/eliminar foto
- `respuesta_mis_datos.php` - Procesamiento de cambios de foto
- `perfil_usuario.php` - Mostrar foto o icono por defecto
- `img/user_default.svg` - Icono por defecto cuando no hay foto

### Funcionalidad:
- **Registro**: Al registrarse, el usuario puede subir una foto de perfil opcional
- **Visualización**: La foto se muestra en la página de respuesta del registro para verificar
- **Modificar**: En "Mis datos" se puede subir una nueva foto (reemplaza la anterior)
- **Eliminar**: Checkbox para eliminar la foto y usar el icono por defecto
- **Almacenamiento**: Todas las fotos en `img/usuarios/` con nombre único `usr_XXXXX.ext`
- **Seguridad**: Solo se aceptan jpg, png, gif validados por MIME type real

---

## 2. GESTIÓN DE FOTOS DE ANUNCIOS

### Archivos modificados:
- `filtros.php` - Nueva función `procesar_foto_anuncio()`
- `crear_anuncio.php` - Subida de foto al crear anuncio
- `anyadir_foto.php` - Simplificado (solo input file)
- `respuesta_anyadir_foto.php` - Procesamiento con nombre único
- `eliminar_anuncio.php` - Borrado físico de todos los ficheros
- `eliminar_foto.php` - Ya existía, sin cambios

### Funcionalidad:
- **Crear anuncio**: Permite subir foto principal con nombre único
- **Añadir foto**: Subida con estrategia anti-colisiones
- **Nombres únicos**: `anun_{userId}_{anuncioId}_{timestamp}_{random}.ext`
  - Evita colisiones entre diferentes usuarios
  - Evita colisiones del mismo usuario en diferentes anuncios
  - Evita colisiones de subidas repetidas al mismo anuncio
- **Almacenamiento**: Todas las fotos en `img/anuncios/`
- **Eliminar anuncio**: Borra todos los ficheros físicos asociados
- **Eliminar foto**: Borra el fichero físico individual

---

## 3. BAJA DE USUARIO COMPLETA

### Archivos modificados:
- `darse_baja.php` - Borrado físico de todas las fotos

### Funcionalidad:
- Al darse de baja, se borran del servidor:
  1. Foto de perfil del usuario
  2. Todas las fotos de todos sus anuncios
- Después se eliminan los registros de BD

---

## 4. SEGURIDAD: CONTRASEÑAS HASHEADAS

### Archivos modificados:
- `respuesta_registro.php` - Hashear con password_hash()
- `control_acceso.php` - Verificar con password_verify()
- `login.php` - Verificar cookies con password_verify()
- `respuesta_mis_datos.php` - Hashear nueva contraseña y verificar actual
- `darse_baja.php` - Verificar con password_verify()
- `actualizar_passwords.php` - Script de migración (ejecutar una vez)

### Funcionalidad:
- **Registro**: Las contraseñas se guardan hasheadas con bcrypt (60 caracteres)
- **Login**: Se verifica con password_verify() comparando texto plano con hash
- **Cambio de contraseña**: La nueva se hashea, la actual se verifica
- **Cookies**: También se verifican con password_verify()
- **En BD**: Las contraseñas aparecen como `$2y$10$abc...` (ilegibles en phpMyAdmin)
- **Migración**: Script para convertir contraseñas antiguas en texto plano a hash

---

## 5. FUNCIONES PHP UTILIZADAS

### Para directorios:
- `is_dir()` - Verificar existencia
- `mkdir($dir, 0755, true)` - Crear con permisos recursivos
- `__DIR__` - Ruta del directorio actual
- `DIRECTORY_SEPARATOR` - Separador según SO

### Para ficheros:
- `is_file()` - Verificar existencia
- `realpath()` - Obtener ruta absoluta real
- `move_uploaded_file()` - Mover desde tmp a destino
- `unlink()` - Borrar fichero
- `@unlink()` - Borrar suprimiendo warnings
- `basename()` - Obtener nombre sin ruta
- `getimagesize()` - Validar imagen

### Para subidas:
- `$_FILES['campo']` - Ficheros subidos
- `$_FILES['campo']['tmp_name']` - Ruta temporal
- `$_FILES['campo']['error']` - Código de error
- `UPLOAD_ERR_NO_FILE` - No se subió fichero
- `UPLOAD_ERR_OK` - Subida correcta

### Para validación:
- `finfo_open(FILEINFO_MIME_TYPE)` - Abrir detector MIME
- `finfo_file()` - Detectar tipo MIME real
- `finfo_close()` - Cerrar recurso

### Para nombres únicos:
- `uniqid()` - ID único basado en microsegundos
- `time()` - Timestamp Unix actual
- `random_bytes(4)` - 4 bytes aleatorios
- `bin2hex()` - Convertir a hexadecimal

### Para seguridad:
- `strpos()` - Validar que ruta está en proyecto
- `rtrim()` - Normalizar rutas
- `password_hash()` - Hashear contraseñas
- `password_verify()` - Verificar contraseñas

---

## 6. ESTRATEGIA ANTI-COLISIONES

### Fotos de usuario:
```
usr_{uniqid}.ext
Ejemplo: usr_6757abc123def.jpg
```
- uniqid() genera ID único basado en microsegundos del sistema
- Probabilidad de colisión: extremadamente baja

### Fotos de anuncio:
```
anun_{userId}_{anuncioId}_{timestamp}_{random}.ext
Ejemplo: anun_5_123_1702234567_a1b2c3d4.jpg
```
- userId: ID del propietario
- anuncioId: ID del anuncio
- timestamp: Segundos desde 1970
- random: 8 caracteres hexadecimales aleatorios
- Probabilidad de colisión: prácticamente cero

---

## 7. VALIDACIONES DE SEGURIDAD

### En todas las operaciones con ficheros:
1. **Validar MIME type real** con finfo, no confiar en extensión
2. **Validar ruta** con realpath() para evitar path traversal
3. **Comprobar que está en proyecto** con strpos($rutaAbs, $base)
4. **Permisos 0755** al crear directorios
5. **Usar @ en unlink()** para suprimir warnings si no existe

### Ejemplo de borrado seguro:
```php
$rutaAbs = realpath(__DIR__ . DIRECTORY_SEPARATOR . $ruta);
$base = realpath(__DIR__);
if ($rutaAbs && strpos($rutaAbs, $base) === 0 && is_file($rutaAbs)) {
    @unlink($rutaAbs);
}
```

---

## 8. PASOS PARA EJECUTAR LA MIGRACIÓN DE CONTRASEÑAS

1. Abrir navegador en: `http://localhost/practicas_daw/actualizar_passwords.php`
2. Verás una tabla con el estado de cada usuario
3. El script convierte automáticamente las contraseñas en texto plano a hash
4. **ELIMINAR el archivo** `actualizar_passwords.php` después de ejecutarlo
5. Los usuarios pueden seguir usando sus mismas contraseñas
6. En phpMyAdmin verás las contraseñas hasheadas (60 caracteres con $2y$...)

---

## NOTAS IMPORTANTES

- Todos los cambios están comentados en el código
- Las fotos de usuario y anuncios se guardan en directorios separados
- Los nombres únicos previenen colisiones completamente
- Las contraseñas ya no se ven en texto plano en la BD
- Todos los borrados de ficheros son seguros y validados
- El icono por defecto es SVG escalable
