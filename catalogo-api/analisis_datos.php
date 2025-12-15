<?php
header("Content-Type: application/json; charset=utf-8");

// 1. Incluir y verificar la conexión
include_once('../catalogo-conexion/conexion.php'); 

if (!$conexion || $conexion->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos: ' . $conexion->connect_error]);
    exit;
}

// Recibir filtros (GET)
$start = isset($_GET['start']) && $_GET['start'] !== '' ? $_GET['start'] : null;
$end   = isset($_GET['end']) && $_GET['end'] !== '' ? $_GET['end'] : null;
$metodo = isset($_GET['metodo_pago']) && $_GET['metodo_pago'] !== '' ? $_GET['metodo_pago'] : null;

// Helper: crear fragmento WHERE según filtros
$whereParts = [];
$params = [];
$types = '';

if ($start) {
    $whereParts[] = "v.fecha >= ?";
    $params[] = $start . " 00:00:00";
    $types .= 's';
}
if ($end) {
    $whereParts[] = "v.fecha <= ?";
    $params[] = $end . " 23:59:59";
    $types .= 's';
}
if ($metodo) {
    $whereParts[] = "v.metodo_pago = ?";
    $params[] = $metodo;
    $types .= 's';
}
$whereSql = count($whereParts) ? "WHERE " . implode(" AND ", $whereParts) : "";

// Función para ejecutar consultas preparadas con los parámetros dinámicos
function execute_query($conexion, $sql, $types, $params) {
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conexion->error);
    }
    // Solo bindeamos parámetros si existen
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

$response = [];

try {
    // 1) Totales (ventas, iva) - No requiere JOIN
    $sqlTot = "SELECT IFNULL(SUM(v.total),0) AS total_ventas, IFNULL(SUM(v.iva),0) AS total_iva
                FROM ventas v
                $whereSql";
    $resTot = execute_query($conexion, $sqlTot, $types, $params)->fetch_assoc();

    // 2) Ganancia estimada (USANDO id_venta y id_producto)
    $sqlGan = "SELECT IFNULL(SUM(dv.cantidad * (dv.precio_unitario - IFNULL(p.precio_costo, 0))), 0) AS ganancia
               FROM detalle_ventas dv
               LEFT JOIN ventas v ON v.id = dv.id_venta      
               LEFT JOIN productos p ON p.id = dv.id_producto 
               $whereSql";
    $resGan = execute_query($conexion, $sqlGan, $types, $params)->fetch_assoc();

    $response['totales'] = [
        'total_ventas' => floatval($resTot['total_ventas']),
        'total_iva'    => floatval($resTot['total_iva']),
        'ganancia'     => floatval($resGan['ganancia'])
    ];

    // 3) Top productos (USANDO id_venta y id_producto)
    $sqlTop = "SELECT p.nombre AS nombre, SUM(dv.cantidad) AS total_vendido
               FROM detalle_ventas dv
               LEFT JOIN ventas v ON v.id = dv.id_venta   
               LEFT JOIN productos p ON p.id = dv.id_producto 
               $whereSql
               GROUP BY p.nombre
               ORDER BY total_vendido DESC
               LIMIT 10";
    $response['top'] = execute_query($conexion, $sqlTop, $types, $params)->fetch_all(MYSQLI_ASSOC);
    
    // 4) Ventas por mes (últimos 12 meses o rango filtrado)
    $months = [];
    $dataMonths = [];
    
    $sqlMonths = "SELECT DATE_FORMAT(v.fecha, '%Y-%m') AS mes, SUM(v.total) AS total_mes
                  FROM ventas v
                  $whereSql
                  GROUP BY mes
                  ORDER BY mes ASC";

    $resMonths = execute_query($conexion, $sqlMonths, $types, $params)->fetch_all(MYSQLI_ASSOC);

    foreach ($resMonths as $r) {
        $months[] = $r['mes'];
        $dataMonths[] = floatval($r['total_mes']);
    }
    
    $response['meses'] = $months;
    $response['ventas_mes'] = $dataMonths;

    // 5) Ventas por día (ajustado para respetar el filtro de fecha)
    $sqlDays = "SELECT DATE(v.fecha) AS dia, SUM(v.total) AS total_dia
                FROM ventas v
                " . (!$start && !$end ? "WHERE DATE(v.fecha) >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)" : $whereSql) . "
                GROUP BY DATE(v.fecha)
                ORDER BY DATE(v.fecha) ASC";

    if (!$start && !$end) {
        $resDays = $conexion->query($sqlDays);
        $response['dias'] = $resDays ? $resDays->fetch_all(MYSQLI_ASSOC) : [];
    } else {
        $response['dias'] = execute_query($conexion, $sqlDays, $types, $params)->fetch_all(MYSQLI_ASSOC);
    }
    
    // 6) Métodos de pago
    $sqlMet = "SELECT v.metodo_pago, COUNT(*) as cantidad, SUM(v.total) as total_metodo
               FROM ventas v
               $whereSql
               GROUP BY v.metodo_pago
               ORDER BY total_metodo DESC";
    $response['metodos'] = execute_query($conexion, $sqlMet, $types, $params)->fetch_all(MYSQLI_ASSOC);
    
    // 7) Movimientos stock
    $sqlMov = "SELECT tipo, COUNT(*) AS cantidad, SUM(cantidad) as suma_cantidad FROM movimientos_stock GROUP BY tipo";
    $resMov = $conexion->query($sqlMov);
    $response['movimientos'] = $resMov ? $resMov->fetch_all(MYSQLI_ASSOC) : [];
    
    // Devolvemos el JSON final
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

$conexion->close();
?>