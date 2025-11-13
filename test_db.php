<?php
require_once __DIR__ . '/bd.php';

$mysqli = obtenerConexion();

$sql = "SELECT IdPais, NomPais FROM Paises";
$result = $mysqli->query($sql);

if (!$result) {
    die('Error en la consulta: ' . $mysqli->error);
}

echo "<h1>Paises en la base de datos</h1>";
echo "<ul>";
while ($fila = $result->fetch_assoc()) {
    echo "<li>{$fila['IdPais']} - {$fila['NomPais']}</li>";
}
echo "</ul>";

$result->free();
$mysqli->close();
