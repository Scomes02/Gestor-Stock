<?php
session_start();
require '../catalogo-conexion/conexion.php';

// 1. Consulta a la base de datos (Usamos la nueva columna categoria)
$resultado = $conexion->query("SELECT p.*, pr.nombre as proveedor FROM productos p LEFT JOIN proveedores pr ON p.id_proveedor = pr.id ORDER BY p.categoria ASC, p.nombre ASC");
$productos = $resultado->fetch_all(MYSQLI_ASSOC);

// 2. Agrupación dinámica por la columna 'categoria'
$categorias_agrupadas = [];
foreach ($productos as $p) {
    $cat = !empty($p['categoria']) ? $p['categoria'] : 'General';
    $categorias_agrupadas[$cat][] = $p;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="icon" href="/img/Proyecto_nuevo.ico">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda | Capibara Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styleTienda.css">
</head>

<body>
    <header class="main-header">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand text-white fw-bold" href="tienda.php">🦫 Capibara Store</a>
            <nav class="nav-menu">
                <a href="vista_cliente.php">Inicio</a>
                <a href="tienda.php" class="active">Tienda</a>
                <a href="pedidos.php">Mis Pedidos</a>
                <div class="cart-wrapper">
                    <div class="cart-icon-container" id="cartIcon">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="contador-productos">0</span>
                    </div>

                    <div class="cart-dropdown hidden" id="cartDropdown">
                        <div class="cart-items-list" id="cartItems">
                            <p class="text-center text-muted py-3">El carrito está vacío</p>
                        </div>
                        <div class="cart-footer">
                            <div class="d-flex justify-content-between fw-bold mb-3">
                                <span>Total:</span>
                                <span id="total-pagar">$0.00</span>
                            </div>
                            <button class="btn-pay w-100" id="btnPagar">Finalizar Compra</button>
                        </div>
                    </div>
                </div>
                <a></a>
                <a href="../catalogo-api/cerrar_sesion.php">Cerrar Sesión</a>
            </nav>
        </div>
    </header>

    <div class="container py-5">
        <div class="search-bar-container shadow-sm mb-5">
            <div class="row g-2">
                <div class="col-md-7">
                    <input type="text" id="search-bar" class="form-control" placeholder="¿Qué estás buscando hoy?">
                </div>
                <div class="col-md-3">
                    <select id="category-filter" class="form-select">
                        <option value="all">Todas las categorías</option>
                        <?php foreach (array_keys($categorias_agrupadas) as $cat_name): ?>
                            <option value="<?= htmlspecialchars($cat_name) ?>"><?= htmlspecialchars($cat_name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div id="grid-productos">
            <?php foreach ($categorias_agrupadas as $nombre_cat => $lista_p): ?>
                <div class="container-header mt-5 mb-3">
                    <h2 class="fw-bold text-azul"><?= strtoupper($nombre_cat) ?></h2>
                </div>
                <hr>
                <div class="row g-4 mb-5">
                    <?php foreach ($lista_p as $p): ?>
                        <div class="col-6 col-md-4 col-lg-3 product-item" data-category="<?= htmlspecialchars($nombre_cat) ?>">
                            <div class="card product-card h-100 shadow-sm <?= ($p['cantidad'] <= 0) ? 'opacity-75' : '' ?>">
                                <div class="img-zoom-container position-relative">
                                    <img src="<?= !empty($p['imagen']) ? $p['imagen'] : 'fotos index/logo.png' ?>" class="card-img-top" alt="<?= htmlspecialchars($p['nombre']) ?>">

                                    <?php if ($p['cantidad'] <= 0): ?>
                                        <div class="position-absolute top-50 start-50 translate-middle w-100 text-center">
                                            <span class="badge bg-danger fs-5 shadow">SIN STOCK</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="product-title"><?= htmlspecialchars($p['nombre']) ?></h5>
                                    <p class="product-price mt-auto">$<?= number_format($p['precio_venta'], 2, ',', '.') ?></p>

                                    <?php if ($p['cantidad'] > 0): ?>
                                        <button class="btn-add-cart w-100"
                                            data-id="<?= $p['id'] ?>"
                                            data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                            data-precio="<?= $p['precio_venta'] ?>"
                                            data-img="<?= !empty($p['imagen']) ? $p['imagen'] : 'fotos index/logo.png' ?>">
                                            <i class="fas fa-cart-plus me-2"></i>Agregar
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-secondary w-100" disabled>No disponible</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="modalCheckout" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-azul text-white">
                    <h5 class="modal-title fw-bold">Finalizar Pedido 📦</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div id="step-payment">
                        <h5 class="mb-4 text-azul fw-bold">¿Cómo deseas abonar en sucursal?</h5>
                        <div class="d-grid gap-3">
                            <button class="btn btn-outline-primary btn-pay-option" data-method="Efectivo">
                                <i class="fas fa-money-bill-wave me-2"></i> Efectivo (10% OFF)
                            </button>
                            <button class="btn btn-outline-primary btn-pay-option" data-method="Tarjeta">
                                <i class="fas fa-credit-card me-2"></i> Tarjeta Débito / Crédito
                            </button>
                            <button class="btn btn-outline-primary btn-pay-option" data-method="Transferencia">
                                <i class="fas fa-university me-2"></i> Transferencia Bancaria
                            </button>
                        </div>
                    </div>
                    <div id="step-success" class="d-none">
                        <div class="mb-3"><i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i></div>
                        <h4 class="fw-bold text-azul">¡Pedido Confirmado!</h4>
                        <p id="mensaje-final" class="text-muted mt-3"></p>
                        <button type="button" class="btn btn-azul w-100 mt-3" data-bs-dismiss="modal">Entendido</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="tienda.js"></script>
</body>

</html>