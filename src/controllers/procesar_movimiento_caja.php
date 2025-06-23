<?php
// src/controllers/procesar_movimiento_caja.php
session_start();
require_once __DIR__ . '/../../config/database.php';

// Seguridad: Verificar que el usuario esté logueado y tenga una caja abierta
if (!isset($_SESSION['user_id']) || !isset($_SESSION['caja_sesion_id'])) {
    die("Acceso denegado o no hay una sesión de caja activa.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $caja_sesion_id = $_SESSION['caja_sesion_id'];
    $usuario_id = $_SESSION['user_id'];
    $tipo = $_POST['tipo'];
    $monto = $_POST['monto'];
    $motivo = trim($_POST['motivo']);

    // Validación de datos
    if (!in_array($tipo, ['ingreso', 'retiro']) || !is_numeric($monto) || $monto <= 0 || empty($motivo)) {
        die("Datos inválidos. Por favor, complete todos los campos correctamente.");
    }

    $sql = "INSERT INTO caja_movimientos (caja_sesion_id, usuario_id, tipo, monto, motivo) VALUES (?, ?, ?, ?, ?)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$caja_sesion_id, $usuario_id, $tipo, $monto, $motivo]);

        // Redirigimos de vuelta a la pantalla de ventas con un mensaje de éxito
        header("Location: /ventas.php?movimiento_exito=1");
        exit();

    } catch (PDOException $e) {
        die("Error al registrar el movimiento de caja: " . $e->getMessage());
    }
} else {
    // Si se accede directamente al archivo, redirigir
    header("Location: /ventas.php");
    exit();
}
?>