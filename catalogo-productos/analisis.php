<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['admin'] != 1) {
    header("Location: inicio.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" href="/img/Proyecto_nuevo.ico">
    <meta charset="utf-8">
    <title>Análisis de Negocio | Capibara Store</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link rel="stylesheet" href="style2.css">
    <style>
        :root { --azul-capibara: #0B173D; --dorado-capibara: #B78F0D; }
        body { padding-top: 80px; background-color: #f4f7f9; }
        .card { border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 12px; }
        .chart-container { position: relative; height: 320px; width: 100%; }
        .card-resumen h6 { font-weight: 600; color: #6c757d; text-transform: uppercase; font-size: 0.8rem; }
        .card-resumen h3 { font-weight: 700; color: var(--azul-capibara); }
        .filters { gap: 15px; background: white; padding: 20px; border-radius: 12px; margin-bottom: 25px; }
        .btn-primary { background-color: var(--azul-capibara); border: none; }
        .btn-primary:hover { background-color: #1a2a5a; }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    
    <div id="analisis-dashboard" class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Panel de Análisis 📊</h2>
                <p class="text-muted">Rendimiento mensual de la tienda</p>
            </div>
            <div>
                <button id="btnExportCSV" class="btn btn-outline-success btn-sm">Exportar Datos (.CSV)</button>
                <button id="btnExportPDF" class="btn btn-outline-primary btn-sm">Descargar Reporte (.PDF)</button>
            </div>
        </div>

        <div class="card filters shadow-sm">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-3">
                <form id="formFiltros" class="row g-3 align-items-end flex-grow-1">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Desde</label>
                        <input type="date" id="start" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Hasta</label>
                        <input type="date" id="end" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">Método de Pago</label>
                        <select id="metodo_pago" class="form-select">
                            <option value="">Todos los métodos</option>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Débito">Débito</option>
                            <option value="Crédito">Crédito</option>
                            <option value="Transferencia">Transferencia</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-grid">
                        <button type="button" id="btnAplicar" class="btn btn-primary">Aplicar Filtros</button>
                    </div>
                </form>
                <div class="btn-group mb-1">
                    <button class="btn btn-outline-secondary btn-sm" onclick="setRango('hoy')">Hoy</button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="setRango('mes')">30 Días</button>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="card p-4 text-center card-resumen"><h6>Ventas Totales</h6><h3 id="totalVentas">$0.00</h3></div></div>
            <div class="col-md-3"><div class="card p-4 text-center card-resumen"><h6>Ganancia Est.</h6><h3 id="ganancia" style="color: #28a745;">$0.00</h3></div></div>
            <div class="col-md-3"><div class="card p-4 text-center card-resumen"><h6>IVA Total</h6><h3 id="totalIVA">$0.00</h3></div></div>
            <div class="col-md-3"><div class="card p-4 text-center card-resumen"><h6>Ticket Prom.</h6><h3 id="ticketPromedio" style="color: var(--dorado-capibara);">$0.00</h3></div></div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-lg-7"><div class="card p-4"><h6>Histórico de Ventas</h6><div class="chart-container"><canvas id="chartMeses"></canvas></div></div></div>
            <div class="col-lg-5"><div class="card p-4"><h6>Top 10 Productos</h6><div class="chart-container"><canvas id="chartTop"></canvas></div></div></div>
            <div class="col-12"><div class="card p-4"><h6>Frecuencia Diaria de Operaciones</h6><div class="chart-container"><canvas id="chartDias"></canvas></div></div></div>
        </div>

        <div class="card p-4">
            <h6>Rendimiento por Método de Pago</h6>
            <div id="tablaMetodos" class="table-responsive"></div>
        </div>
    </div>

    <script>
        const colorAzul = '#0B173D', colorDorado = '#B78F0D';
        function money(v) { return '$' + Number(v || 0).toLocaleString('es-AR', {minimumFractionDigits: 2}); }
        let charts = {};

        // FECHA PREDETERMINADA: DIA 1 HASTA HOY
        function configurarFechas() {
            const hoy = new Date();
            const y = hoy.getFullYear();
            const m = String(hoy.getMonth() + 1).padStart(2, '0');
            const d = String(hoy.getDate()).padStart(2, '0');
            document.getElementById('start').value = `${y}-${m}-01`;
            document.getElementById('end').value = `${y}-${m}-${d}`;
        }

        function setRango(t) {
            const hoy = new Date().toISOString().split('T')[0];
            const inicio = new Date();
            if(t === 'mes') inicio.setDate(inicio.getDate() - 30);
            document.getElementById('end').value = hoy;
            document.getElementById('start').value = (t === 'hoy') ? hoy : inicio.toISOString().split('T')[0];
            renderAll();
        }

        async function renderAll() {
            const params = new URLSearchParams({
                start: document.getElementById('start').value,
                end: document.getElementById('end').value,
                metodo_pago: document.getElementById('metodo_pago').value
            });
            const res = await fetch('../catalogo-api/analisis_datos.php?' + params);
            if (!res.ok) return;
            const data = await res.json();
            
            document.getElementById('totalVentas').textContent = money(data.totales.total_ventas);
            document.getElementById('ganancia').textContent = money(data.totales.ganancia);
            document.getElementById('totalIVA').textContent = money(data.totales.total_iva);
            document.getElementById('ticketPromedio').textContent = money(data.totales.ticket_promedio);

            dibujar('chartMeses', 'line', data.meses, data.ventas_mes, colorAzul, true);
            dibujar('chartTop', 'bar', data.top.map(x => x.nombre), data.top.map(x => x.total_vendido), colorDorado, false, 'y');
            dibujar('chartDias', 'line', data.dias.map(x => x.dia), data.dias.map(x => x.total_dia), '#4bc0c0');

            let html = `<table class="table align-middle mt-3"><thead><tr><th>Método</th><th>Monto</th><th>Participación</th></tr></thead><tbody>`;
            const total = data.metodos.reduce((s, m) => s + parseFloat(m.total_metodo), 0);
            data.metodos.forEach(m => {
                const p = ((m.total_metodo / (total || 1)) * 100).toFixed(1);
                html += `<tr><td><strong>${m.metodo_pago}</strong></td><td>${money(m.total_metodo)}</td>
                         <td><div class="progress" style="height: 8px;"><div class="progress-bar" style="width: ${p}%; background: ${colorAzul}"></div></div> ${p}%</td></tr>`;
            });
            document.getElementById('tablaMetodos').innerHTML = html + '</tbody></table>';
        }

        function dibujar(id, type, labels, data, color, fill = false, axis = 'x') {
            if (charts[id]) charts[id].destroy();
            charts[id] = new Chart(document.getElementById(id), {
                type: type,
                data: { labels, datasets: [{ data, borderColor: color, backgroundColor: color + '22', fill, tension: 0.4, pointRadius: 4 }] },
                options: { indexAxis: axis, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }

        // VINCULACIÓN CSV
        document.getElementById('btnExportCSV').addEventListener('click', () => {
            const q = new URLSearchParams({
                start: document.getElementById('start').value,
                end: document.getElementById('end').value,
                metodo_pago: document.getElementById('metodo_pago').value
            });
            window.location.href = '../catalogo-api/exportar_analisis_csv.php?' + q.toString();
        });

        document.getElementById('btnExportPDF').addEventListener('click', function() {
            const btn = this;
            btn.innerHTML = "Generando...";
            html2canvas(document.querySelector('#analisis-dashboard'), { scale: 2 }).then(canvas => {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');
                const imgData = canvas.toDataURL('image/png');
                const pdfWidth = pdf.internal.pageSize.getWidth();
                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, (canvas.height * pdfWidth) / canvas.width);
                pdf.save(`Reporte_Capibara_${new Date().toISOString().slice(0,10)}.pdf`);
                btn.innerHTML = "Descargar Reporte (.PDF)";
            });
        });

        document.getElementById('btnAplicar').addEventListener('click', renderAll);
        document.addEventListener('DOMContentLoaded', () => {
            configurarFechas();
            renderAll();
        });
    </script>
</body>
</html>