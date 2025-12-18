const API_BASE = "../catalogo-api/";
const urlParams = new URLSearchParams(window.location.search);
const proveedorIdUrl = urlParams.get("id");
const proveedorNombreUrl = urlParams.get("proveedor");
const form = document.getElementById("formProducto");

document.addEventListener("DOMContentLoaded", () => {
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
        tituloElement.textContent = selectProv.value ? `Proveedor: ${nombreParaMostrar}` : "Seleccione un proveedor para comenzar";
    }
    document.getElementById("nombre")?.focus();
});

if (form) {
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const idProv = form.id_proveedor.value;
        if (!idProv) return alert("Error: Debe seleccionar un proveedor.");
        localStorage.setItem("ultimoProveedorId", idProv);

        const formData = new FormData(form);

        try {
            const res = await fetch(API_BASE + "guardar_productos.php", {
                method: 'POST',
                body: formData // Importante: Sin headers de Content-Type
            });
            const resp = await res.json();
            if (resp && (resp.mensaje || resp.exito)) {
                alert("✅ " + (resp.mensaje || "Operación exitosa"));
                location.reload();
            } else {
                alert("❌ Error: " + (resp?.error || "No se pudo procesar"));
            }
        } catch (error) {
            alert("Ocurrió un error al conectar con el servidor.");
        }
    });
}

document.addEventListener("click", (e) => {
    if (e.target.classList.contains("btn-editar-accion")) {
        const d = e.target.dataset;
        document.getElementById('nombre').value = d.nombre;
        document.getElementById('precio_costo').value = d.costo;
        document.getElementById('precio_venta').value = d.venta;
        document.getElementById('cantidad').value = d.stock;
        document.getElementById('id_proveedor').value = d.proveedor;

        const selectCat = document.getElementById('categoriaSelect');
        const inputNuevaCat = document.getElementById('inputNuevaCat');
        const existeCat = Array.from(selectCat.options).some(opt => opt.value === d.categoria);
        
        if (existeCat) {
            selectCat.value = d.categoria;
            inputNuevaCat.style.display = 'none';
        } else {
            selectCat.value = 'NUEVA';
            inputNuevaCat.style.display = 'block';
            inputNuevaCat.value = d.categoria;
        }

        let inputId = document.getElementById('id_editar');
        if (!inputId) {
            inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id_editar';
            inputId.id = 'id_editar';
            form.appendChild(inputId);
        }
        inputId.value = d.id;
        document.getElementById('btnSubmitForm').textContent = "Actualizar Producto #" + d.id;
        document.getElementById('btnSubmitForm').classList.replace('btn-primary', 'btn-warning');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

// Lógica de Venta Rápida y PDF (Se mantiene igual)
const modalVenta = document.getElementById('modalVentaRapida');
if (modalVenta) {
    modalVenta.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; 
        document.getElementById('v_id').value = button.getAttribute('data-id');
        document.getElementById('v_nombre').textContent = button.getAttribute('data-nombre');
        document.getElementById('v_precio').value = button.getAttribute('data-precio');
        document.getElementById('v_cant').max = button.getAttribute('data-stock');
        document.getElementById('v_cant').value = 1;
        actualizarTotalVenta();
    });
    document.getElementById('v_cant').addEventListener('input', actualizarTotalVenta);
}
function actualizarTotalVenta() {
    const cant = parseInt(document.getElementById('v_cant').value) || 0;
    const precio = parseFloat(document.getElementById('v_precio').value) || 0;
    document.getElementById('v_total').textContent = `$${(cant * precio).toFixed(2)}`;
}
document.getElementById('formVentaRapida')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const datosVenta = {
        id_cliente: document.getElementById('v_cliente').value || null,
        metodo_pago: document.getElementById('v_pago').value,
        iva: (parseFloat(document.getElementById('v_precio').value) * parseInt(document.getElementById('v_cant').value) * 0.21).toFixed(2),
        total: (parseFloat(document.getElementById('v_precio').value) * parseInt(document.getElementById('v_cant').value)).toFixed(2),
        items: [{
            producto_id: document.getElementById('v_id').value,
            cantidad: document.getElementById('v_cant').value,
            precio_unitario: document.getElementById('v_precio').value
        }]
    };
    const response = await fetch(API_BASE + 'agregar_venta.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosVenta)
    });
    const resultado = await response.json();
    alert(resultado.mensaje);
    location.reload();
});
document.getElementById('buscador')?.addEventListener('keyup', function() {
    const valor = this.value.toLowerCase();
    document.querySelectorAll('#productList tr').forEach(fila => {
        fila.style.display = fila.children[1].textContent.toLowerCase().includes(valor) ? '' : 'none';
    });
});
window.generarPDFCarga = function() {
    const doc = new jspdf.jsPDF();
    doc.text("Reporte de Carga Diaria", 14, 20);
    doc.autoTable({ html: '#tablaProductos', startY: 25 });
    window.open(doc.output("bloburl"));
};
window.generarPDFStockCompleto = function() {
    const doc = new jspdf.jsPDF();
    doc.text("Inventario General", 14, 20);
    doc.autoTable({ html: '#productTable', startY: 25 });
    window.open(doc.output("bloburl"));
};