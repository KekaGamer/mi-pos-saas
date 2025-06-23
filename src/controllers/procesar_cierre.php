<?php
// src/controllers/procesar_cierre.php
session_start();
require_once __DIR__ . '/../../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['caja_sesion_id'])) {
    die("Acceso denegado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $caja_sesion_id = $_SESSION['caja_sesion_id'];
    $monto_teorico = $_POST['monto_teorico'];
    $monto_real = $_POST['monto_real'];

    if (!is_numeric($monto_real) || $monto_real < 0) {
        die("Monto real inválido.");
    }

    $diferencia = $monto_real - $monto_teorico;

    // Actualizamos el registro de la sesión de caja con los datos de cierre
    $sql = "UPDATE cajas_sesiones SET 
                monto_cierre_teorico = ?,
                monto_cierre_real = ?,
                diferencia = ?,
                estado = 'cerrada',
                fecha_cierre = CURRENT_TIMESTAMP
            WHERE id = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$monto_teorico, $monto_real, $diferencia, $caja_sesion_id]);

        // Limpiamos la sesión del usuario para que tenga que abrir una nueva caja en su próximo turno
        unset($_SESSION['caja_sesion_id']);
        
        // Podríamos redirigir a una página de resumen del cierre, pero por ahora lo llevamos al login o al dashboard.
        // Opcional: podríamos guardar la diferencia en una variable de sesión para mostrarla en la siguiente página.
        $_SESSION['cierre_exitoso'] = true;
        $_SESSION['cierre_diferencia'] = $diferencia;

        header("Location: /index.php"); // O a login.php
        exit();

    } catch (PDOException $e) {
        die("Error al cerrar la caja: " . $e->getMessage());
    }
}
?>