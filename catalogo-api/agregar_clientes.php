<?php
header("Content-Type: application/json");
include '../catalogo-conexion/conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

// Validación y sanitización básica
$nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
$direccion = isset($data['direccion']) ? trim($data['direccion']) : '';
$telefono = isset($data['telefono']) ? trim($data['telefono']) : '';
$rubro = isset($data['rubro']) ? trim($data['rubro']) : '';

if ($nombre === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre requerido']);
    exit;
}

$stmt = $conexion->prepare("INSERT INTO clientes (nombre, direccion, telefono, rubro) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nombre, $direccion, $telefono, $rubro);

if ($stmt->execute()) {
    echo json_encode(['mensaje' => 'Cliente agregado']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al agregar cliente']);
}

$stmt->close();
?>
