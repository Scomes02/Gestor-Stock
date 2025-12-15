<?php
// Iniciamos sesión para saber quién está operando
session_start();
include '../catalogo-conexion/conexion.php';

// Verificación de seguridad: Solo usuarios logueados pueden guardar
if (!isset($_SESSION['usuario']['id'])) {
    echo json_encode(['error' => 'Sesión no válida. Por favor, inicie sesión nuevamente.']);
    exit;
}

$id_usuario = $_SESSION['usuario']['id']; // Obtenemos el ID del usuario actual
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['nombre'])) {
  echo json_encode(['error' => 'Datos incompletos o mal formateados']);
  exit;
}

$nombre = mysqli_real_escape_string($conexion, $data['nombre']);
$precio_costo = floatval($data['precio_costo']);
$precio_venta = floatval($data['precio_venta']);
$cantidad = intval($data['cantidad']);
$id_proveedor = intval($data['id_proveedor']); // Asegúrate de recibirlo del JS

// 1. Verificar si el producto ya existe
$sql = "SELECT cantidad FROM productos WHERE nombre = '$nombre'";
$result = mysqli_query($conexion, $sql);

if (mysqli_num_rows($result) > 0) {
  // === CASO UPDATE: Actualizamos stock y registramos quién hizo la última carga ===
  $actual = mysqli_fetch_assoc($result)['cantidad'];
  $nuevaCantidad = $actual + $cantidad;

  $update = "UPDATE productos 
             SET precio_costo='$precio_costo', 
                 precio_venta='$precio_venta', 
                 cantidad=$nuevaCantidad,
                 id_usuario=$id_usuario 
             WHERE nombre='$nombre'";
             
  $ok = mysqli_query($conexion, $update);
  if ($ok) {
    echo json_encode(['mensaje' => 'Stock actualizado correctamente']);
  } else {
    echo json_encode(['error' => 'Error al actualizar: ' . mysqli_error($conexion)]);
  }
} else {
  // === CASO INSERT: Guardamos el producto nuevo vinculado al usuario ===
  $insert = "INSERT INTO productos (nombre, precio_costo, precio_venta, cantidad, id_proveedor, id_usuario)
             VALUES ('$nombre', '$precio_costo', '$precio_venta', '$cantidad', '$id_proveedor', '$id_usuario')";
             
  $ok = mysqli_query($conexion, $insert);
  if ($ok) {
    echo json_encode(['mensaje' => 'Producto registrado con éxito']);
  } else {
    echo json_encode(['error' => 'Error al agregar: ' . mysqli_error($conexion)]);
  }
}