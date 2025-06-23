<?php
// src/controllers/guardar_caja_fisica.php
session_start();
require_once __DIR__ . '/../../config/database.php';

// Seguridad
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 'admin') {
    die("Acceso denegado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $negocio_id = 1; // Asumimos negocio_id = 1

    if (empty($nombre)) {
        die("El nombre de la caja no puede estar vacío.");
    }

    $sql = "INSERT INTO puntos_caja (negocio_id, nombre, estado) VALUES (?, ?, 'activo')";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$negocio_id, $nombre]);
        // Redirigimos a la lista de cajas para ver el resultado
        header("Location: /cajas_admin.php");
        exit();
    } catch (PDOException $e) {
        die("Error al guardar la nueva caja: " . $e->getMessage());
    }
}
?>