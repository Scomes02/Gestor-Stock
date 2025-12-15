<?php
// catalogo-api/listar_ventas.php
header("Content-Type: application/json");
include_once('../catalogo-conexion/conexion.php');

$sql = "SELECT v.id, v.cliente_id, v.fecha, v.total, v.iva, v.metodo_pago, v.usuario, c.nombre AS cliente_nombre
        FROM ventas v
        LEFT JOIN clientes c ON c.id = v.cliente_id
        ORDER BY v.fecha DESC
        LIMIT 200";

$result = $conexion->query($sql);
if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar ventas: ' . $conexion->error]);
    exit;
}

$ventas = [];
while ($row = $result->fetch_assoc()) {
    $row['detalles'] = [];
    $stmt = $conexion->prepare("SELECT producto_id, nombre_producto, cantidad, precio_unitario, subtotal FROM venta_detalle WHERE venta_id = ?");
    $stmt->bind_param("i", $row['id']);
    $stmt->execute();
    $resd = $stmt->get_result();
    while ($d = $resd->fetch_assoc()) {
        $row['detalles'][] = $d;
    }
    $stmt->close();
    $ventas[] = $row;
}

echo json_encode($ventas);
