<!DOCTYPE html>
<html lang="es">
  
<head>
  <link rel="icon" href="/img/Proyecto_nuevo.ico">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Catálogo de Productos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style2.css" />
</head>

<body>
  <?php include('header.php'); ?>
  <div class="container py-4">
    <h1 class="text-center mb-4">CAPIBARA STORE</h1>
    <h1 class="text-center mb-4">Stock Productos</h1>
    <a href="../catalogo-productos/inicio.php" class="btn btn-link">Volver</a>

    <!-- Formulario -->
    <form id="productForm" class="mb-4">
      <div class="row g-3">
        <input type="hidden" id="productId" />

        <!-- Nombre del producto -->
        <div class="col-md-3">
          <input type="text" class="form-control" id="name" placeholder="Nombre del producto" required>
        </div>

        <!-- Precio Costo -->
        <div class="col-md-3">
          <input type="number" class="form-control" id="costPrice" placeholder="Precio costo ($)" min="0" step="0.01" required>
        </div>

        <!-- Precio Venta -->
        <div class="col-md-3">
          <input type="number" class="form-control" id="price" placeholder="Precio venta ($)" min="0" step="0.01" required>
        </div>

        <!-- Cantidad -->
        <div class="col-md-3">
          <input type="number"  class="form-control" id="quantity" placeholder="Cantidad de unidades" min="0" required>
        </div>

        <!-- Botón guardar -->
        <div class="col-12 d-grid mt-3">
          <button type="submit" class="btn btn-primary">Guardar Producto</button>
        </div>
  </div>
    </form>

    <!--boton proveedores y clientes-->
    <div class="mb-3">
      <button class="btn btn-secondary mt-3 mb-3" onclick="generarPDF()">Imprimir Stock</button>
      <a href="proveedores.html" class="btn btn-info mt-3 mb-3 ms-2">Proveedores</a>
      <a href="clientes.html" class="btn btn-success mt-3 mb-3 ms-2">Clientes</a>
    </div>


    <div class="mb-3">
      <input type="text" id="buscador" class="form-control" placeholder="Buscar productos por nombre...">
    </div> 

    <!-- Tabla de productos -->
    <table class="table table-striped table-bordered" id="productTable">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>Precio Costo</th>
          <th>Precio Venta</th>
          <th>Cantidad</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody id="productList"></tbody>
    </table>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="app.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</body>
</html>

