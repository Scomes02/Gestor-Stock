<?php
header("Content-Type: application/json");
include '../catalogo-conexion/conexion.php';
$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? intval($data['id']) : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

$stmt = $conexion->prepare("DELETE FROM proveedores WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['mensaje' => 'Proveedor eliminado']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al eliminar proveedor']);
}

$stmt->close();
?>
