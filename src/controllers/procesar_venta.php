<?php
// src/controllers/procesar_venta.php -- VERSIÓN DEFINITIVA CON LOG DE INVENTARIO CORREGIDO
session_start();
require_once __DIR__ . '/../../config/database.php';

// Seguridad y verificación de sesión de caja
if (!isset($_SESSION['user_id']) || !isset($_SESSION['caja_sesion_id'])) { 
    die("Acceso denegado o no hay una sesión de caja activa."); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $carrito = json_decode($_POST['carrito_data'], true);
    $total_venta_final = $_POST['total_venta'];
    $metodo_pago = $_POST['metodo_pago'];
    $negocio_id = 1;
    $cajero_id = $_SESSION['user_id'];
    $caja_sesion_id = $_SESSION['caja_sesion_id'];

    if (empty($carrito)) { 
        die("El carrito está vacío."); 
    }

    // Obtenemos la configuración de IVA del negocio
    $stmtConfig = $pdo->prepare("SELECT iva_porcentaje FROM negocios WHERE id = ?");
    $stmtConfig->execute([$negocio_id]);
    $iva_tasa = $stmtConfig->fetchColumn() / 100;

    // Calculamos el desglose de impuestos
    $impuestos_adicionales_calculados = 0;
    if ($metodo_pago === 'debito' || $metodo_pago === 'credito') {
        foreach ($carrito as $item) {
            if ($item['tipo'] === 'producto' && isset($item['impuesto_adicional']) && $item['impuesto_adicional'] > 0) {
                $impuestos_adicionales_calculados += $item['impuesto_adicional'] * $item['cantidad'];
            }
        }
    }
    $subtotal_base = $total_venta_final - $impuestos_adicionales_calculados;
    $neto = $subtotal_base / (1 + $iva_tasa);
    $iva = $subtotal_base - $neto;

    // Iniciamos la transacción para asegurar la integridad de los datos
    $pdo->beginTransaction();
    try {
        // 1. Insertar la venta en la tabla 'ventas'
        $sqlVenta = "INSERT INTO ventas (negocio_id, caja_sesion_id, cajero_id, total, neto, impuestos, impuestos_adicionales, metodo_pago) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtVenta = $pdo->prepare($sqlVenta);
        $stmtVenta->execute([$negocio_id, $caja_sesion_id, $cajero_id, $total_venta_final, $neto, $iva, $impuestos_adicionales_calculados, $metodo_pago]);
        $venta_id = $pdo->lastInsertId();

        // Preparamos las consultas que usaremos repetidamente
        $stmtProductoInfo = $pdo->prepare("SELECT stock, precio_venta FROM productos WHERE id = ?");
        $stmtLog = $pdo->prepare("INSERT INTO inventario_log (producto_id, usuario_id, cantidad_anterior, cantidad_movimiento, cantidad_nueva, tipo_movimiento, motivo) VALUES (?, ?, ?, ?, ?, 'venta', ?)");
        $stmtStock = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
        $stmtDetalle = $pdo->prepare("INSERT INTO venta_detalles (venta_id, producto_id, cantidad, precio_unitario_venta, subtotal) VALUES (?, ?, ?, ?, ?)");

        // 2. Recorremos cada item del carrito
        foreach ($carrito as $item) {
            if ($item['tipo'] === 'producto') {
                // --- Lógica para productos individuales ---
                $cantidad_vendida = $item['cantidad'];
                
                // Obtenemos el stock actual para el log
                $stmtProductoInfo->execute([$item['id']]);
                $stock_anterior = $stmtProductoInfo->fetchColumn();
                $stock_nuevo = $stock_anterior - $cantidad_vendida;

                // Insertamos en el log
                $stmtLog->execute([$item['id'], $cajero_id, $stock_anterior, -$cantidad_vendida, $stock_nuevo, "Venta #" . $venta_id]);
                // Actualizamos el stock
                $stmtStock->execute([$cantidad_vendida, $item['id']]);
                // Insertamos el detalle de la venta
                $stmtDetalle->execute([$venta_id, $item['id'], $cantidad_vendida, $item['precio_venta'], $item['subtotal']]);

            } elseif ($item['tipo'] === 'pack') {
                // --- Lógica para packs ---
                // (Esta lógica se asegura de procesar cada producto DENTRO del pack)
                $total_original_pack = 0; $productos_del_pack = [];
                foreach($item['productos'] as $prod_en_pack) {
                    $stmtProductoInfo->execute([$prod_en_pack['id']]);
                    $precio_original = $stmtProductoInfo->fetchColumn();
                    $total_original_pack += $precio_original * $prod_en_pack['cantidad'];
                    $productos_del_pack[] = ['id' => $prod_en_pack['id'], 'cantidad' => $prod_en_pack['cantidad'], 'precio_original' => $precio_original];
                }

                foreach ($productos_del_pack as $producto_en_pack) {
                    $cantidad_item_pack = $producto_en_pack['cantidad'];

                    // Obtenemos el stock actual para el log
                    $stmtProductoInfo->execute([$producto_en_pack['id']]);
                    $stock_anterior_pack = $stmtProductoInfo->fetchColumn();
                    $stock_nuevo_pack = $stock_anterior_pack - $cantidad_item_pack;

                    // Insertamos en el log PARA CADA PRODUCTO DEL PACK
                    $stmtLog->execute([$producto_en_pack['id'], $cajero_id, $stock_anterior_pack, -$cantidad_item_pack, $stock_nuevo_pack, "Venta Pack #" . $venta_id]);
                    // Actualizamos el stock
                    $stmtStock->execute([$cantidad_item_pack, $producto_en_pack['id']]);
                    // Insertamos el detalle de la venta
                    $factor_prorrateo = ($producto_en_pack['precio_original'] * $cantidad_item_pack) / $total_original_pack;
                    $subtotal_final_item = $item['precio_venta'] * $factor_prorrateo;
                    $precio_unitario_final = $subtotal_final_item / $cantidad_item_pack;
                    $stmtDetalle->execute([$venta_id, $producto_en_pack['id'], $cantidad_item_pack, $precio_unitario_final, $subtotal_final_item]);
                }
            }
        }

        // Si todo fue bien, confirmamos todos los cambios en la base de datos
        $pdo->commit();
        header("Location: /ventas.php?exito=1");
        exit();

    } catch (Exception $e) {
        // Si algo falló, revertimos todos los cambios para no dejar datos inconsistentes
        $pdo->rollBack();
        die("Error crítico al procesar la venta: " . $e->getMessage());
    }
}
?>