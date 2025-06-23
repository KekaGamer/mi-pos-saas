<?php
// cierre_caja.php -- VERSIÓN AVANZADA
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['caja_sesion_id'])) {
    echo "<div class='alert alert-info text-center'>No hay una sesión de caja activa para cerrar.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}

$caja_sesion_id = $_SESSION['caja_sesion_id'];

try {
    // 1. Obtener monto de apertura
    $monto_apertura = $pdo->query("SELECT monto_apertura FROM cajas_sesiones WHERE id = $caja_sesion_id")->fetchColumn();

    // 2. Obtener total de ventas POR MÉTODO DE PAGO
    $stmtVentas = $pdo->prepare("SELECT metodo_pago, SUM(total) as total FROM ventas WHERE caja_sesion_id = ? GROUP BY metodo_pago");
    $stmtVentas->execute([$caja_sesion_id]);
    $ventas_por_metodo = $stmtVentas->fetchAll(PDO::FETCH_KEY_PAIR);

    // 3. Obtener total de movimientos de caja (ingresos y retiros)
    $stmtMovimientos = $pdo->prepare("SELECT tipo, SUM(monto) as total FROM caja_movimientos WHERE caja_sesion_id = ? GROUP BY tipo");
    $stmtMovimientos->execute([$caja_sesion_id]);
    $movimientos_de_caja = $stmtMovimientos->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // 4. Obtener el detalle de los movimientos para listarlos
    $stmtDetalleMov = $pdo->prepare("SELECT * FROM caja_movimientos WHERE caja_sesion_id = ? ORDER BY fecha_hora ASC");
    $stmtDetalleMov->execute([$caja_sesion_id]);
    $detalle_movimientos = $stmtDetalleMov->fetchAll();

    // 5. Calcular totales para la cuadratura
    $total_ventas_efectivo = $ventas_por_metodo['efectivo'] ?? 0;
    $total_ingresos = $movimientos_de_caja['ingreso'] ?? 0;
    $total_retiros = $movimientos_de_caja['retiro'] ?? 0;
    $monto_teorico = ($monto_apertura + $total_ventas_efectivo + $total_ingresos) - $total_retiros;

} catch (PDOException $e) { die("Error al calcular el cierre de caja: " . $e->getMessage()); }

?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <h1 class="text-center mb-4">Cierre y Cuadratura de Caja</h1>
        <div class="card bg-secondary text-white">
            <div class="card-header fs-4 text-center">Resumen del Turno (Caja #<?php echo $caja_sesion_id; ?>)</div>
            <div class="card-body">
                <h4 class="text-info">1. Cuadratura de Efectivo</h4>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between">Monto de Apertura: <span>$<?php echo number_format($monto_apertura, 0, ',', '.'); ?></span></li>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between">(+) Total Ventas en Efectivo: <span class="text-success fw-bold">$<?php echo number_format($total_ventas_efectivo, 0, ',', '.'); ?></span></li>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between">(+) Otros Ingresos de Efectivo: <span class="text-success">$<?php echo number_format($total_ingresos, 0, ',', '.'); ?></span></li>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between">(-) Retiros de Efectivo: <span class="text-danger">-$<?php echo number_format($total_retiros, 0, ',', '.'); ?></span></li>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between fs-4 fw-bold border-top border-info">(=) Total Teórico en Caja: <span>$<?php echo number_format($monto_teorico, 0, ',', '.'); ?></span></li>
                </ul>

                <h4 class="text-info mt-4">2. Resumen de Otros Medios de Pago</h4>
                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between">Total Ventas con Débito/Crédito: <span>$<?php echo number_format(($ventas_por_metodo['debito'] ?? 0) + ($ventas_por_metodo['credito'] ?? 0), 0, ',', '.'); ?></span></li>
                    <li class="list-group-item bg-dark text-white d-flex justify-content-between">Total Ventas con Transferencia: <span>$<?php echo number_format($ventas_por_metodo['transferencia'] ?? 0), 0, ',', '.'; ?></span></li>
                </ul>

                <?php if (!empty($detalle_movimientos)): ?>
                <h4 class="text-info mt-4">3. Detalle de Movimientos de Caja</h4>
                <table class="table table-sm table-dark">
                    <thead><tr><th>Hora</th><th>Tipo</th><th>Monto</th><th>Motivo</th></tr></thead>
                    <tbody>
                        <?php foreach($detalle_movimientos as $mov): ?>
                        <tr class="<?php echo $mov['tipo'] == 'ingreso' ? 'text-success' : 'text-warning'; ?>">
                            <td><?php echo date('H:i', strtotime($mov['fecha_hora'])); ?></td>
                            <td><?php echo ucfirst($mov['tipo']); ?></td>
                            <td>$<?php echo number_format($mov['monto'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($mov['motivo']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

                <hr class="my-4">
                <form action="src/controllers/procesar_cierre.php" method="POST">
                    <input type="hidden" name="monto_teorico" value="<?php echo $monto_teorico; ?>">
                    <div class="mb-3"><label for="monto_real" class="form-label fs-4">Monto Real Contado en Caja:</label>
                        <div class="input-group"><span class="input-group-text">$</span><input type="number" class="form-control form-control-lg" id="monto_real" name="monto_real" step="1" required></div>
                    </div>
                    <div class="d-grid mt-4"><button type="submit" class="btn btn-danger btn-lg">Finalizar y Cerrar Turno</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>