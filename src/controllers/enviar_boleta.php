<?php
// src/controllers/enviar_boleta.php
session_start();

// Usamos las clases de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Requerimos los archivos de la librería
require __DIR__ . '/../../libs/PHPMailer/src/Exception.php';
require __DIR__ . '/../../libs/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../../libs/PHPMailer/src/SMTP.php';

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/email_config.php'; // Nuestro archivo de configuración de correo

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $venta_id = $_POST['venta_id'];
    $email_cliente = $_POST['email_cliente'];
    $negocio_id = 1;

    // --- 1. Obtenemos los datos de la venta (igual que en boleta.php) ---
    try {
        $stmtNegocio = $pdo->prepare("SELECT * FROM negocios WHERE id = ?");
        $stmtNegocio->execute([$negocio_id]);
        $negocio = $stmtNegocio->fetch();
        $sqlVenta = "SELECT v.*, u.nombre as nombre_cajero FROM ventas v JOIN usuarios u ON v.cajero_id = u.id WHERE v.id = ?";
        $stmtVenta = $pdo->prepare($sqlVenta);
        $stmtVenta->execute([$venta_id]);
        $venta = $stmtVenta->fetch();
        $sqlDetalles = "SELECT vd.*, p.nombre as nombre_producto FROM venta_detalles vd JOIN productos p ON vd.producto_id = p.id WHERE vd.venta_id = ?";
        $stmtDetalles = $pdo->prepare($sqlDetalles);
        $stmtDetalles->execute([$venta_id]);
        $detalles = $stmtDetalles->fetchAll();
    } catch (PDOException $e) {
        header("Location: /boleta.php?id=$venta_id&email_error=1"); exit();
    }
    
    // --- 2. Generamos el HTML de la boleta en una variable usando "Output Buffering" ---
    ob_start();
    include __DIR__ . '/../views/templates/contenido_boleta.php';
    $html_boleta = ob_get_clean();

    // --- 3. Configuramos y enviamos el correo con PHPMailer ---
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP (usa los datos de email_config.php)
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;

        // Remitente y Destinatario
        $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
        $mail->addAddress($email_cliente);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Tu Boleta de Compra de ' . $negocio['nombre_local'];
        $mail->Body    = $html_boleta;
        $mail->AltBody = 'Gracias por tu compra. Adjunto encontrarás tu boleta.'; // Texto plano para clientes de correo sin HTML

        $mail->send();
        
        header("Location: /boleta.php?id=$venta_id&email_exito=1");
        exit();

    } catch (Exception $e) {
        // Redirigimos con un error si algo falla
        // Para depurar, podrías hacer un log del error: error_log("Mailer Error: {$mail->ErrorInfo}");
        header("Location: /boleta.php?id=$venta_id&email_error=1");
        exit();
    }
}
?>