<?php
header("Content-Type: application/json");
include '../catalogo-conexion/conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = isset($data['id']) ? intval($data['id']) : 0;
$nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
$direccion = isset($data['direccion']) ? trim($data['direccion']) : '';
$telefono = isset($data['telefono']) ? trim($data['telefono']) : '';
$rubro = isset($data['rubro']) ? trim($data['rubro']) : '';

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

$stmt = $conexion->prepare("UPDATE clientes SET nombre = ?, direccion = ?, telefono = ?, rubro = ? WHERE id = ?");
$stmt->bind_param("ssssi", $nombre, $direccion, $telefono, $rubro, $id);

if ($stmt->execute()) {
    echo json_encode(['mensaje' => 'Cliente actualizado correctamente']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar cliente']);
}

$stmt->close();
?>


