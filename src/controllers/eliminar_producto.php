<?php
// src/controllers/eliminar_producto.php

require_once __DIR__ . '/../../config/database.php';

if (isset($_POST['id'])) {
    $id_producto = $_POST['id'];

    $sql = "DELETE FROM productos WHERE id = ?";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_producto]);

        header("Location: /index.php");
        exit();

    } catch (PDOException $e) {
        die("Error al eliminar el producto: " . $e->getMessage());
    }

} else {
    header("Location: /index.php");
    exit();
}
?>