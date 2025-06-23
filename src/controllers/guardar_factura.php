<?php
// src/controllers/guardar_factura.php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!in_array($_SESSION['user_rol'], ['admin', 'contador'])) {
    die("Acceso denegado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $proveedor_id = $_POST['proveedor_id'];
    $numero_factura = trim($_POST['numero_factura']);
    $monto = $_POST['monto'];
    $fecha_emision = $_POST['fecha_emision'];
    $estado_pago = $_POST['estado_pago'];

    $sql = "INSERT INTO facturas_proveedor (proveedor_id, numero_factura, monto, fecha_emision, estado_pago) VALUES (?, ?, ?, ?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$proveedor_id, $numero_factura, $monto, $fecha_emision, $estado_pago]);
        header("Location: /facturas.php");
        exit();
    } catch (PDOException $e) {
        die("Error al guardar la factura: " . $e->getMessage());
    }
}
?>