<?php
// src/controllers/procesar_importacion.php -- VERSIÓN FINAL CON CORRECCIÓN AUTOMÁTICA
session_start();
require_once __DIR__ . '/../../config/database.php';

// Seguridad y verificación de archivo
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') { die("Acceso denegado."); }
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) { header("Location: /index.php?import_error=Error con el archivo."); exit(); }

$file_path = $_FILES['csv_file']['tmp_name'];
$file = fopen($file_path, 'r');
if ($file === false) { header("Location: /index.php?import_error=No se pudo abrir el archivo CSV."); exit(); }

$negocio_id = 1; 
$header = fgetcsv($file, 0, ';'); // Le decimos a fgetcsv que use ';' como delimitador
$inserted_count = 0; 
$updated_count = 0; 
$categorias_cache = []; 
$line_number = 1;

$pdo->beginTransaction();
try {
    $stmt_check_prod = $pdo->prepare("SELECT id FROM productos WHERE codigo_barra = ? AND negocio_id = ?");
    $stmt_update_prod = $pdo->prepare("UPDATE productos SET nombre = ?, categoria_id = ?, precio_costo = ?, precio_venta = ?, stock = ?, stock_minimo = ?, impuesto_adicional = ? WHERE id = ?");
    $stmt_insert_prod = $pdo->prepare("INSERT INTO productos (nombre, categoria_id, codigo_barra, precio_costo, precio_venta, stock, stock_minimo, impuesto_adicional, negocio_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_check_cat = $pdo->prepare("SELECT id FROM categorias WHERE nombre = ? AND negocio_id = ?");
    $stmt_insert_cat = $pdo->prepare("INSERT INTO categorias (nombre, negocio_id) VALUES (?, ?)");

    while (($row = fgetcsv($file, 0, ';')) !== FALSE) {
        $line_number++;
        
        $nombre = trim($row[0]);
        $nombre_categoria = trim($row[1]);
        $codigo_barra_raw = trim($row[2]);
        $precio_costo = trim($row[3]);
        $precio_venta = trim($row[4]);
        $stock = trim($row[5]);
        $stock_minimo = !empty(trim($row[6])) ? trim($row[6]) : 5;
        $impuesto_adicional = !empty(trim($row[7])) ? trim($row[7]) : null;

        // --- NUEVO BLOQUE DE CORRECCIÓN AUTOMÁTICA DE CÓDIGO DE BARRAS ---
        // Detectamos si el código de barras está en notación científica (ej: 8,80E+12)
        if (stripos($codigo_barra_raw, 'E+') !== false) {
            // Reemplazamos la coma decimal (formato español) por un punto
            $numero_cientifico = str_replace(',', '.', $codigo_barra_raw);
            // Convertimos la notación científica a un número y luego a una cadena de texto sin decimales
            $codigo_barra = sprintf('%.0f', floatval($numero_cientifico));
        } else {
            // Si no está en notación científica, lo usamos tal cual, convirtiendo un string vacío en NULL
            $codigo_barra = !empty($codigo_barra_raw) ? $codigo_barra_raw : null;
        }
        // --- FIN DEL BLOQUE DE CORRECCIÓN ---

        if (empty($nombre) || !ctype_digit($precio_costo) || !ctype_digit($precio_venta) || !ctype_digit($stock)) {
            throw new Exception("Fila inválida (N° $line_number). Nombre no puede estar vacío. Costo, venta y stock deben ser números enteros.");
        }
        
        $categoria_id = null;
        if (!empty($nombre_categoria)) {
            if (isset($categorias_cache[$nombre_categoria])) {
                $categoria_id = $categorias_cache[$nombre_categoria];
            } else {
                $stmt_check_cat->execute([$nombre_categoria, $negocio_id]);
                $categoria_id = $stmt_check_cat->fetchColumn();
                if (!$categoria_id) {
                    $stmt_insert_cat->execute([$nombre_categoria, $negocio_id]);
                    $categoria_id = $pdo->lastInsertId();
                }
                $categorias_cache[$nombre_categoria] = $categoria_id;
            }
        }
        
        $existing_id = null;
        if ($codigo_barra !== null) {
            $stmt_check_prod->execute([$codigo_barra, $negocio_id]);
            $existing_id = $stmt_check_prod->fetchColumn();
        }

        if ($existing_id) {
            $stmt_update_prod->execute([$nombre, $categoria_id, $precio_costo, $precio_venta, $stock, $stock_minimo, $impuesto_adicional, $existing_id]);
            $updated_count++;
        } else {
            $stmt_insert_prod->execute([$nombre, $categoria_id, $codigo_barra, $precio_costo, $precio_venta, $stock, $stock_minimo, $impuesto_adicional, $negocio_id]);
            $inserted_count++;
        }
    }
    
    $pdo->commit();
    header("Location: /index.php?import_success=1&inserted=$inserted_count&updated=$updated_count");
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: /index.php?import_error=" . urlencode($e->getMessage()));
} finally {
    fclose($file); 
    exit();
}
?>