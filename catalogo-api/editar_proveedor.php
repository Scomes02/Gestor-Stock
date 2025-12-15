<?php
header("Content-Type: application/json");
include '../catalogo-conexion/conexion.php';
$data = json_decode(file_get_contents("php://input"), true);

$id = isset($data['id']) ? intval($data['id']) : 0;
$rubro = isset($data['rubro']) ? trim($data['rubro']) : '';
$nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
$direccion = isset($data['direccion']) ? trim($data['direccion']) : '';
$telefono = isset($data['telefono']) ? trim($data['telefono']) : '';

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

$stmt = $conexion->prepare("UPDATE proveedores SET rubro = ?, nombre = ?, direccion = ?, telefono = ? WHERE id = ?");
$stmt->bind_param("ssssi", $rubro, $nombre, $direccion, $telefono, $id);

if ($stmt->execute()) {
    echo json_encode(['mensaje' => 'Proveedor actualizado']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar proveedor']);
}

$stmt->close();
?>
