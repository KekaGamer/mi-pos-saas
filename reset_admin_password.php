<?php
// reset_admin_password.php
// Este es un script de un solo uso para resetear la contraseña del administrador.

// Habilitamos la visualización de errores para no perdernos nada.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/database.php';

// La nueva contraseña será extremadamente simple para evitar cualquier error.
$nueva_contrasena = '12345'; 

// Usamos el propio PHP de tu servidor para generar el hash correcto.
$hash_generado = password_hash($nueva_contrasena, PASSWORD_DEFAULT);

$admin_id = 1;

echo "<div style='font-family: monospace; padding: 20px;'>";
echo "<h1>Reseteando Contraseña del Admin...</h1>";

try {
    $sql = "UPDATE usuarios SET password_hash = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$hash_generado, $admin_id]);

    if ($stmt->rowCount() > 0) {
        echo "<h2 style='color: green;'>¡ÉXITO!</h2>";
        echo "<p>La contraseña para el usuario con ID 1 (`administrador@pos.cl`) ha sido reseteada.</p>";
        echo "<p>Por favor, inicia sesión ahora con las siguientes credenciales:</p>";
        echo "<ul>";
        echo "<li><strong>Usuario:</strong> administrador@pos.cl</li>";
        echo "<li><strong>Nueva Contraseña:</strong> " . $nueva_contrasena . "</li>";
        echo "</ul>";
        echo "<p style='color:red; font-weight:bold; font-size: 1.2em;'>IMPORTANTE: Por seguridad, borra este archivo (reset_admin_password.php) de tu carpeta htdocs AHORA MISMO.</p>";
    } else {
        echo "<h2 style='color: orange;'>AVISO.</h2>";
        echo "<p>La consulta se ejecutó, pero no se actualizó ninguna fila. Esto usualmente significa que no se encontró ningún usuario con `id = 1`.</p>";
        echo "<p>Por favor, verifica en phpMyAdmin que el usuario Super Administrador realmente existe y tiene el ID 1.</p>";
    }

} catch (PDOException $e) {
    die("<h2 style='color: red;'>ERROR AL ACTUALIZAR LA BASE DE DATOS:</h2><p>" . $e->getMessage() . "</p>");
}

echo "</div>";
?>