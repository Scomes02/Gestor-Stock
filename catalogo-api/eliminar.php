<?php
header("Content-Type: application/json");
include '../catalogo-conexion/conexion.php';

$datos = json_decode(file_get_contents("php://input"), true);
$id = $datos["id"] ?? null;

if ($id) {
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["mensaje" => "Producto eliminado correctamente"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar producto"]);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode(["error" => "ID inválido"]);
}
?>
