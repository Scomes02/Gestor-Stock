<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    echo json_encode([]);
    exit;
}
header("Content-Type: application/json");
include '../catalogo-conexion/conexion.php';

$resultado = $conexion->query("SELECT * FROM productos");
$productos = [];

while ($fila = $resultado->fetch_assoc()) {
    $productos[] = [
        "id" => (int)$fila["id"],
        "nombre" => $fila["nombre"],
        "precio_costo" => (float)$fila["precio_costo"],
        "precio_venta" => (float)$fila["precio_venta"],
        "cantidad" => (int)$fila["cantidad"]
    ];
}

echo json_encode($productos);
?>

