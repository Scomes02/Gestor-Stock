<?php
header('Content-Type: application/json');
include '../catalogo-conexion/conexion.php';

$data = json_decode(file_get_contents("php://input"), true);
$nombre = isset($data['nombre']) ? trim($data['nombre']) : '';

if ($nombre === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre requerido']);
    exit;
}

$stmt = $conexion->prepare("SELECT cantidad FROM productos WHERE nombre = ?");
$stmt->bind_param("s", $nombre);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['existe' => true, 'cantidad' => (int)$row['cantidad']]);
} else {
    echo json_encode(['existe' => false]);
}

$stmt->close();
?>
