<?php
include_once('../catalogo-conexion/conexion.php');

$start = !empty($_GET['start']) ? $_GET['start'] : null;
$end   = !empty($_GET['end']) ? $_GET['end'] : null;
$metodo = isset($_GET['metodo_pago']) && $_GET['metodo_pago'] !== '' ? $_GET['metodo_pago'] : null;

$whereParts = []; $params = []; $types = '';
if ($start) { $whereParts[] = "v.fecha >= ?"; $params[] = $start . " 00:00:00"; $types .= 's'; }
if ($end)   { $whereParts[] = "v.fecha <= ?"; $params[] = $end . " 23:59:59"; $types .= 's'; }
if ($metodo){ $whereParts[] = "v.metodo_pago = ?"; $params[] = $metodo; $types .= 's'; }
$whereSql = count($whereParts) ? "WHERE " . implode(" AND ", $whereParts) : "";

// Totales
$stmt = $conexion->prepare("SELECT IFNULL(SUM(total),0) as t_v, IFNULL(SUM(iva),0) as t_i FROM ventas v $whereSql");
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$tot = $stmt->get_result()->fetch_assoc();

// Top Productos
$stmt2 = $conexion->prepare("SELECT p.nombre, SUM(dv.cantidad) as cant FROM detalle_ventas dv JOIN ventas v ON v.id = dv.id_venta JOIN productos p ON p.id = dv.id_producto $whereSql GROUP BY p.nombre ORDER BY cant DESC");
if ($types) $stmt2->bind_param($types, ...$params);
$stmt2->execute();
$prod = $stmt2->get_result();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=analisis_capibara.csv');
$out = fopen('php://output', 'w');
fputs($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 para Excel

fputcsv($out, ['Reporte de Análisis - Capibara Store']);
fputcsv($out, ['Total Ventas', number_format($tot['t_v'], 2, ',', '.')]);
fputcsv($out, ['IVA Recaudado', number_format($tot['t_i'], 2, ',', '.')]);
fputcsv($out, []);
fputcsv($out, ['Producto', 'Unidades Vendidas']);
while($r = $prod->fetch_assoc()) fputcsv($out, [$r['nombre'], $r['cant']]);
fclose($out);
exit;