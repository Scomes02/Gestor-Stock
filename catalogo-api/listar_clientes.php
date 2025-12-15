<?php
include '../catalogo-conexion/conexion.php';

$sql = "SELECT * FROM clientes";
$resultado = mysqli_query($conexion, $sql);

$clientes = [];

while ($fila = mysqli_fetch_assoc($resultado)) {
    $clientes[] = $fila;
}

echo json_encode($clientes);


