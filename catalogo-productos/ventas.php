<?php
session_start();
$usuarioLogueado = isset($_SESSION['usuario']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <link rel="icon" href="/img/Proyecto_nuevo.ico">
  <meta charset="utf-8">
  <title>Ventas - Capibara Store</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style2.css">
  <style>
    .item-row { gap: 8px; }
    .card-small { padding: 12px; border-radius: 10px; }
    .table-fixed { table-layout: fixed; word-wrap: break-word; }
  </style>
</head>
<body>
  <?php include('header.php'); ?>
  <div class="container py-4">
    <h1 class="text-center mb-4">Registrar Venta</h1>

    <?php if (!$usuarioLogueado): ?>
      <div class="alert alert-warning">
        Debes iniciar sesión para registrar ventas. <a href="inicio.php">Volver al inicio</a>
      </div>
      <?php exit; ?>
    <?php endif; ?>

    <div class="row mb-4">
      <div class="col-lg-6">
        <div class="card card-small p-3">
          <h5>Crear nueva venta</h5>

          <div class="mb-2">
            <label class="form-label">Cliente (opcional)</label>
            <select id="selectCliente" class="form-select">
              <option value="">-- Cliente eventual --</option>
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label">Método de pago</label>
            <select id="metodoPago" class="form-select">
              <option value="Efectivo">Efectivo</option>
              <option value="Débito">Débito</option>
              <option value="Crédito">Crédito</option>
              <option value="Transferencia">Transferencia</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Agregar ítem</label>
            <div class="d-flex item-row">
              <input id="itemBuscar" class="form-control" placeholder="Buscar producto por nombre">
              <input id="itemCantidad" class="form-control" type="number" min="1" value="1" style="width:110px;">
              <input id="itemPrecio" class="form-control" type="number" min="0" step="0.01" placeholder="Precio" style="width:140px;">
              <button id="btnAddItem" class="btn btn-success">Agregar</button>
            </div>
            <div class="form-text">Puedes escribir el nombre de un producto que exista para autocompletar o ingresar uno nuevo.</div>
          </div>

          <div class="mb-2">
            <table class="table table-sm table-bordered table-fixed" id="tablaItems">
              <thead class="table-dark">
                <tr>
                  <th>Producto</th><th>Cantidad</th><th>Precio Unit.</th><th>Subtotal</th><th>Acción</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <div class="d-flex justify-content-between align-items-center">
            <div>
              <label class="form-label">IVA ($)</label>
              <input id="inputIVA" class="form-control" type="number" min="0" step="0.01" value="0" style="width:120px;">
            </div>
            <div>
              <label class="form-label">Total</label>
              <div id="spanTotal" class="h5">$0.00</div>
            </div>
          </div>

          <div class="mt-3 d-grid">
            <button id="btnGuardarVenta" class="btn btn-primary">Registrar Venta</button>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card card-small p-3">
          <h5>Historial reciente (ventas)</h5>
          <div id="listaVentas" style="max-height:520px; overflow:auto;"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script>
    let items = [];
    let productosCache = [];

    function formatMoney(v) {
      return '$' + Number(v).toFixed(2);
    }

    // Cargar clientes para el select
    async function cargarClientes() {
      try {
        const res = await fetch('../catalogo-api/listar_clientes.php');
        const datos = await res.json();
        const sel = document.getElementById('selectCliente');
        sel.innerHTML = '<option value="">-- Cliente eventual --</option>';
        datos.forEach(p => {
          const o = document.createElement('option');
          o.value = p.id;
          o.textContent = p.nombre;
          sel.appendChild(o);
        });
      } catch (err) {
        console.error('Error cargando clientes', err);
      }
    }

    // Cargar productos para autocompletar búsqueda
    async function cargarProductos() {
      try {
        const res = await fetch('../catalogo-api/listar.php');
        productosCache = await res.json();
      } catch (err) {
        console.error('Error cargando productos', err);
      }
    }

    function renderItems() {
      const tbody = document.querySelector('#tablaItems tbody');
      tbody.innerHTML = '';
      let total = 0;
      items.forEach((it, idx) => {
        const subtotal = it.cantidad * it.precio_unitario;
        total += subtotal;
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${it.nombre_producto}</td>
          <td style="width:80px;">${it.cantidad}</td>
          <td style="width:120px;">${formatMoney(it.precio_unitario)}</td>
          <td style="width:120px;">${formatMoney(subtotal)}</td>
          <td style="width:120px;"><button class="btn btn-sm btn-danger" data-idx="${idx}">Eliminar</button></td>
        `;
        tbody.appendChild(tr);
      });

      const iva = parseFloat(document.getElementById('inputIVA').value || 0);
      const totalConIva = total + iva;
      document.getElementById('spanTotal').textContent = formatMoney(totalConIva);
    }

    // Agregar item
    document.getElementById('btnAddItem').addEventListener('click', function (e) {
      e.preventDefault();
      const nombre = document.getElementById('itemBuscar').value.trim();
      const cantidad = parseInt(document.getElementById('itemCantidad').value) || 0;
      const precio = parseFloat(document.getElementById('itemPrecio').value) || 0;

      if (!nombre || cantidad <= 0 || precio < 0) { alert('Completa el item correctamente'); return; }

      // buscar producto en cache por nombre (exacto o parcial)
      let productoEncontrado = productosCache.find(p => p.nombre.toLowerCase() === nombre.toLowerCase());
      let producto_id = productoEncontrado ? productoEncontrado.id : null;

      items.push({ producto_id: producto_id, nombre_producto: nombre, cantidad: cantidad, precio_unitario: precio });
      document.getElementById('itemBuscar').value = '';
      document.getElementById('itemCantidad').value = 1;
      document.getElementById('itemPrecio').value = '';
      renderItems();
    });

    // eliminar item
    document.querySelector('#tablaItems tbody').addEventListener('click', function (e) {
      if (e.target.matches('button')) {
        const idx = parseInt(e.target.getAttribute('data-idx'));
        items.splice(idx, 1);
        renderItems();
      }
    });

    document.getElementById('inputIVA').addEventListener('input', renderItems);

    // Guardar venta
    document.getElementById('btnGuardarVenta').addEventListener('click', async function () {
      if (!items.length) { alert('Agregá al menos un item'); return; }
      const cliente_id = document.getElementById('selectCliente').value || null;
      const metodo_pago = document.getElementById('metodoPago').value;
      const iva = parseFloat(document.getElementById('inputIVA').value || 0);

      let subtotal = 0;
      items.forEach(it => subtotal += it.cantidad * it.precio_unitario);
      const total = subtotal + iva;

      const payload = { cliente_id: cliente_id ? Number(cliente_id) : null, metodo_pago, iva, total, items };

      try {
        const res = await fetch('../catalogo-api/agregar_venta.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (res.ok) {
          alert('Venta registrada con ID: ' + (data.venta_id || 'OK'));
          items = [];
          renderItems();
          document.getElementById('inputIVA').value = '0';
          cargarVentas();
          cargarProductos(); // refrescar cache
        } else {
          alert('Error: ' + (data.error || 'Error desconocido'));
        }
      } catch (err) {
        alert('Error al guardar venta: ' + err);
      }
    });

    // Cargar ventas recientes
    async function cargarVentas() {
      const cont = document.getElementById('listaVentas');
      cont.innerHTML = 'Cargando...';
      try {
        const res = await fetch('../catalogo-api/listar_ventas.php');
        const datos = await res.json();
        if (!Array.isArray(datos)) {
          cont.innerHTML = '<div class="text-danger">Error cargando ventas</div>';
          return;
        }
        if (datos.length === 0) {
          cont.innerHTML = '<div class="text-muted">No hay ventas registradas</div>';
          return;
        }
        cont.innerHTML = '';
        datos.forEach(c => {
          const card = document.createElement('div');
          card.className = 'border rounded p-2 mb-2';
          let detallesHtml = '<ul class="mb-0">';
          (c.detalles || []).forEach(d => {
            detallesHtml += `<li>${d.nombre_producto} — ${d.cantidad} x ${formatMoney(d.precio_unitario)} = ${formatMoney(d.subtotal)}</li>`;
          });
          detallesHtml += '</ul>';
          card.innerHTML = `
            <div class="d-flex justify-content-between">
              <div><strong>#${c.id}</strong> — ${c.cliente_nombre ?? 'Cliente eventual'}</div>
              <div class="text-end"><small>${c.fecha}</small></div>
            </div>
            <div class="small text-muted">${c.metodo_pago ?? ''} — ${c.usuario ?? ''}</div>
            <div class="mt-2">${detallesHtml}</div>
            <div class="mt-2"><strong>Total: ${formatMoney(c.total)} (IVA: ${formatMoney(c.iva)})</strong></div>
          `;
          cont.appendChild(card);
        });
      } catch (err) {
        cont.innerHTML = '<div class="text-danger">Error al consultar ventas</div>';
      }
    }

    (function init() {
      cargarClientes();
      cargarProductos();
      cargarVentas();
      renderItems();
    })();
  </script>
</body>
</html>
