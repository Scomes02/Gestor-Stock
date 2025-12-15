<!DOCTYPE html>
<html lang="es">

<head>
  <link rel="icon" href="/img/Proyecto_nuevo.ico">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestión de Proveedores | Capibara Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style2.css" />
</head>

<body>
  <?php include('header.php'); ?>
  <div class="container py-4">
    <h1 class="text-center mb-4">Gestión de Proveedores</h1>
    <a href="../catalogo-productos/inicio.php" class="btn btn-link">Volver</a>

    <form id="formProveedor" class="mb-4">
      <input type="hidden" id="idProveedor" />
      <div class="row g-2">
        <div class="col-md-3">
          <input type="text" class="form-control" id="rubro" placeholder="Rubro" required>
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" id="nombre" placeholder="Nombre" required>
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" id="direccion" placeholder="Dirección" required>
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" id="telefono" placeholder="Teléfono" required>
        </div>
      </div>

      <div class="col-12 d-grid mt-3">
        <button class="btn btn-primary" type="submit">Guardar Proveedor</button>
      </div>

      <div class="row g-2 mt-2">
        <div class="col-md-2">
          <button class="btn btn-success" type="button" onclick="generarPDF()">Imprimir Listado</button>
        </div>
      </div>
    </form>

    <input type="text" id="buscador" class="form-control mb-3" placeholder="Buscar por nombre o rubro...">

    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>Rubro</th>
          <th>Nombre</th>
          <th>Dirección</th>
          <th>Teléfono</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody id="tablaProveedores"></tbody>
    </table>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

  <script>
    let proveedores = [];

    // 1. CARGA DE DATOS (Ruta relativa corregida)
    function cargarProveedores() {
      fetch("../catalogo-api/listar_proveedores.php")
        .then(res => {
          if (!res.ok) throw new Error("No se pudo cargar la lista de proveedores");
          return res.json();
        })
        .then(data => {
          proveedores = data;
          renderProveedores();
        })
        .catch(err => console.error(err));
    }

    // 2. RENDERIZADO DE TABLA (Enlace de carga corregido)
    function renderProveedores(filtrados = null) {
      const lista = document.getElementById("tablaProveedores");
      lista.innerHTML = "";
      (filtrados ?? proveedores).forEach(p => {
        lista.innerHTML += `
          <tr>
            <td>${p.rubro}</td>
            <td>${p.nombre}</td>
            <td>${p.direccion}</td>
            <td>${p.telefono}</td>
            <td>
              <button class="btn btn-sm btn-warning" onclick="editar(${p.id})">Editar</button>
              <button class="btn btn-sm btn-danger" onclick="eliminar(${p.id})">Eliminar</button>
              <a href="productos.php?id=${p.id}&proveedor=${encodeURIComponent(p.nombre)}" 
                 class="btn btn-sm btn-info mt-1">Cargar productos</a>
            </td>
          </tr>`;
      });
    }

    // 3. BUSCADOR
    document.getElementById("buscador").addEventListener("keyup", function () {
      const texto = this.value.toLowerCase();
      const filtrados = proveedores.filter(p =>
        p.nombre.toLowerCase().includes(texto) || p.rubro.toLowerCase().includes(texto)
      );
      renderProveedores(filtrados);
    });

    // 4. GUARDAR / EDITAR (Rutas corregidas)
    document.getElementById("formProveedor").addEventListener("submit", function (e) {
      e.preventDefault();
      const id = document.getElementById("idProveedor").value;
      const proveedor = {
        id,
        rubro: document.getElementById("rubro").value,
        nombre: document.getElementById("nombre").value,
        direccion: document.getElementById("direccion").value,
        telefono: document.getElementById("telefono").value
      };

      const endpoint = id ? "editar_proveedor.php" : "agregar_proveedor.php";
      
      fetch(`../catalogo-api/${endpoint}`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(proveedor)
      })
        .then(res => res.json())
        .then(data => {
          alert(data.mensaje || data.error);
          this.reset();
          document.getElementById("idProveedor").value = "";
          cargarProveedores();
        });
    });

    // 5. EDITAR
    function editar(id) {
      const p = proveedores.find(p => p.id == id);
      if (!p) return;
      document.getElementById("idProveedor").value = p.id;
      document.getElementById("rubro").value = p.rubro;
      document.getElementById("nombre").value = p.nombre;
      document.getElementById("direccion").value = p.direccion;
      document.getElementById("telefono").value = p.telefono;
      window.scrollTo(0, 0); // Sube al formulario
    }

    // 6. ELIMINAR (Ruta corregida)
    function eliminar(id) {
      if (!confirm("¿Deseas eliminar este proveedor?")) return;

      fetch("../catalogo-api/eliminar_proveedor.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
      })
        .then(res => res.json())
        .then(data => {
          alert(data.mensaje || data.error);
          cargarProveedores();
        });
    }

    // 7. PDF
    function generarPDF() {
      if (!proveedores.length) {
        alert("No hay proveedores para imprimir.");
        return;
      }
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();
      doc.setFontSize(18);
      doc.text("Listado de Proveedores", 14, 20);
      const filas = proveedores.map(p => [p.rubro, p.nombre, p.direccion, p.telefono]);
      doc.autoTable({
        head: [["Rubro", "Nombre", "Dirección", "Teléfono"]],
        body: filas,
        startY: 30
      });
      window.open(doc.output("bloburl"));
    }

    cargarProveedores();
  </script>
</body>
</html>