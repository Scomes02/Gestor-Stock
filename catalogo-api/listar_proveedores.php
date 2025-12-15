<?php
include '../catalogo-conexion/conexion.php';

$sql = "SELECT * FROM proveedores";
$result = mysqli_query($conexion, $sql);

$datos = [];

while ($fila = mysqli_fetch_assoc($result)) {
  $datos[] = $fila;
}

echo json_encode($datos);
