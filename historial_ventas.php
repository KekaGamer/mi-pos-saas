<?php
// historial_ventas.php -- VERSIÓN COMPLETA Y CORREGIDA
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_rol'], ['admin', 'contador'])) {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}

$where_clauses = ['v.negocio_id = ?'];
$params = [1];
if (!empty($_GET['fecha_desde'])) {
    $where_clauses[] = 'DATE(v.fecha_hora) >= ?';
    $params[] = $_GET['fecha_desde'];
}
if (!empty($_GET['fecha_hasta'])) {
    $where_clauses[] = 'DATE(v.fecha_hora) <= ?';
    $params[] = $_GET['fecha_hasta'];
}
if (!empty($_GET['metodo_pago'])) {
    $where_clauses[] = 'v.metodo_pago = ?';
    $params[] = $_GET['metodo_pago'];
}
$where_sql = count($where_clauses) > 0 ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

try {
    $sqlTotales = "SELECT COUNT(*) as num_ventas, SUM(v.total) as total_vendido FROM ventas v $where_sql";
    $stmtTotales = $pdo->prepare($sqlTotales);
    $stmtTotales->execute($params);
    $totales = $stmtTotales->fetch(PDO::FETCH_ASSOC);

    $sqlVentas = "SELECT v.id, v.fecha_hora, v.total, v.metodo_pago, u.nombre as nombre_cajero FROM ventas v JOIN usuarios u ON v.cajero_id = u.id $where_sql ORDER BY v.fecha_hora DESC";
    $stmtVentas = $pdo->prepare($sqlVentas);
    $stmtVentas->execute($params);
    $ventas = $stmtVentas->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar el historial de ventas: " . $e->getMessage());
}

$metodos_de_pago = ['efectivo', 'debito', 'credito', 'transferencia'];
?>

<h1 class="mb-4">Historial de Ventas</h1>

<div class="card bg-dark text-white p-3 mb-4">
    <form method="GET" action="historial_ventas.php" class="row g-3 align-items-end">
        <div class="col-md-3"><label for="fecha_desde" class="form-label">Desde</label><input type="date" name="fecha_desde" id="fecha_desde" class="form-control form-control-dark" value="<?php echo htmlspecialchars($_GET['fecha_desde'] ?? ''); ?>"></div>
        <div class="col-md-3"><label for="fecha_hasta" class="form-label">Hasta</label><input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control form-control-dark" value="<?php echo htmlspecialchars($_GET['fecha_hasta'] ?? ''); ?>"></div>
        <div class="col-md-3">
            <label for="metodo_pago" class="form-label">Método de Pago</label>
            <select name="metodo_pago" id="metodo_pago" class="form-select form-control-dark">
                <option value="">Todos</option>
                <?php foreach ($metodos_de_pago as $metodo): ?>
                    <option value="<?php echo $metodo; ?>" <?php echo (isset($_GET['metodo_pago']) && $_GET['metodo_pago'] == $metodo) ? 'selected' : ''; ?>><?php echo ucfirst($metodo); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex"><button type="submit" class="btn btn-primary w-100 me-2">Filtrar</button><a href="historial_ventas.php" class="btn btn-secondary" title="Limpiar Filtros"><i class="bi bi-x-lg"></i></a></div>
    </form>
</div>

<div class="row mb-4">
    <div class="col-md-6"><div class="card bg-secondary text-white text-center"><div class="card-body"><h5 class="card-title">Ventas Totales (en período)</h5><p class="card-text fs-2 fw-bold">$<?php echo number_format($totales['total_vendido'] ?? 0, 0, ',', '.'); ?></p></div></div></div>
    <div class="col-md-6"><div class="card bg-secondary text-white text-center"><div class="card-body"><h5 class="card-title">N° de Transacciones (en período)</h5><p class="card-text fs-2 fw-bold"><?php echo $totales['num_ventas'] ?? 0; ?></p></div></div></div>
</div>

<div class="card bg-secondary">
    <div class="card-body">
        <table class="table table-dark table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th>Folio Venta</th>
                    <th>Fecha y Hora</th>
                    <th>Cajero</th>
                    <th>Total</th>
                    <th>Método de Pago</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventas)): ?>
                    <tr><td colspan="6" class="text-center">No se encontraron ventas que coincidan con los filtros seleccionados.</td></tr>
                <?php else: ?>
                    <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?php echo $venta['id']; ?></td>
                            <td><?php echo date("d/m/Y H:i:s", strtotime($venta['fecha_hora'])); ?></td>
                            <td><?php echo htmlspecialchars($venta['nombre_cajero']); ?></td>
                            <td>$<?php echo number_format($venta['total'], 0, ',', '.'); ?></td>
                            <td><?php echo ucfirst($venta['metodo_pago']); ?></td>
                            <td class="text-center"><a href="boleta.php?id=<?php echo $venta['id']; ?>" class="btn btn-sm btn-info" target="_blank" title="Ver Boleta"><i class="bi bi-receipt"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/src/views/partials/footer.php';
?>