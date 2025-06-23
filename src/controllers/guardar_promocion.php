<?php
// src/controllers/guardar_promocion.php -- VERSIÓN MEJORADA
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['user_rol'] != 'admin') { die("Acceso denegado."); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $tipo = $_POST['tipo'];
    $valor = $_POST['valor'];
    $producto_ids = $_POST['producto_ids'] ?? [];
    $cantidades = $_POST['cantidades'] ?? [];
    $negocio_id = 1;

    if (empty($nombre) || empty($tipo) || !is_numeric($valor) || empty($producto_ids) || count($producto_ids) !== count($cantidades)) {
        die("Datos inválidos. Asegúrese de añadir al menos un producto al pack.");
    }

    $pdo->beginTransaction();
    try {
        $sqlPromo = "INSERT INTO promociones (negocio_id, nombre, tipo, valor, estado) VALUES (?, ?, ?, ?, 'activa')";
        $stmtPromo = $pdo->prepare($sqlPromo);
        $stmtPromo->execute([$negocio_id, $nombre, $tipo, $valor]);
        $promocion_id = $pdo->lastInsertId();

        $sqlProductos = "INSERT INTO promocion_productos (promocion_id, producto_id, cantidad) VALUES (?, ?, ?)";
        $stmtProductos = $pdo->prepare($sqlProductos);
        
        // Recorremos los productos y sus cantidades para guardarlos
        foreach ($producto_ids as $key => $producto_id) {
            $cantidad = $cantidades[$key];
            $stmtProductos->execute([$promocion_id, $producto_id, $cantidad]);
        }

        $pdo->commit();
        header("Location: /promociones.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error al guardar la promoción: " . $e->getMessage());
    }
}
?>