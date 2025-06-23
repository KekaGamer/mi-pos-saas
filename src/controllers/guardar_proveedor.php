<?php
// src/controllers/guardar_proveedor.php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!in_array($_SESSION['user_rol'], ['admin', 'contador'])) {
    die("Acceso denegado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $negocio_id = 1; // Simulado

    $sql = "INSERT INTO proveedores (negocio_id, nombre, email, telefono) VALUES (?, ?, ?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$negocio_id, $nombre, $email, $telefono]);
        header("Location: /proveedores.php");
        exit();
    } catch (PDOException $e) {
        die("Error al guardar el proveedor: " . $e->getMessage());
    }
}
?>