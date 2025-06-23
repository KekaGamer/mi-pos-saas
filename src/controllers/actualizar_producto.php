<?php
// src/controllers/actualizar_producto.php -- VERSIÓN FINAL
session_start();
require_once __DIR__ . '/../../config/database.php';
if ($_SESSION['user_rol'] != 'admin') { die("Acceso denegado."); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = trim($_POST['nombre_producto']);
    $codigo_barra = trim($_POST['codigo_barra']);
    $precio_costo = $_POST['precio_costo'];
    $precio_venta = $_POST['precio_venta'];
    $stock_nuevo = $_POST['stock'];
    $stock_minimo = $_POST['stock_minimo'];
    $impuesto_adicional = !empty($_POST['impuesto_adicional']) ? $_POST['impuesto_adicional'] : null;
    $motivo_ajuste = trim($_POST['motivo_ajuste']);
    $usuario_id = $_SESSION['user_id'];

    $pdo->beginTransaction();
    try {
        $stmt_stock_anterior = $pdo->prepare("SELECT stock FROM productos WHERE id = ?");
        $stmt_stock_anterior->execute([$id]);
        $stock_anterior = $stmt_stock_anterior->fetchColumn();

        $sql_update = "UPDATE productos SET nombre = ?, codigo_barra = ?, precio_costo = ?, precio_venta = ?, stock = ?, stock_minimo = ?, impuesto_adicional = ? WHERE id = ?";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([$nombre, $codigo_barra, $precio_costo, $precio_venta, $stock_nuevo, $stock_minimo, $impuesto_adicional, $id]);

        if ($stock_nuevo != $stock_anterior) {
            $cantidad_movimiento = $stock_nuevo - $stock_anterior;
            $motivo = empty($motivo_ajuste) ? "Ajuste manual sin motivo." : $motivo_ajuste;
            $sql_log = "INSERT INTO inventario_log (producto_id, usuario_id, cantidad_anterior, cantidad_movimiento, cantidad_nueva, tipo_movimiento, motivo) VALUES (?, ?, ?, ?, ?, 'ajuste_manual', ?)";
            $stmt_log = $pdo->prepare($sql_log);
            $stmt_log->execute([$id, $usuario_id, $stock_anterior, $cantidad_movimiento, $stock_nuevo, $motivo]);
        }
        
        $pdo->commit();
        header("Location: /index.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error al actualizar el producto: " . $e->getMessage());
    }
}
?>