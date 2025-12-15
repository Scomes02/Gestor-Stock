<?php
// SOLUCIÓN: Verifica si la sesión NO está activa antes de iniciarla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nombreUsuario = 'Invitado';

if (isset($_SESSION['usuario'])) {
    if (is_array($_SESSION['usuario']) && isset($_SESSION['usuario']['nombre'])) {
        $nombreUsuario = $_SESSION['usuario']['nombre'];
    } elseif (!is_array($_SESSION['usuario'])) {
        // En caso de que se haya guardado solo el nombre como string
        $nombreUsuario = $_SESSION['usuario'];
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container-fluid px-4">
    <a class="navbar-brand" href="vista_cliente.php">🦫 Capibara Store</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="productos.php">Productos</a></li>
        <li class="nav-item"><a class="nav-link" href="clientes.php">Clientes</a></li>
        <li class="nav-item"><a class="nav-link" href="pedidos_admin.php">Pedidos</a></li>
        <li class="nav-item"><a class="nav-link" href="proveedores.php">Proveedores</a></li>
        <li class="nav-item"><a class="nav-link" href="analisis.php">Análisis</a></li>
        <li class="nav-item">
          <span class="nav-link text-white-50 small d-lg-inline d-none me-2">Hola, <?= htmlspecialchars($nombreUsuario) ?></span>
        </li>
        <li class="nav-item">
          <form action="../catalogo-api/cerrar_sesion.php" method="post" class="d-inline">
            <button class="btn btn-sm btn-light ms-2" type="submit">Cerrar sesión</button>
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>