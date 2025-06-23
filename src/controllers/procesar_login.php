<?php
// src/controllers/procesar_login.php -- VERSIÓN ACTUALIZADA
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password_hash'])) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['user_nombre'] = $usuario['nombre'];
        $_SESSION['user_rol'] = $usuario['rol'];

        // Si el usuario es un proveedor, guardamos su ID de proveedor y lo redirigimos a su portal
        if ($usuario['rol'] === 'proveedor') {
            $_SESSION['proveedor_id'] = $usuario['proveedor_id'];
            header("Location: /portal_proveedor.php");
        } else {
            // Para todos los demás roles, los enviamos al dashboard principal
            header("Location: /reportes.php");
        }
        exit();
    } else {
        header("Location: /login.php?error=1");
        exit();
    }
}
?>