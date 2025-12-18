<?php 
// catalogo-productos/pedidos.php (CÓDIGO CORREGIDO PARA LEER DE LA DB)
session_start();
// Asegúrate de que esta ruta a tu conexión sea correcta
require '../catalogo-conexion/conexion.php'; 

// 1. Verificar si el usuario está logueado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario']['id'])) {
    header("Location: ../catalogo-api/login.php");
    exit;
}

$id_usuario_actual = $_SESSION['usuario']['id'];

// 2. Consulta para obtener los pedidos de ESTE usuario
$sql = "SELECT id, fecha_pedido, total, estado FROM pedidos WHERE usuario_id = ? ORDER BY fecha_pedido DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario_actual);
$stmt->execute();
$resultado = $stmt->get_result();
$pedidos_usuario = $resultado->fetch_all(MYSQLI_ASSOC);

// Función para obtener los detalles de un pedido específico
function obtenerDetallePedido($conexion, $pedido_id) {
    $sql_detalle = "SELECT nombre_producto, cantidad, precio_unitario FROM detalle_pedido WHERE pedido_id = ?";
    $stmt = $conexion->prepare($sql_detalle);
    $stmt->bind_param("i", $pedido_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    return $resultado->fetch_all(MYSQLI_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="/img/Proyecto_nuevo.ico">
    <meta charset="UTF-8">
    <title>Mis Pedidos - Capibara Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styleTienda.css"> 
    <style>
        .pedido-card { border-left: 5px solid #0B173D; }
        .producto-item img { width: 50px; height: 50px; object-fit: cover; }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand text-white fw-bold" href="tienda.php">🦫 Capibara Store</a>
            <nav class="nav-menu">
                <a href="vista_cliente.php">Inicio</a>
                <a href="tienda.php">Tienda</a>
                <a href="pedidos.php" class="active">Mis Pedidos</a> 
                <a href="../catalogo-api/cerrar_sesion.php">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <div class="container py-5">
        <h1 class="mb-4 text-azul fw-bold">Historial de Pedidos</h1>
        
        <?php if (empty($pedidos_usuario)): ?>
            <div class="alert alert-warning text-center">
                Aún no tienes pedidos registrados. ¡Visita la tienda!
            </div>
        <?php else: ?>
            <?php foreach ($pedidos_usuario as $pedido): ?>
                <?php $detalles = obtenerDetallePedido($conexion, $pedido['id']); ?>
                <div class="card pedido-card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold">Pedido #<?= htmlspecialchars($pedido['id']) ?></span> 
                            <small class="text-muted ms-3">Fecha: <?= date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])) ?></small>
                        </div>
                        <span class="badge bg-primary"><?= htmlspecialchars($pedido['estado']) ?></span>
                    </div>
                    <div class="card-body">
                        <p class="fw-bold mb-2">Total Pagado: <span class="text-success">$<?= number_format($pedido['total'], 2) ?></span></p>
                        
                        <h6 class="mt-3">Productos:</h6>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($detalles as $producto): ?>
                                <li class="list-group-item d-flex align-items-center producto-item">
                                    <div class="flex-grow-1">
                                        <?= htmlspecialchars($producto['nombre_producto']) ?>
                                    </div>
                                    <span class="text-muted"><?= $producto['cantidad'] ?> unid. x $<?= number_format($producto['precio_unitario'], 2) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>