// app.js - Gestión Capibara Store (Versión Final con Selección de Cliente)
const API_BASE = "../catalogo-api/";

const urlParams = new URLSearchParams(window.location.search);
const proveedorIdUrl = urlParams.get("id");
const proveedorNombreUrl = urlParams.get("proveedor");
const form = document.getElementById("formProducto");

document.addEventListener("DOMContentLoaded", () => {
    // 1. Configuración de Proveedores
    const tituloElement = document.getElementById("tituloProveedor");
    const selectProv = document.getElementById("id_proveedor");

    const idGuardado = localStorage.getItem("ultimoProveedorId");
    if (proveedorIdUrl) {
        selectProv.value = proveedorIdUrl;
        localStorage.setItem("ultimoProveedorId", proveedorIdUrl);
    } else if (idGuardado) {
        selectProv.value = idGuardado;
    }

    if (tituloElement) {
        const nombreParaMostrar = proveedorNombreUrl || (selectProv.options[selectProv.selectedIndex]?.text);
        tituloElement.textContent = selectProv.value 
            ? `Proveedor: ${nombreParaMostrar}` 
            : "Seleccione un proveedor para comenzar";
    }

    // 2. Configuración para el Panel de Análisis (Si existe en la página)
    if (document.getElementById('analisis-dashboard')) {
        const hoy = new Date();
        const y = hoy.getFullYear();
        const m = String(hoy.getMonth() + 1).padStart(2, '0');
        const d = String(hoy.getDate()).padStart(2, '0');
        document.getElementById('start').value = `${y}-${m}-01`;
        document.getElementById('end').value = `${y}-${m}-${d}`;
        if (typeof renderAll === 'function') renderAll();
    }

    document.getElementById("nombre")?.focus();
});

// 3. LÓGICA PARA GUARDAR PRODUCTOS
if (form) {
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const idProv = form.id_proveedor.value;
        if (!idProv) return alert("Error: Debe seleccionar un proveedor.");

        localStorage.setItem("ultimoProveedorId", idProv);

        const datos = {
            nombre: form.nombre.value,
            precio_costo: form.precio_costo.value,
            precio_venta: form.precio_venta.value,
            cantidad: form.cantidad.value,
            id_proveedor: idProv
        };

        const res = await fetch(API_BASE + "guardar_productos.php", {
            method: 'POST',
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
        });
        const resp = await res.json();
        if (resp && (resp.mensaje || resp.exito)) {
            alert(resp.mensaje || "Producto guardado con éxito");
            location.reload();
        } else {
            alert("Error: " + (resp?.error || "No se pudo guardar"));
        }
    });
}

// 4. LÓGICA PARA EL MODAL DE VENTA RÁPIDA
const modalVenta = document.getElementById('modalVentaRapida');

if (modalVenta) {
    // A. Cargar datos del producto al abrir el modal
    modalVenta.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; 
        
        const id = button.getAttribute('data-id');
        const nombre = button.getAttribute('data-nombre');
        const precio = parseFloat(button.getAttribute('data-precio'));
        const stock = parseInt(button.getAttribute('data-stock'));

        document.getElementById('v_id').value = id;
        document.getElementById('v_nombre').textContent = nombre;
        document.getElementById('v_precio').value = precio;
        document.getElementById('v_precio_text').textContent = `$${precio.toFixed(2)}`;
        document.getElementById('v_cant').max = stock;
        document.getElementById('v_cant').value = 1;
        
        // Resetear el selector de cliente a Consumidor Final
        if (document.getElementById('v_cliente')) {
            document.getElementById('v_cliente').value = "";
        }
        
        actualizarTotalVenta();
    });

    // B. Actualizar total al cambiar cantidad
    document.getElementById('v_cant').addEventListener('input', actualizarTotalVenta);

    function actualizarTotalVenta() {
        const cant = parseInt(document.getElementById('v_cant').value) || 0;
        const precio = parseFloat(document.getElementById('v_precio').value) || 0;
        document.getElementById('v_total').textContent = `$${(cant * precio).toFixed(2)}`;
    }

    // C. ENVIAR LA VENTA (ACTUALIZADO CON CLIENTE)
    document.getElementById('formVentaRapida').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById('btnConfirmarVenta');
        btn.disabled = true;
        btn.textContent = "Procesando...";

        const total = parseFloat(document.getElementById('v_precio').value) * parseInt(document.getElementById('v_cant').value);

        // Capturamos el cliente del selector (si es "" manda null)
        const idCliente = document.getElementById('v_cliente')?.value || null;

        const datosVenta = {
            id_cliente: idCliente, 
            metodo_pago: document.getElementById('v_pago').value,
            iva: (total * 0.21).toFixed(2),
            total: total.toFixed(2),
            items: [{
                producto_id: document.getElementById('v_id').value,
                cantidad: document.getElementById('v_cant').value,
                precio_unitario: document.getElementById('v_precio').value
            }]
        };

        try {
            const response = await fetch(API_BASE + 'agregar_venta.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosVenta)
            });

            if (!response.ok) {
                const errorTxt = await response.text();
                throw new Error(errorTxt);
            }

            const resultado = await response.json();
            alert("✅ " + resultado.mensaje);
            location.reload(); 

        } catch (error) {
            console.error("Error:", error);
            alert("Error al procesar la venta. Verifique los datos e intente nuevamente.");
        } finally {
            btn.disabled = false;
            btn.textContent = "Confirmar";
        }
    });
}

// 5. BUSCADOR DE PRODUCTOS EN TIEMPO REAL
document.getElementById('buscador')?.addEventListener('keyup', function() {
    const valor = this.value.toLowerCase();
    const filas = document.querySelectorAll('#productList tr');
    
    filas.forEach(fila => {
        const nombreProducto = fila.children[1].textContent.toLowerCase();
        fila.style.display = nombreProducto.includes(valor) ? '' : 'none';
    });
});

// 6. FUNCIONES DE EXPORTACIÓN PDF
window.generarPDFCarga = function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.setFontSize(18);
    doc.text("Reporte de Carga Diaria", 14, 20);
    doc.autoTable({ html: '#tablaProductos', startY: 25 });
    window.open(doc.output("bloburl"));
};

window.generarPDFStockCompleto = function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    doc.setFontSize(18);
    doc.text("Inventario General - Capibara Store", 14, 20);
    doc.autoTable({ html: '#productTable', startY: 25 });
    window.open(doc.output("bloburl"));
};