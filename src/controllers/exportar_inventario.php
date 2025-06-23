<?php
// src/controllers/exportar_inventario.php -- VERSIÓN CORREGIDA Y ESTANDARIZADA
session_start();
require_once __DIR__ . '/../../config/database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    die("Acceso denegado.");
}

$filename = "plantilla_inventario_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);
$output = fopen('php://output', 'w');

// Encabezados en el orden correcto que espera la importación
fputcsv($output, [
    'Nombre', 
    'Categoria', 
    'Codigo de Barra', 
    'Precio Costo', 
    'Precio Venta', 
    'Stock', 
    'Stock Minimo',
    'Impuesto Adicional'
]);

try {
    // Usamos un LEFT JOIN para obtener el nombre de la categoría
    $sql = "SELECT p.nombre, c.nombre as nombre_categoria, p.codigo_barra, p.precio_costo, p.precio_venta, p.stock, p.stock_minimo, p.impuesto_adicional
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.negocio_id = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
} catch (PDOException $e) { die("Error al generar el CSV: " . $e->getMessage()); }

fclose($output);
exit();
?>