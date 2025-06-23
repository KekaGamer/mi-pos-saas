<?php
// src/controllers/actualizar_usuario.php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    die("Acceso denegado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    // Seguridad CRÍTICA: solo el Super Admin puede asignar/mantener el rol de admin
    if ($rol === 'admin' && $_SESSION['user_id'] != 1) {
        die("No tienes permisos para asignar el rol de Administrador.");
    }

    // Proteger al Super Admin de cambiar su propio rol por error
    if ($id == 1 && $rol != 'admin') {
        $rol = 'admin'; // Forzar que el rol del ID 1 siempre sea admin
    }
    
    try {
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, password_hash = ? WHERE id = ?";
            $params = [$nombre, $email, $rol, $password_hash, $id];
        } else {
            $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?";
            $params = [$nombre, $email, $rol, $id];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: /usuarios.php?exito=edicion");
        exit();
    } catch (PDOException $e) {
        die("Error al actualizar el usuario: " . $e->getMessage());
    }
}
?>