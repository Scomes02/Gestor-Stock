<?php
header("Content-Type: application/json");
include '../catalogo-conexion/conexion.php';

// Obtener datos del request (desde fetch POST)
$datos = json_decode(file_get_contents("php://input"), true);

$nombre = $datos["nombre"] ?? '';
$precioCosto = $datos["precio_costo"] ?? 0;
$precioVenta = $datos["precio_venta"] ?? 0;
$cantidad = $datos["cantidad"] ?? 0;

if ($nombre && $precioCosto >= 0 && $precioVenta >= 0 && $cantidad >= 0) {
    $stmt = $conexion->prepare("INSERT INTO productos (nombre, precio_costo, precio_venta, cantidad) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sddi", $nombre, $precioCosto, $precioVenta, $cantidad);
    
    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Producto agregado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al guardar producto"]);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["error" => "Datos inválidos"]);
}
?>
