<?php
session_start();
require '../catalogo-conexion/conexion.php';

// 1. PRIMERO: Realizar la consulta a la base de datos
$resultado = $conexion->query("SELECT p.*, pr.nombre as proveedor FROM productos p LEFT JOIN proveedores pr ON p.id_proveedor = pr.id ORDER BY p.nombre ASC");
$productos = $resultado->fetch_all(MYSQLI_ASSOC);

// 2. SEGUNDO: Inicializar y llenar las categorías agrupadas
$categorias_agrupadas = [
    'Electronica y Robotica' => [],   // IDs 100-199
    'Perifericos y Accesorios' => [], // IDs 200-299
    'Smart Home y Gadgets' => [],     // IDs 300-399
    'Merchandising Capibara' => []    // IDs 400-499
];

foreach ($productos as $p) {
    if ($p['id'] >= 100 && $p['id'] < 200) $categorias_agrupadas['Electronica y Robotica'][] = $p;
    elseif ($p['id'] >= 200 && $p['id'] < 300) $categorias_agrupadas['Perifericos y Accesorios'][] = $p;
    elseif ($p['id'] >= 300 && $p['id'] < 400) $categorias_agrupadas['Smart Home y Gadgets'][] = $p;
    elseif ($p['id'] >= 400 && $p['id'] < 500) $categorias_agrupadas['Merchandising Capibara'][] = $p;
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
                <a href="pedidos.php" class="active">Mis Pedidos</a>
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
                        <option value="Electronica y Robotica">Electrónica y Robótica</option>
                        <option value="Perifericos y Accesorios">Periféricos y Accesorios</option>
                        <option value="Smart Home y Gadgets">Smart Home y Gadgets</option>
                        <option value="Merchandising Capibara">Merchandising Capibara</option>
                    </select>
                </div>
            </div>
        </div>

        <div id="grid-productos">
            <?php foreach ($categorias_agrupadas as $nombre_cat => $lista_p): ?>
                <?php if (!empty($lista_p)): ?>
                    <div class="container-header mt-5 mb-3">
                        <h2 class="fw-bold text-azul"><?= strtoupper($nombre_cat) ?></h2>
                    </div>
                    <hr>
                    <div class="row g-4 mb-5">
                        <?php foreach ($lista_p as $p): ?>
                            <div class="col-6 col-md-4 col-lg-3 product-item" data-category="<?= $nombre_cat ?>">
                                <div class="card product-card h-100 shadow-sm">
                                    <div class="img-zoom-container">
                                        <img src="<?= !empty($p['imagen']) ? $p['imagen'] : 'fotos index/logo.png' ?>" class="card-img-top" alt="<?= htmlspecialchars($p['nombre']) ?>">
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="product-title"><?= htmlspecialchars($p['nombre']) ?></h5>
                                        <p class="product-price mt-auto">$<?= number_format($p['precio_venta'], 2, ',', '.') ?></p>
                                        <button class="btn-add-cart w-100"
                                            data-id="<?= $p['id'] ?>"
                                            data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                                            data-precio="<?= $p['precio_venta'] ?>"
                                            data-img="<?= !empty($p['imagen']) ? $p['imagen'] : 'fotos index/logo.png' ?>">
                                            <i class="fas fa-cart-plus me-2"></i>Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="modal fade" id="modalCheckout" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-azul text-white">
                    <h5 class="modal-title fw-bold">Finalizar Pedido 📦</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div id="step-payment">
                        <h5 class="mb-4 text-azul fw-bold">¿Cómo deseas abonar en sucursal?</h5>
                        <div class="d-grid gap-3">
                            <button class="btn btn-outline-primary btn-pay-option" data-method="Efectivo">
                                <i class="fas fa-money-bill-wave me-2"></i> Efectivo (10% OFF)
                            </button>
                            <button class="btn btn-outline-primary btn-pay-option" data-method="Débito/Crédito">
                                <i class="fas fa-credit-card me-2"></i> Tarjeta Débito / Crédito
                            </button>
                            <button class="btn btn-outline-primary btn-pay-option" data-method="Transferencia">
                                <i class="fas fa-university me-2"></i> Transferencia Bancaria
                            </button>
                        </div>
                    </div>

                    <div id="step-success" class="d-none">
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
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