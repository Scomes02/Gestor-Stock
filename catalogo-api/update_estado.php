<?php
session_start();
require '../catalogo-conexion/conexion.php'; 

$data = json_decode(file_get_contents("php://input"), true);
$pedido_id = $data['id'] ?? null;
$nuevo_estado = $data['estado'] ?? null;

if (!$pedido_id || !$nuevo_estado) {
    die(json_encode(['error' => 'Datos incompletos.']));
}

try {
    $conexion->begin_transaction();

    // 1. Actualizar estado del pedido
    $stmt = $conexion->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stmt->bind_param("si", $nuevo_estado, $pedido_id);
    $stmt->execute();

    // 2. SI EL PEDIDO SE ENTREGA -> REGISTRAR VENTA Y DESCONTAR STOCK
    if ($nuevo_estado === 'Entregado') {
        
        // A. Obtener datos del pedido original para la tabla 'ventas'
        $resPedido = $conexion->query("SELECT * FROM pedidos WHERE id = $pedido_id");
        $pedido = $resPedido->fetch_assoc();

        // Calcular IVA (21%)
        $total = $pedido['total'];
        $iva = $total * 0.21;

        // B. Insertar en la tabla 'ventas'
        $stmtVenta = $conexion->prepare("INSERT INTO ventas (fecha, total, iva, metodo_pago, id_cliente) VALUES (NOW(), ?, ?, ?, ?)");
        $stmtVenta->bind_param("ddsi", $total, $iva, $pedido['metodo_pago'], $pedido['id_cliente']);
        $stmtVenta->execute();
        $id_nueva_venta = $conexion->insert_id;

        // C. Obtener detalles del pedido para 'detalle_ventas' y stock
        $resDetalle = $conexion->query("SELECT * FROM detalle_pedido WHERE pedido_id = $pedido_id");
        
        while ($item = $resDetalle->fetch_assoc()) {
            $p_id = $item['producto_id']; // Asegúrate que el nombre de columna sea correcto
            $cant = $item['cantidad'];
            $precio = $item['precio_unitario'];

            // i. Insertar en detalle_ventas (Para el gráfico de Top Productos)
            $stmtDV = $conexion->prepare("INSERT INTO detalle_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
            $stmtDV->bind_param("iiid", $id_nueva_venta, $p_id, $cant, $precio);
            $stmtDV->execute();

            // ii. Descontar Stock
            $conexion->query("UPDATE productos SET cantidad = cantidad - $cant WHERE id = $p_id");
        }
    }

    $conexion->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}