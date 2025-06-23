<?php
// src/controllers/actualizar_configuracion.php -- VERSIÓN CON IVA
session_start();
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['user_rol'] != 'admin') { die("Acceso denegado."); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recogemos todos los datos del formulario
    $negocio_id = $_POST['negocio_id'];
    $nombre_local = trim($_POST['nombre_local']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $ganancia = $_POST['porcentaje_ganancia_defecto'];
    $iva_porcentaje = $_POST['iva_porcentaje'];
    $precios_incluyen_iva = $_POST['precios_incluyen_iva'];
    $logo_path_db = null;

    // Lógica para subir el logo (sin cambios)
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $upload_dir = __DIR__ . '/../../uploads/logos/';
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
        $file_name = uniqid() . '-' . basename($_FILES['logo']['name']);
        $target_file = $upload_dir . $file_name;
        $check = getimagesize($_FILES['logo']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
                $logo_path_db = '/uploads/logos/' . $file_name;
            } else { die("Error al mover el archivo subido."); }
        } else { die("El archivo subido no es una imagen válida."); }
    }

    try {
        // Construimos la consulta dinámicamente, ahora con los campos de IVA
        $params = [
            $nombre_local, $direccion, $telefono, $ganancia, $iva_porcentaje, $precios_incluyen_iva
        ];
        $sql = "UPDATE negocios SET nombre_local = ?, direccion = ?, telefono = ?, porcentaje_ganancia_defecto = ?, iva_porcentaje = ?, precios_incluyen_iva = ?";
        
        if ($logo_path_db !== null) {
            $sql .= ", logo_url = ?";
            $params[] = $logo_path_db;
        }
        $sql .= " WHERE id = ?";
        $params[] = $negocio_id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        header("Location: /configuracion.php?exito=1");
        exit();
    } catch (PDOException $e) {
        die("Error al actualizar la configuración: " . $e->getMessage());
    }
}
?>