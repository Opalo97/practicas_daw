<?php
function obtenerConexion()
{
    // Lee el fichero INI que está en la misma carpeta que este archivo
    $rutaConfig = __DIR__ . '/config.ini';

    if (!file_exists($rutaConfig)) {
        die('No se encuentra el fichero de configuración de la base de datos.');
    }

    $config = parse_ini_file($rutaConfig, true);

    if ($config === false || !isset($config['DB'])) {
        die('Error al leer la configuración de la base de datos.');
    }

    $db = $config['DB'];

    $servidor = $db['Server']   ?? 'localhost';
    $usuario  = $db['User']     ?? '';
    $clave    = $db['Password'] ?? '';
    $nombreBD = $db['Database'] ?? '';
    $charset  = $db['Charset']  ?? 'utf8mb4';

    $mysqli = @new mysqli($servidor, $usuario, $clave, $nombreBD);

    if ($mysqli->connect_errno) {
        die('Error al conectar con la base de datos: ' . $mysqli->connect_error);
    }

    if (!$mysqli->set_charset($charset)) {
        die('Error al establecer el conjunto de caracteres: ' . $mysqli->error);
    }

    return $mysqli;
}
