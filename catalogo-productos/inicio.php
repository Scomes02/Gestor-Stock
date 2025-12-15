<?php
// catalogo-productos/inicio.php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../catalogo-api/login.php");
    exit;
}

// Lógica para obtener el nombre del usuario logueado
$nombreUsuario = 'Invitado';
if (isset($_SESSION['usuario']) && is_array($_SESSION['usuario']) && isset($_SESSION['usuario']['nombre'])) {
    $nombreUsuario = $_SESSION['usuario']['nombre'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <link rel="icon" href="/img/Proyecto_nuevo.ico">
    <meta charset="utf-8">
    <title>Inicio - Capibara Store</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style2.css">
</head>

<body>
    <?php include('header.php'); ?>

    <div class="main-content">
        <div class="container py-5 fade-in">
            <h1 class="text-center mb-5">Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?> 👋</h1>

            <div class="row justify-content-center g-4">
                <div class="col-md-4">
                    <div class="card p-4 text-center h-100 shadow-sm">
                        <h5>Productos</h5>
                        <p class="text-muted small">Gestioná el stock, precios y movimientos.</p>
                        <div class="mt-auto">
                            <a href="productos.php" class="btn btn-primary">Ver productos</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-4 text-center h-100 shadow-sm">
                        <h5>Clientes</h5>
                        <p class="text-muted small">Controlá tus clientes y sus compras.</p>
                        <div class="mt-auto">
                            <a href="clientes.php" class="btn btn-success">Ver clientes</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card p-4 text-center h-100 shadow-sm">
                        <h5>Proveedores</h5>
                        <p class="text-muted small">Mantené actualizado el registro de proveedores.</p>
                        <div class="mt-auto">
                            <a href="proveedores.php" class="btn btn-info">Ver proveedores</a>
                        </div>
                    </div>
                </div>
            

                <div class="col-md-4">
                    <div class="card p-4 text-center h-100 shadow-sm">
                        <h5>Pedidos</h5>
                        <p class="text-muted small">Revisá y gestioná las órdenes de los clientes.</p>
                        <div class="mt-auto">
                            <a href="pedidos_admin.php" class="btn btn-warning text-white">Ver pedidos</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="analisis.php" class="btn btn-outline-secondary btn-lg shadow-sm">
                    📊 Ver análisis de ventas
                </a>
            </div>

        </div>
    </div>
    <footer class="mt-auto py-3">
        <div class="container text-center">
            &copy; <?php echo date('Y'); ?> Capibara Store - Todos los derechos reservados
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>