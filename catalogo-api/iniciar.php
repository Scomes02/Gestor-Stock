<?php
session_start();
include '../catalogo-conexion/conexion.php';

$usuario = $_POST['usuario'] ?? '';
$clave = $_POST['clave'] ?? '';

if (!$usuario || !$clave) {
    echo json_encode(["error" => "Usuario y clave son requeridos"]);
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE nombre = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($clave, $row['Clave'])) {
        $_SESSION['usuario'] = $usuario;
        header("Location: ../catalogo-productos/inicio.php");
        exit;
        //echo json_encode(["mensaje" => "Inicio de sesión exitoso"]);
    } else {
        echo json_encode(["error" => "Clave incorrecta"]);
    }
} else {
    echo json_encode(["error" => "Usuario no encontrado"]);
}
?>
