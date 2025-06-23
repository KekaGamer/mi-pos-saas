<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
if ($_SESSION['user_rol'] != 'admin') { die("Acceso denegado."); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $negocio_id = 1;
    if (empty($nombre)) { die("El nombre no puede estar vacío."); }

    $sql = "INSERT INTO categorias (negocio_id, nombre) VALUES (?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$negocio_id, $nombre]);
        header("Location: /categorias.php");
        exit();
    } catch (PDOException $e) {
        die("Error al guardar la categoría: " . $e->getMessage());
    }
}
?>