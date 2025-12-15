<!DOCTYPE html>
<html lang="es">

<head>
  <link rel="icon" href="/img/Proyecto_nuevo.ico">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gestion de Clientes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style2.css" />
</head>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<body>
  <?php include('header.php'); ?>
  <div class="container py-4">
    <h1 class="text-center mb-4">Gestión de Clientes</h1>
    <a href="../catalogo-productos/inicio.php" class="btn btn-link">Volver</a>

    <form id="formCliente" class="mb-4">
      <input type="hidden" id="idCliente" />
      <div class="row g-2">
        <div class="col-md-3">
          <input type="text" class="form-control" id="nombre" placeholder="Nombre" required>
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" id="direccion" placeholder="Dirección" required>
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" id="telefono" placeholder="Teléfono" required>
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" id="rubro" placeholder="Rubro" required>
        </div>
      </div>

      <div class="col-12 d-grid mt-3">
        <button class="btn btn-primary" type="submit">Guardar</button>
      </div>


      <div class="row g-2 mt-2">
        <div class="col-md-3">
          <button class="btn btn-success" type="button" onclick="generarPDF()">Imprimir</button>
        </div>
      </div>
    </form>

    <input type="text" id="buscador" class="form-control mb-3" placeholder="Buscar por nombre o rubro...">

    <table class="table table-bordered">
      <thead class="table-dark">
        <tr>
          <th>Nombre</th>
          <th>Dirección</th>
          <th>Teléfono</th>
          <th>Rubro</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody id="tablaClientes"></tbody>
    </table>
  </div>

  <script>
    let clientes = [];

    function cargarClientes() {
      fetch("../catalogo-api/listar_clientes.php")
        .then(res => res.json())
        .then(data => {
          clientes = data;
          renderClientes();
        });
    }

    function renderClientes(filtrados = null) {
      const lista = document.getElementById("tablaClientes");
      lista.innerHTML = "";
      (filtrados ?? clientes).forEach(p => {
        lista.innerHTML += `
          <tr>
            <td>${p.nombre}</td>
            <td>${p.direccion}</td>
            <td>${p.telefono}</td>
            <td>${p.rubro}</td>
            <td>
              <button class="btn btn-sm btn-warning" onclick="editar(${p.id})">Editar</button>
              <button class="btn btn-sm btn-danger" onclick="eliminar(${p.id})">Eliminar</button>
            </td>
          </tr>`;
      });
    }

    document.getElementById("buscador").addEventListener("keyup", function () {
      const texto = this.value.toLowerCase();
      const filtrados = clientes.filter(p =>
        p.nombre.toLowerCase().includes(texto) || p.rubro.toLowerCase().includes(texto)
      );
      renderClientes(filtrados);
    });

    document.getElementById("formCliente").addEventListener("submit", function (e) {
      e.preventDefault();
      const id = document.getElementById("idCliente").value;
      const cliente = {
        id,
        nombre: document.getElementById("nombre").value,
        direccion: document.getElementById("direccion").value,
        telefono: document.getElementById("telefono").value,
        rubro: document.getElementById("rubro").value
      };

      const url = id
        ? "../catalogo-api/editar_clientes.php"
        : "../catalogo-api/agregar_clientes.php";

      fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(cliente)
      })
        .then(res => res.json())
        .then(data => {
          alert(data.mensaje || data.error);
          this.reset();
          document.getElementById("idCliente").value = "";
          cargarClientes();
        });
    });

    function editar(id) {
      const p = clientes.find(p => p.id == id);
      if (!p) return;
      document.getElementById("idCliente").value = p.id;
      document.getElementById("nombre").value = p.nombre;
      document.getElementById("direccion").value = p.direccion;
      document.getElementById("telefono").value = p.telefono;
      document.getElementById("rubro").value = p.rubro;
    }

    function eliminar(id) {
      if (!confirm("¿Deseas eliminar este cliente?")) return;

      fetch("../catalogo-api/eliminar_cliente.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id })
      })
        .then(res => res.json())
        .then(data => {
          alert(data.mensaje || data.error);
          cargarClientes();
        });
    }


    function generarPDF() {
      if (!clientes.length) {
        alert("No hay clientes para imprimir.");
        return;
      }

      const { jsPDF } = window.jspdf;
      const doc = new jsPDF();

      doc.setFontSize(18);
      doc.text("Listado de Clientes", 14, 20);

      const filas = clientes.map(c => [
        c.nombre,
        c.direccion,
        c.telefono,
        c.rubro
      ]);

      doc.autoTable({
        head: [["Nombre", "Dirección", "Teléfono", "Rubro"]],
        body: filas,
        startY: 30
      });

      const blob = doc.output("blob");
      const url = URL.createObjectURL(blob);
      window.open(url);
    }


    cargarClientes();
  </script>
</body>

</html>