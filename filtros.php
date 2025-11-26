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

/** Valida upload de imagen simple y devuelve array con 'ok' y 'ruta' o 'error' */
function procesar_foto_subida($file, $destDir)
{
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) return ['ok'=>true, 'ruta'=>null];

    if ($file['error'] !== UPLOAD_ERR_OK) return ['ok'=>false, 'error'=>'Error al subir fichero.'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $ext = null;
    if ($mime === 'image/jpeg') $ext = 'jpg';
    elseif ($mime === 'image/png') $ext = 'png';
    elseif ($mime === 'image/gif') $ext = 'gif';
    else return ['ok'=>false, 'error'=>'Formato de imagen no permitido.'];

    if (!is_dir($destDir) && !mkdir($destDir, 0755, true)) {
        return ['ok'=>false, 'error'=>'No se puede crear directorio de destino.'];
    }

    $nombre = uniqid('usr_') . '.' . $ext;
    $rutaRel = 'img/usuarios/' . $nombre;
    $rutaAbs = __DIR__ . DIRECTORY_SEPARATOR . $rutaRel;

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
