<?php
// boleta.php -- VERSIÓN REFACTORIZADA
session_start();
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id'])) { die("Acceso denegado."); }
if (!isset($_GET['id'])) { die("ID de venta no especificado."); }

$id_venta = $_GET['id'];
$negocio_id = 1;

try {
    $stmtNegocio = $pdo->prepare("SELECT * FROM negocios WHERE id = ?");
    $stmtNegocio->execute([$negocio_id]);
    $negocio = $stmtNegocio->fetch();

    $sqlVenta = "SELECT v.*, u.nombre as nombre_cajero FROM ventas v JOIN usuarios u ON v.cajero_id = u.id WHERE v.id = ?";
    $stmtVenta = $pdo->prepare($sqlVenta);
    $stmtVenta->execute([$id_venta]);
    $venta = $stmtVenta->fetch();

    if (!$venta) { die("Venta no encontrada."); }

    $sqlDetalles = "SELECT vd.*, p.nombre as nombre_producto FROM venta_detalles vd JOIN productos p ON vd.producto_id = p.id WHERE vd.venta_id = ?";
    $stmtDetalles = $pdo->prepare($sqlDetalles);
    $stmtDetalles->execute([$id_venta]);
    $detalles = $stmtDetalles->fetchAll();
} catch (PDOException $e) { die("Error al generar la boleta: " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Boleta de Venta #<?php echo $venta['id']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Courier New', Courier, monospace; background-color: #f4f4f4; display: flex; flex-direction: column; align-items: center; margin: 0; padding: 20px; }
        .boleta-container { width: 300px; background-color: #fff; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.15); color: #000; }
        .header { text-align: center; } .header img { max-width: 100px; margin-bottom: 10px; } .header h2 { margin: 0; font-size: 1.2em; }
        .info-venta, .totales { margin-top: 15px; border-top: 1px dashed #000; padding-top: 10px; } .info-venta p, .totales p { margin: 2px 0; font-size: 0.9em; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; } th, td { font-size: 0.8em; padding: 3px 0; } th { text-align: left; border-bottom: 1px solid #000; }
        .text-right { text-align: right; } .footer { text-align: center; margin-top: 20px; font-size: 0.8em; border-top: 1px dashed #000; padding-top: 10px; }
        .actions-container { width: 300px; text-align: center; margin-top: 20px; display: flex; justify-content: space-around; }
        @media print { body { background-color: #fff; padding: 0; } .boleta-container { box-shadow: none; width: 100%; } .actions-container, .modal { display: none; } }
    </style>
</head>
<body>
    
    <?php if (isset($_GET['email_exito'])): ?>
        <div class="alert alert-success">¡Boleta enviada por correo exitosamente!</div>
    <?php elseif (isset($_GET['email_error'])): ?>
        <div class="alert alert-danger">Error al enviar el correo. Revise la configuración.</div>
    <?php endif; ?>

    <?php 
    // Incluimos el contenido de la boleta desde nuestra nueva plantilla
    include __DIR__ . '/src/views/templates/contenido_boleta.php'; 
    ?>

    <div class="actions-container">
        <button class="btn btn-primary" onclick="window.print();"><i class="bi bi-printer-fill"></i> Imprimir</button>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-email"><i class="bi bi-envelope-fill"></i> Enviar por Correo</button>
    </div>

    <div class="modal fade" id="modal-email" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Enviar Boleta por Correo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="src/controllers/enviar_boleta.php" method="POST">
            <div class="modal-body">
                <input type="hidden" name="venta_id" value="<?php echo $venta['id']; ?>">
                <div class="mb-3">
                    <label for="email_cliente" class="form-label">Email del Cliente</label>
                    <input type="email" class="form-control" name="email_cliente" id="email_cliente" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>