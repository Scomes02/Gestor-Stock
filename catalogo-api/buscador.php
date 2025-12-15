<?php
include '../catalogo-conexion/conexion.php';

$query = $_GET['query'] ?? '';

$sql = "SELECT * FROM productos WHERE nombre LIKE ?";
$stmt = $conn->prepare($sql);
$param = '%' . $query . '%';
$stmt->bind_param("s", $param);
$stmt->execute();
$result = $stmt->get_result();

$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}

echo json_encode($productos);
