<?php
// Configuración de cabeceras para respuesta JSON
header("Content-Type: application/json; charset=utf-8");
session_start();
include_once('../catalogo-conexion/conexion.php'); //

// Leer los datos enviados por el JavaScript (JSON)
$input = file_get_contents("php://input");
$datos = json_decode($input, true);

// 1. VALIDACIÓN DE ENTRADA
if (!$datos || empty($datos['items'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos: no se recibieron productos para la venta.']);
    exit;
}

// Extraer datos de la cabecera de la venta
$id_cliente = $datos['id_cliente'] ?? null; // Coincide con tu columna id_cliente
$metodo_pago = $datos['metodo_pago'] ?? 'Efectivo';
$iva = (float)($datos['iva'] ?? 0);
$total = (float)($datos['total'] ?? 0);

// 2. INICIO DE TRANSACCIÓN
$conexion->begin_transaction();

try {
    // 3. INSERTAR EN LA TABLA 'ventas'
    // La columna 'fecha' se llena automáticamente con NOW()
    $stmt = $conexion->prepare("INSERT INTO ventas (id_cliente, fecha, total, iva, metodo_pago) VALUES (?, NOW(), ?, ?, ?)");
    if (!$stmt) throw new Exception("Error al preparar venta: " . $conexion->error);

    $stmt->bind_param("idds", $id_cliente, $total, $iva, $metodo_pago);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al insertar cabecera de venta: " . $stmt->error);
    }
    
    $venta_id = $stmt->insert_id; // Obtenemos el ID de la venta recién creada
    $stmt->close();

    // 4. PREPARAR SENTENCIAS PARA DETALLES Y STOCK
    // Insertar en 'detalle_ventas' (nombre exacto según tu DB)
    $stmtDet = $conexion->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    
    // Actualizar stock en 'productos' restando la cantidad vendida
    $stmtStock = $conexion->prepare("UPDATE productos SET cantidad = cantidad - ? WHERE id = ? AND cantidad >= ?");

    if (!$stmtDet || !$stmtStock) {
        throw new Exception("Error al preparar sentencias de detalle/stock: " . $conexion->error);
    }

    // 5. RECORRER CADA PRODUCTO VENDIDO
    foreach ($datos['items'] as $it) {
        $prod_id = (int)$it['producto_id'];
        $cant = (int)$it['cantidad'];
        $prec = (float)$it['precio_unitario'];

        if ($cant <= 0) throw new Exception("Cantidad inválida para el producto ID $prod_id");

        // A. Guardar en detalle_ventas
        $stmtDet->bind_param("iiid", $venta_id, $prod_id, $cant, $prec);
        if (!$stmtDet->execute()) {
            throw new Exception("Error al insertar detalle del producto ID $prod_id: " . $stmtDet->error);
        }

        // B. Descontar stock
        // Solo se ejecuta si hay stock suficiente (cantidad >= vendida)
        $stmtStock->bind_param("iii", $cant, $prod_id, $cant);
        $stmtStock->execute();
        
        if ($conexion->affected_rows === 0) {
            throw new Exception("Stock insuficiente para el producto ID $prod_id o el producto no existe.");
        }
    }

    // 6. CONFIRMAR TODO SI NO HUBO ERRORES
    $conexion->commit();
    
    // Cerrar conexiones abiertas
    $stmtDet->close();
    $stmtStock->close();

    echo json_encode([
        'exito' => true,
        'mensaje' => 'Venta registrada con éxito y stock actualizado.',
        'venta_id' => $venta_id
    ]);

} catch (Exception $e) {
    // 7. CANCELAR TODO SI ALGO FALLÓ (Rollback)
    $conexion->rollback();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}