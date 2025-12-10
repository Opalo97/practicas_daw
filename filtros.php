<?php
// Funciones reutilizables para validar y filtrar datos de formularios

/** Limpia un campo de texto básico */
function limpiar_texto($s) {
    $s = trim((string)$s);
    // Eliminar caracteres de control
    $s = preg_replace('/[\x00-\x1F\x7F]/u', '', $s);
    return $s;
}

function validar_usuario($usuario) {
    $usuario = limpiar_texto($usuario);
    if ($usuario === '' || ctype_space($usuario)) return false;
    // Longitud mínima
    if (mb_strlen($usuario) < 3) return false;
    return $usuario;
}

function validar_email($email) {
    $email = trim((string)$email);
    if ($email === '') return null; // opcional: campo vacío permitido

    // Longitud máxima según RFC (práctica común): 254
    if (mb_strlen($email) > 254) return false;

    // Primero usar filter_var
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

    // Además aplicar una expresión regular restrictiva (no totalmente RFC5322, pero práctica)
    // Permite letras, números y ._%+- en la parte local, y dominios con guiones y subdominios
    if (!preg_match('/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/u', $email)) {
        return false;
    }

    // Normalizar: lowercase el dominio (parte after @)
    $parts = explode('@', $email, 2);
    $local = $parts[0];
    $domain = mb_strtolower($parts[1]);
    return $local . '@' . $domain;
}

function validar_fecha($fecha) {
    $fecha = trim($fecha);
    if ($fecha === '') return null;
    // Aceptar formatos YYYY-MM-DD
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $fecha, $m)) {
        if (checkdate((int)$m[2], (int)$m[3], (int)$m[1])) return $fecha;
    }
    return false;
}

function validar_claves_nuevas($c1, $c2) {
    $c1 = trim($c1);
    $c2 = trim($c2);
    if ($c1 === '' && $c2 === '') return null; // no cambio
    if ($c1 === '' || $c2 === '') return false; // incompleto
    if ($c1 !== $c2) return false; // no coinciden
    if (mb_strlen($c1) < 4) return false; // longitud mínima
    return $c1;
}

/**
 * Procesa la subida de una foto de perfil de usuario
 * 
 * @param array $file - Array $_FILES['campo'] con el fichero subido
 * @param string $destDir - Directorio de destino ABSOLUTO (ej: /ruta/completa/img/usuarios)
 * @return array - ['ok' => true/false, 'ruta' => ruta_relativa o null, 'error' => mensaje]
 * 
 * Funcionalidad:
 * - Valida que sea imagen (jpg/png/gif) mediante MIME type real del fichero
 * - Genera nombre único con uniqid() para evitar colisiones entre usuarios
 * - Crea el directorio img/usuarios/ si no existe
 * - Mueve el fichero desde tmp a img/usuarios/usr_XXXXX.ext
 * - Devuelve ruta relativa para guardar en BD (ej: 'img/usuarios/usr_abc123.jpg')
 */
function procesar_foto_subida($file, $destDir)
{
    // Si no hay fichero subido, devolver OK sin ruta
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) return ['ok'=>true, 'ruta'=>null];

    // Verificar que la subida fue exitosa
    if ($file['error'] !== UPLOAD_ERR_OK) return ['ok'=>false, 'error'=>'Error al subir fichero.'];

    // Detectar tipo MIME real del fichero (no confiar en la extensión del nombre)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    // Validar que sea una imagen permitida y asignar extensión
    $ext = null;
    if ($mime === 'image/jpeg') $ext = 'jpg';
    elseif ($mime === 'image/png') $ext = 'png';
    elseif ($mime === 'image/gif') $ext = 'gif';
    else return ['ok'=>false, 'error'=>'Formato de imagen no permitido.'];

    // Crear directorio si no existe (0755 = permisos lectura/escritura)
    if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
        return ['ok'=>false, 'error'=>'No se puede crear directorio de destino.'];
    }

    // Generar nombre único: usr_ + ID único basado en microsegundos + extensión
    $nombre = uniqid('usr_') . '.' . $ext;
    // Ruta relativa para guardar en BD
    $rutaRel = 'img/usuarios/' . $nombre;
    // Ruta absoluta para mover el fichero
    $rutaAbs = rtrim($destDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $nombre;

    // Mover fichero desde temporal a destino final
    if (!move_uploaded_file($file['tmp_name'], $rutaAbs)) {
        return ['ok'=>false, 'error'=>'No se pudo mover el fichero subido.'];
    }

    return ['ok'=>true, 'ruta'=>$rutaRel];
}

