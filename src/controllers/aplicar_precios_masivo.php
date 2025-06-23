<?php
// src/controllers/aplicar_precios_masivo.php
session_start();
require_once __DIR__ . '/../../config/database.php';

// Seguridad: Solo el administrador puede ejecutar esta acción
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    die("Acceso denegado.");
}

// Asegurarse de que se acceda por POST para mayor seguridad
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /configuracion.php");
    exit();
}

try {
    $negocio_id = 1; // Asumimos el negocio con ID 1

    // 1. Obtenemos la configuración de precios actual del negocio
    $stmtConfig = $pdo->prepare("SELECT porcentaje_ganancia_defecto, iva_porcentaje, precios_incluyen_iva FROM negocios WHERE id = ?");
    $stmtConfig->execute([$negocio_id]);
    $config = $stmtConfig->fetch();

    if (!$config) {
        throw new Exception("Configuración del negocio no encontrada.");
    }
    
    $margen = $config['porcentaje_ganancia_defecto'] / 100; // ej: 30 -> 0.30
    $iva = $config['iva_porcentaje'] / 100; // ej: 19 -> 0.19
    $precios_con_iva = $config['precios_incluyen_iva'];

    // 2. Construimos la consulta SQL para la actualización masiva
    $sql_update = "";
    
    if ($precios_con_iva) {
        // El precio de venta debe incluir el IVA.
        // Fórmula: PrecioVenta = (Costo * (1 + Margen)) * (1 + IVA)
        $sql_update = "UPDATE productos SET precio_venta = (precio_costo * (1 + ?)) * (1 + ?) WHERE negocio_id = ?";
        $params = [$margen, $iva, $negocio_id];
    } else {
        // El precio de venta es el valor neto (sin IVA).
        // Fórmula: PrecioVenta = Costo * (1 + Margen)
        $sql_update = "UPDATE productos SET precio_venta = precio_costo * (1 + ?) WHERE negocio_id = ?";
        $params = [$margen, $negocio_id];
    }
    
    $stmt = $pdo->prepare($sql_update);
    $stmt->execute($params);

    // 3. Obtenemos el número de filas afectadas para informar al usuario
    $filas_afectadas = $stmt->rowCount();
    
    // 4. Redirigimos con un mensaje de éxito
    header("Location: /configuracion.php?exito_masivo=1&afectados=$filas_afectadas");
    exit();

} catch (Exception $e) {
    // En caso de error, redirigimos con un mensaje
    header("Location: /configuracion.php?error_masivo=" . urlencode($e->getMessage()));
    exit();
}
?>