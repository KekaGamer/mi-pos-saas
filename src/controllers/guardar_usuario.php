<?php
// src/controllers/guardar_usuario.php -- VERSIÓN CORREGIDA
session_start();
require_once __DIR__ . '/../../config/database.php';

// Seguridad
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 'admin') {
    die("Acceso denegado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rol = $_POST['rol'];
    $proveedor_id = ($rol === 'proveedor') ? $_POST['proveedor_id'] : null;
    $negocio_id = 1; 
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($password) || empty($rol)) {
        die("Error: Todos los campos son obligatorios.");
    }
    if ($rol === 'proveedor' && empty($proveedor_id)) {
        die("Error: Debe seleccionar una empresa proveedora para un usuario de tipo proveedor.");
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (negocio_id, nombre, email, password_hash, rol, proveedor_id) VALUES (?, ?, ?, ?, ?, ?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$negocio_id, $nombre, $email, $password_hash, $rol, $proveedor_id]);
        
        // --- LÍNEA CORREGIDA ---
        // Redirigimos de vuelta a la misma página de creación con un mensaje de éxito.
        header("Location: /crear_usuario.php?exito=1");
        exit();

    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            die("Error al registrar el usuario: El correo electrónico ya está en uso.");
        } else {
            die("Error al registrar el usuario: " . $e->getMessage());
        }
    }
}
?>