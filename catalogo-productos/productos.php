<?php
session_start();
if (!isset($_SESSION['usuario']['id'])) {
    header("Location: login.php");
    exit();
}
include '../catalogo-conexion/conexion.php';
$userId = $_SESSION['usuario']['id'];

// 1. Productos cargados hoy
$sqlSesion = "SELECT * FROM productos WHERE id_usuario = $userId AND DATE(fecha_creacion) = CURDATE() ORDER BY id DESC";
$resSesion = $conexion->query($sqlSesion);

// 2. Stock Completo (Incluimos la columna categoria e imagen)
$sqlFull = "SELECT p.*, pr.nombre as proveedor_nombre 
            FROM productos p
            JOIN proveedores pr ON p.id_proveedor = pr.id
            ORDER BY p.id ASC";
$resultado = $conexion->query($sqlFull);
$productos_base = $resultado->fetch_all(MYSQLI_ASSOC);

// 3. Select de Proveedores
$sqlProv = "SELECT id, nombre FROM proveedores ORDER BY nombre ASC";
$resProveedores = $conexion->query($sqlProv);

// 4. Select de Clientes
$sqlCli = "SELECT id, nombre FROM clientes ORDER BY nombre ASC";
$resClientes = $conexion->query($sqlCli);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="/img/Proyecto_nuevo.ico">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Stock y Carga de Productos | Capibara Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style2.css" />
</head>
<body>
    <?php include('header.php'); ?>
    <div class="container py-4">
        <h1 class="text-center mb-4">CAPIBARA STORE</h1>
        <a href="inicio.php" class="btn btn-link">Volver al Inicio</a>

        <div class="card p-4 my-4 shadow-sm">
            <h2 class="text-center mb-3">Carga de Nuevos Productos 📦</h2>
            <h4 id="tituloProveedor" class="text-center text-secondary mb-3">Cargando proveedor...</h4>
            
            <form id="formProducto" class="mb-4" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-12 mb-2">
                        <label for="id_proveedor" class="form-label fw-bold">Proveedor:</label>
                        <select id="id_proveedor" name="id_proveedor" class="form-select" required>
                            <option value="">-- Seleccione un proveedor --</option>
                            <?php foreach ($resProveedores as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Nombre</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Nombre" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Costo</label>
                        <input type="number" id="precio_costo" name="precio_costo" class="form-control" placeholder="Costo" step="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Venta</label>
                        <input type="number" id="precio_venta" name="precio_venta" class="form-control" placeholder="Venta" step="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Cant.</label>
                        <input type="number" id="cantidad" name="cantidad" class="form-control" placeholder="Cant." min="1" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Categoría:</label>
                        <select name="categoria" id="categoriaSelect" class="form-select" onchange="toggleNuevaCat(this)" required>
                            <option value="Electronica y Robotica">Electrónica y Robótica</option>
                            <option value="Perifericos y Accesorios">Periféricos y Accesorios</option>
                            <option value="Smart Home y Gadgets">Smart Home y Gadgets</option>
                            <option value="Merchandising Capibara">Merchandising Capibara</option>
                            <option value="General">General</option>
                            <option value="NUEVA" style="background-color: #d4edda;">+ Agregar Nueva Categoría...</option>
                        </select>
                        <input type="text" name="nueva_categoria" id="inputNuevaCat" class="form-control mt-2" style="display:none;" placeholder="Nombre de la nueva categoría">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Imagen del Producto:</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                    </div>
                    <div class="col-12 d-grid mt-3">
                        <button type="submit" class="btn btn-primary" id="btnSubmitForm">Guardar Producto</button>
                    </div>
                </div>
            </form>

            <h4 class="text-center mb-4 mt-4">Productos cargados hoy</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th><th>Costo</th><th>Venta</th><th>Cant.</th><th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tablaProductos">
                        <?php foreach ($resSesion as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nombre']) ?></td>
                                <td>$<?= number_format($row['precio_costo'], 2) ?></td>
                                <td>$<?= number_format($row['precio_venta'], 2) ?></td>
                                <td><?= $row['cantidad'] ?></td>
                                <td class="text-success">Cargado</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h2 class="text-center mb-4 mt-5">Stock Completo de la Tienda</h2>
        <div class="mb-3">
            <input type="text" id="buscador" class="form-control" placeholder="Buscar productos por nombre...">
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="productTable">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th><th>Nombre</th><th>Costo</th><th>Venta</th><th>Stock</th><th>Proveedor</th><th>Acción</th>
                    </tr>
                </thead>
                <tbody id="productList">
                    <?php foreach ($productos_base as $producto): ?>
                        <tr>
                            <td><?= $producto['id'] ?></td>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td>$<?= number_format($producto['precio_costo'], 2) ?></td>
                            <td>$<?= number_format($producto['precio_venta'], 2) ?></td>
                            <?php
                            $stock = (int)$producto['cantidad'];
                            $color = ($stock < 10) ? 'text-danger fw-bold' : (($stock < 50) ? 'text-warning' : 'text-success');
                            ?>
                            <td class="<?= $color ?>"><?= $stock ?></td>
                            <td><?= htmlspecialchars($producto['proveedor_nombre']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalVentaRapida"
                                    data-id="<?= $producto['id'] ?>" data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                                    data-precio="<?= $producto['precio_venta'] ?>" data-stock="<?= $stock ?>">Vender</button>

                                <button class="btn btn-sm btn-warning btn-editar-accion"
                                    data-id="<?= $producto['id'] ?>"
                                    data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                                    data-costo="<?= $producto['precio_costo'] ?>"
                                    data-venta="<?= $producto['precio_venta'] ?>"
                                    data-stock="<?= $stock ?>"
                                    data-categoria="<?= htmlspecialchars($producto['categoria'] ?? 'General') ?>"
                                    data-proveedor="<?= $producto['id_proveedor'] ?>">Editar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button class="btn btn-secondary mt-3" onclick="generarPDFStockCompleto()">Imprimir Stock Total</button>
    </div>

    <div class="modal fade" id="modalVentaRapida" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-success">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Venta Rápida ⚡</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="formVentaRapida">
                    <div class="modal-body">
                        <input type="hidden" id="v_id"><input type="hidden" id="v_precio">
                        <p class="mb-2">Producto: <strong id="v_nombre"></strong></p>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cliente</label>
                            <select id="v_cliente" class="form-select">
                                <option value="">Consumidor Final</option>
                                <?php foreach ($resClientes as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-bold">Cantidad</label>
                                <input type="number" id="v_cant" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold">Pago</label>
                                <select id="v_pago" class="form-select">
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Débito">Débito</option>
                                    <option value="Crédito">Crédito</option>
                                </select>
                            </div>
                        </div>
                        <h4 class="mt-3 text-end text-success" id="v_total">$0.00</h4>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-success w-100" id="btnConfirmarVenta">Confirmar</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="app.js"></script>
    <script>
        function toggleNuevaCat(select) {
            document.getElementById('inputNuevaCat').style.display = (select.value === 'NUEVA') ? 'block' : 'none';
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</body>
</html>