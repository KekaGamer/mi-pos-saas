<?php
// src/controllers/guardar_producto.php -- VERSIÓN CON CATEGORÍA
session_start();
require_once __DIR__ . '/../../config/database.php';
if ($_SESSION['user_rol'] != 'admin') { die("Acceso denegado."); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre_producto']);
    $categoria_id = !empty($_POST['categoria_id']) ? $_POST['categoria_id'] : null;
    $codigo_barra = trim($_POST['codigo_barra']);
    $precio_costo = $_POST['precio_costo'];
    $precio_venta = $_POST['precio_venta'];
    $stock = $_POST['stock'];
    $stock_minimo = $_POST['stock_minimo'];
    $impuesto_adicional = !empty($_POST['impuesto_adicional']) ? $_POST['impuesto_adicional'] : null;
    $negocio_id = 1;

    $sql = "INSERT INTO productos (negocio_id, nombre, categoria_id, codigo_barra, precio_costo, precio_venta, stock, stock_minimo, impuesto_adicional) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$negocio_id, $nombre, $categoria_id, $codigo_barra, $precio_costo, $precio_venta, $stock, $stock_minimo, $impuesto_adicional]);
        header("Location: /index.php?exito_creacion=1");
        exit();
    } catch (PDOException $e) { die("Error al guardar el producto: " . $e->getMessage()); }
}
?>