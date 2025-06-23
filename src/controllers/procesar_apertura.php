<?php
// src/controllers/procesar_apertura.php -- VERSIÓN ACTUALIZADA
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_rol'], ['admin', 'cajero'])) {
    die("Acceso denegado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $monto_apertura = $_POST['monto_apertura'];
    $punto_caja_id = $_POST['punto_caja_id']; // Obtenemos la caja física seleccionada
    $cajero_id = $_SESSION['user_id'];
    $negocio_id = 1; 

    if (!is_numeric($monto_apertura) || $monto_apertura < 0 || empty($punto_caja_id)) {
        die("Datos de apertura inválidos.");
    }
    
    // Guardamos la nueva sesión de caja con la referencia a la caja física
    $sql = "INSERT INTO cajas_sesiones (negocio_id, punto_caja_id, cajero_id, monto_apertura, estado) VALUES (?, ?, ?, ?, 'abierta')";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$negocio_id, $punto_caja_id, $cajero_id, $monto_apertura]);
        
        // Guardamos los datos de la sesión en PHP
        $_SESSION['caja_sesion_id'] = $pdo->lastInsertId();
        
        // También guardamos el nombre de la caja para mostrarlo fácilmente
        $stmtNombre = $pdo->prepare("SELECT nombre FROM puntos_caja WHERE id = ?");
        $stmtNombre->execute([$punto_caja_id]);
        $_SESSION['punto_caja_nombre'] = $stmtNombre->fetchColumn();

        header("Location: /ventas.php");
        exit();
    } catch (PDOException $e) {
        die("Error al abrir la caja: " . $e->getMessage());
    }
}
?>