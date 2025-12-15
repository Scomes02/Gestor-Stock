<?php
// catalogo-api/guardar_pedido.php (CONEXIÓN REAL A LA BASE DE DATOS)
session_start();
// ¡CLAVE! Habilita que MySQLi lance excepciones para errores SQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require  '../catalogo-conexion/conexion.php'; 

// 1. Verificar autenticación
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Usuario no autenticado. Inicie sesión para realizar un pedido.']));
}

$datos_raw = file_get_contents("php://input");
$payload = json_decode($datos_raw, true);

$carrito_data = $payload['carrito'] ?? [];
$metodo_pago = $payload['metodo_pago'] ?? 'Efectivo';

if (!isset($conexion) || $conexion->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Error crítico: La base de datos no está conectada.']));
}

if (empty($carrito_data)) {
    http_response_code(400);
    die(json_encode(['error' => 'Carrito vacío.']));
}

// Datos del usuario logueado
$id_usuario = $_SESSION['usuario']['id'];
$nombre_usuario = $_SESSION['usuario']['nombre'] ?? 'Cliente Desconocido'; 
$fecha = date('Y-m-d H:i:s');
$estado = 'Pendiente de Retiro';
$total = array_sum(array_map(function($p) {
    return $p['precio'] * $p['cantidad'];
}, $carrito_data));

// INICIAMOS LA TRANSACCIÓN
$conexion->begin_transaction();

try {
    // 2. Insertar la cabecera del pedido en la tabla 'pedidos'
    $stmt = $conexion->prepare("INSERT INTO pedidos (usuario_id, usuario_nombre, fecha_pedido, total, metodo_pago, estado) VALUES (?, ?, ?, ?, ?, ?)");
    
    // CORRECCIÓN CLAVE: El formato correcto es "issdss" 
    // i: INT (usuario_id), s: STRING (usuario_nombre), s: STRING (fecha), d: DOUBLE (total), s: STRING (metodo), s: STRING (estado)
    $stmt->bind_param("issdss", $id_usuario, $nombre_usuario, $fecha, $total, $metodo_pago, $estado);
    $stmt->execute();
    
    // Obtener el ID del pedido recién insertado
    $pedido_id = $conexion->insert_id;

    // 3. Insertar los detalles del pedido en la tabla 'detalle_pedido'
    $stmt_detalle = $conexion->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?, ?)");

    foreach ($carrito_data as $item) {
        $prod_id = $item['id'];
        $nombre = $item['nombre'];
        $cantidad = $item['cantidad'];
        $precio = $item['precio'];
        
        // Tipos: i (pedido_id), i (producto_id), s (nombre), i (cantidad), d (precio_unitario)
        $stmt_detalle->bind_param("iisid", $pedido_id, $prod_id, $nombre, $cantidad, $precio);
        $stmt_detalle->execute();
    }
    
    // 4. Si todo fue bien, confirmamos la transacción
    $conexion->commit();

    echo json_encode(['success' => true, 'mensaje' => 'Pedido guardado con éxito.']);
    
} catch (Exception $e) {
    // Si algo falla, revertimos todos los cambios
    $conexion->rollback();
    http_response_code(500);
    error_log("Error al guardar pedido: " . $e->getMessage());
    echo json_encode(['error' => 'Error al procesar el pedido. Intente nuevamente.']);
}
?>