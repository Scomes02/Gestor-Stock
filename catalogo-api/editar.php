<?php
header("Content-Type: application/json");
include '../catalogo-conexion/conexion.php';

// Obtener datos del request
$datos = json_decode(file_get_contents("php://input"), true);

$id = $datos["id"] ?? null;
$nombre = $datos["nombre"] ?? '';
$precioCosto = $datos["precio_costo"] ?? 0;
$precioVenta = $datos["precio_venta"] ?? 0;
$cantidad = $datos["cantidad"] ?? 0;

if ($id && $nombre && $precioCosto >= 0 && $precioVenta >= 0 && $cantidad >= 0) {
    $stmt = $conexion->prepare("UPDATE productos SET nombre=?, precio_costo=?, precio_venta=?, cantidad=? WHERE id=?");
    $stmt->bind_param("sddii", $nombre, $precioCosto, $precioVenta, $cantidad, $id);
    
    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Producto actualizado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al actualizar producto"]);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["error" => "Datos inválidos"]);
}
?>
