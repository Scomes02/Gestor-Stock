<?php
// catalogo-api/update_estado.php (ACTUALIZAR EN LA BASE DE DATOS)
session_start();
require '../catalogo-conexion/conexion.php'; 

if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    die(json_encode(['error' => 'No autorizado.']));
}

$datos_raw = file_get_contents("php://input");
$data = json_decode($datos_raw, true);

$pedido_id = $data['id'] ?? null;
$nuevo_estado = $data['estado'] ?? null;

if ($pedido_id === null || $nuevo_estado === null) {
    http_response_code(400);
    die(json_encode(['error' => 'Faltan datos (ID o estado).']));
}

try {
    // Consulta para actualizar el estado del pedido por su ID
    $stmt = $conexion->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_estado, $pedido_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'mensaje' => 'Estado del pedido actualizado.']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Pedido no encontrado o estado sin cambios.']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    error_log("Error al actualizar estado: " . $e->getMessage());
    echo json_encode(['error' => 'Error de servidor al actualizar el estado.']);
}
?>