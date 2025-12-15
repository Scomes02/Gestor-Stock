<?php
header("Content-Type: application/json");
include '../catalogo-conexion/conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

$rubro = isset($data['rubro']) ? trim($data['rubro']) : '';
$nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
$direccion = isset($data['direccion']) ? trim($data['direccion']) : '';
$telefono = isset($data['telefono']) ? trim($data['telefono']) : '';

if ($nombre === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre del proveedor requerido']);
    exit;
}

$stmt = $conexion->prepare("INSERT INTO proveedores (rubro, nombre, direccion, telefono) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $rubro, $nombre, $direccion, $telefono);

if ($stmt->execute()) {
    echo json_encode(['mensaje' => 'Proveedor agregado']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al agregar proveedor']);
}

$stmt->close();
?>
