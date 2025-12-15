<?php
// catalogo-productos/pedidos_admin.php (LECTURA DESDE LA BASE DE DATOS)
session_start();
// Asegúrate de que esta ruta sea correcta
require '../catalogo-conexion/conexion.php'; 

// Verificar si el usuario es administrador (o al menos está logueado)
if (!isset($_SESSION['usuario'])) {
    header("Location: ../catalogo-api/login.php"); 
    exit;
}

// 1. Consulta para obtener todos los pedidos
$sql = "SELECT * FROM pedidos ORDER BY fecha_pedido DESC";
$resultado = $conexion->query($sql);
$pedidos = $resultado->fetch_all(MYSQLI_ASSOC);

// Función para obtener los detalles de un pedido
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
    <meta charset="utf-8">
    <title>Gestión de Pedidos - Capibara Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style2.css"> 
    <style>
        .estado-pendiente { background-color: #fff3cd; color: #856404; font-weight: bold; }
        .estado-entregado { background-color: #d4edda; color: #155724; }
        .estado-rechazado { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <?php include('../catalogo-productos/header.php'); // Ruta corregida ?>

    <div class="main-content"> 
        <div class="container py-5 fade-in">
            <h1 class="text-center mb-5">Gestión de Pedidos</h1>

            <?php if (empty($pedidos)): ?>
                <div class="alert alert-info text-center">No hay pedidos pendientes.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover shadow-sm">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>ID Pedido</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Total</th>
                                <th>Pago</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                                <th>Detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $p): 
                                $estado_class = 'estado-' . strtolower(str_replace(' ', '', $p['estado']));
                            ?>
                                <tr data-id="<?= $p['id'] ?>">
                                    <td><?= htmlspecialchars($p['id']) ?></td>
                                    <td><?= htmlspecialchars($p['usuario_nombre']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($p['fecha_pedido'])) ?></td>
                                    <td>$<?= number_format($p['total'], 2) ?></td>
                                    <td><?= htmlspecialchars($p['metodo_pago']) ?></td>
                                    <td class="<?= $estado_class ?>" id="estado-<?= $p['id'] ?>">
                                        <?= htmlspecialchars($p['estado']) ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-success btn-estado" data-id="<?= $p['id'] ?>" data-nuevo-estado="Entregado">Aceptar/Entregar</button>
                                        <button class="btn btn-sm btn-danger btn-estado" data-id="<?= $p['id'] ?>" data-nuevo-estado="Rechazado">Rechazar</button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#modalDetalle<?= $p['id'] ?>">Ver Artículos</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php foreach ($pedidos as $p): 
        $detalles = obtenerDetallePedido($conexion, $p['id']);
    ?>
        <div class="modal fade" id="modalDetalle<?= $p['id'] ?>" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDetalleLabel">Detalle Pedido #<?= htmlspecialchars($p['id']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Cliente: <strong><?= htmlspecialchars($p['usuario_nombre']) ?></strong></p>
                        <ul class="list-group">
                            <?php foreach ($detalles as $prod): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($prod['nombre_producto']) ?>
                                    <span class="badge bg-secondary rounded-pill"><?= $prod['cantidad'] ?> unid. | $<?= number_format($prod['precio_unitario'], 2) ?> c/u</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <footer class="mt-auto py-3">
        <div class="container text-center">
            &copy; <?= date('Y'); ?> Capibara Store - Admin
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.querySelectorAll('.btn-estado').forEach(btn => {
            btn.addEventListener('click', function() {
                const pedidoId = this.dataset.id;
                const nuevoEstado = this.dataset.nuevoEstado;
                
                fetch('../catalogo-api/update_estado.php', {
                    method: 'POST',
                    body: JSON.stringify({ id: pedidoId, estado: nuevoEstado }),
                    headers: { 'Content-Type': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar la vista sin recargar la página
                        const estadoCelda = document.getElementById(`estado-${pedidoId}`);
                        estadoCelda.innerText = nuevoEstado;
                        estadoCelda.className = ''; // Limpiar clases anteriores
                        estadoCelda.classList.add('estado-' + nuevoEstado.toLowerCase().replace(' ', ''));
                        alert('Estado actualizado a: ' + nuevoEstado);
                    } else {
                        alert('Error al actualizar: ' + (data.error || 'Desconocido'));
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('Error de conexión con el servidor.');
                });
            });
        });
    </script>
</body>
</html>