/**
 * Procesa la subida de una foto de anuncio con estrategia anti-colisiones
 * 
 * @param array $file - Array $_FILES['campo'] con el fichero subido
 * @param string $destDir - Directorio de destino ABSOLUTO (ej: /ruta/completa/img/anuncios)
 * @param int $userId - ID del usuario propietario
 * @param int $anuncioId - ID del anuncio al que pertenece la foto
 * @return array - ['ok' => true/false, 'ruta' => ruta_relativa o null, 'error' => mensaje]
 * 
 * Funcionalidad:
 * - Valida que sea imagen (jpg/png/gif) mediante MIME type real
 * - Genera nombre ÚNICO con múltiples componentes para evitar colisiones:
 *   anun_{userId}_{anuncioId}_{timestamp}_{random}.ext
 *   Ejemplo: anun_5_123_1702234567_a1b2c3d4.jpg
 * - Esto previene colisiones cuando:
 *   * Dos usuarios suben fotos con el mismo nombre original
 *   * Un usuario sube la misma foto a diferentes anuncios
 *   * Un usuario sube dos veces la misma foto al mismo anuncio
 * - Crea el directorio img/anuncios/ si no existe
 * - Devuelve ruta relativa para guardar en BD
 */
function procesar_foto_anuncio($file, $destDir, $userId, $anuncioId)
{
    // Si no hay fichero subido, devolver OK sin ruta
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) return ['ok'=>true, 'ruta'=>null];
    // Verificar que la subida fue exitosa
    if ($file['error'] !== UPLOAD_ERR_OK) return ['ok'=>false, 'error'=>'Error al subir fichero.'];

    // Detectar tipo MIME real del fichero
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    // Validar formato de imagen y asignar extensión
    $ext = null;
    if ($mime === 'image/jpeg') $ext = 'jpg';
    elseif ($mime === 'image/png') $ext = 'png';
    elseif ($mime === 'image/gif') $ext = 'gif';
    else return ['ok'=>false, 'error'=>'Formato de imagen no permitido.'];

    // Crear directorio si no existe
    if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
        return ['ok'=>false, 'error'=>'No se puede crear directorio de destino.'];
    }

    // Generar nombre único con múltiples componentes:
    // - userId: identifica al propietario
    // - anuncioId: identifica el anuncio
    // - time(): timestamp Unix (segundos desde 1970)
    // - random_bytes(4): 4 bytes aleatorios = 8 caracteres hex
    $rand = bin2hex(random_bytes(4));
    $nombre = 'anun_' . (int)$userId . '_' . (int)$anuncioId . '_' . time() . '_' . $rand . '.' . $ext;
    // Ruta relativa para BD
    $rutaRel = 'img/anuncios/' . $nombre;
    // Ruta absoluta para mover el fichero
    $rutaAbs = rtrim($destDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $nombre;

    if (!move_uploaded_file($file['tmp_name'], $rutaAbs)) {
        return ['ok'=>false, 'error'=>'No se pudo mover el fichero subido.'];
    }
    return ['ok'=>true, 'ruta'=>$rutaRel];
}

/** Validar título de foto (no vacío) */
function validar_titulo_foto($titulo) {
    $t = limpiar_texto($titulo);
    if ($t === '' || ctype_space($t)) return false;
    // longitud razonable
    if (mb_strlen($t) > 255) return false;
    return $t;
}

/** Validar texto alternativo: mínimo 10 caracteres y no empezar por palabras redundantes */
function validar_texto_alternativo($alt) {
    $a = trim((string)$alt);
    if ($a === '') return false; // obligatorio según enunciado
    // longitud mínima
    if (mb_strlen($a) < 10) return false;

    // no debe empezar por palabras redundantes
    $forbidden = [
        'foto', 'foto de', 'imagen', 'imagen de', 'texto', 'texto alternativo', 'imagen:', 'foto:'
    ];
    $low = mb_strtolower($a);
    foreach ($forbidden as $f) {
        $f = trim(mb_strtolower($f));
        if ($f === '') continue;
        if (mb_substr($low, 0, mb_strlen($f)) === $f) return false;
    }

    return $a;
}

function validar_password_registro($clave) {
    $clave = trim($clave);

    if ($clave === '') return false;

    // Longitud 6–15
    if (mb_strlen($clave) < 6 || mb_strlen($clave) > 15) return false;

    // Solo letras inglesas, números, guion y guion bajo
    if (!preg_match('/^[A-Za-z0-9_-]+$/', $clave)) return false;

    // Al menos 1 mayúscula
    if (!preg_match('/[A-Z]/', $clave)) return false;

    // Al menos 1 minúscula
    if (!preg_match('/[a-z]/', $clave)) return false;

    // Al menos 1 número
    if (!preg_match('/[0-9]/', $clave)) return false;

    return $clave;
}
