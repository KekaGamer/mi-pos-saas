<?php
// api/api_ventas.php
session_start();
header('Content-Type: application/json'); // Indicamos que la respuesta será en formato JSON
require_once __DIR__ . '/../config/database.php';

// Seguridad: verificar que el usuario tenga permiso
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_rol'], ['admin', 'contador'])) {
    echo json_encode(['error' => 'Acceso denegado']);
    exit();
}

$periodo = $_GET['periodo'] ?? 'day'; // Por defecto, 'day'
$negocio_id = 1; // Simulado

$sql = "";
$label_format = "";

// Construimos la consulta SQL y el formato de la etiqueta según el período solicitado
switch ($periodo) {
    case 'week':
        $sql = "SELECT DATE_FORMAT(fecha_hora, '%Y-%m-%d') as label, SUM(total) as total_ventas 
                FROM ventas 
                WHERE negocio_id = ? AND fecha_hora >= CURDATE() - INTERVAL 6 DAY 
                GROUP BY label ORDER BY label ASC";
        break;
    case 'month':
        $sql = "SELECT DATE_FORMAT(fecha_hora, '%Y-%m-%d') as label, SUM(total) as total_ventas 
                FROM ventas 
                WHERE negocio_id = ? AND MONTH(fecha_hora) = MONTH(CURDATE()) AND YEAR(fecha_hora) = YEAR(CURDATE())
                GROUP BY label ORDER BY label ASC";
        break;
    case 'year':
        $sql = "SELECT DATE_FORMAT(fecha_hora, '%Y-%m') as label, SUM(total) as total_ventas 
                FROM ventas 
                WHERE negocio_id = ? AND YEAR(fecha_hora) = YEAR(CURDATE())
                GROUP BY label ORDER BY label ASC";
        break;
    case 'day':
    default:
        $sql = "SELECT HOUR(fecha_hora) as label, SUM(total) as total_ventas 
                FROM ventas 
                WHERE negocio_id = ? AND DATE(fecha_hora) = CURDATE()
                GROUP BY label ORDER BY label ASC";
        break;
}

try {
    // Datos para el gráfico
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$negocio_id]);
    $results = $stmt->fetchAll();

    $labels = [];
    $data = [];
    foreach ($results as $row) {
        // Formateamos la etiqueta para que sea más legible en el caso de la hora
        $labels[] = ($periodo == 'day') ? $row['label'] . ':00' : $row['label'];
        $data[] = $row['total_ventas'];
    }

    // Datos para las tarjetas (KPIs) - Siempre se calculan para el día de HOY
    $sqlKpi = "SELECT SUM(total) as totalVentas, COUNT(*) as numTransacciones FROM ventas WHERE negocio_id = ? AND DATE(fecha_hora) = CURDATE()";
    $stmtKpi = $pdo->prepare($sqlKpi);
    $stmtKpi->execute([$negocio_id]);
    $kpiResult = $stmtKpi->fetch();

    $totalVentasHoy = $kpiResult['totalVentas'] ?? 0;
    $numTransaccionesHoy = $kpiResult['numTransacciones'] ?? 0;
    $ticketPromedio = ($numTransaccionesHoy > 0) ? $totalVentasHoy / $numTransaccionesHoy : 0;

    // Preparamos la respuesta final en formato JSON
    $response = [
        'chart' => [
            'labels' => $labels,
            'data' => $data
        ],
        'kpi' => [
            'totalVentas' => round($totalVentasHoy),
            'numTransacciones' => $numTransaccionesHoy,
            'ticketPromedio' => round($ticketPromedio)
        ]
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>