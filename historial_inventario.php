<?php
// historial_inventario.php
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if ($_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}

// Lógica de filtros
$where_clauses = ['p.negocio_id = ?'];
$params = [1];

if (!empty($_GET['producto_id'])) {
    $where_clauses[] = 'l.producto_id = ?';
    $params[] = $_GET['producto_id'];
}
if (!empty($_GET['usuario_id'])) {
    $where_clauses[] = 'l.usuario_id = ?';
    $params[] = $_GET['usuario_id'];
}
if (!empty($_GET['fecha_desde'])) {
    $where_clauses[] = 'l.fecha_hora >= ?';
    $params[] = $_GET['fecha_desde'];
}
if (!empty($_GET['fecha_hasta'])) {
    $where_clauses[] = 'l.fecha_hora <= ?';
    $params[] = $_GET['fecha_hasta'] . ' 23:59:59';
}

$where_sql = count($where_clauses) > 0 ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

try {
    // Consulta principal con JOINS para obtener nombres
    $sql = "SELECT l.*, p.nombre as nombre_producto, u.nombre as nombre_usuario 
            FROM inventario_log l
            JOIN productos p ON l.producto_id = p.id
            JOIN usuarios u ON l.usuario_id = u.id
            $where_sql
            ORDER BY l.fecha_hora DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll();

    // Obtenemos listas para los filtros
    $productos = $pdo->query("SELECT id, nombre FROM productos ORDER BY nombre ASC")->fetchAll();
    $usuarios = $pdo->query("SELECT id, nombre FROM usuarios ORDER BY nombre ASC")->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar el historial: " . $e->getMessage());
}
?>

<h1 class="mb-4">Historial de Movimientos de Inventario</h1>

<div class="card bg-dark text-white p-3 mb-4">
    <form method="GET" action="historial_inventario.php" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label for="producto_id" class="form-label">Producto</label>
            <select name="producto_id" id="producto_id" class="form-select form-control-dark">
                <option value="">Todos</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?php echo $producto['id']; ?>" <?php echo (isset($_GET['producto_id']) && $_GET['producto_id'] == $producto['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($producto['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="usuario_id" class="form-label">Usuario</label>
            <select name="usuario_id" id="usuario_id" class="form-select form-control-dark">
                <option value="">Todos</option>
                <?php foreach ($usuarios as $usuario): ?>
                     <option value="<?php echo $usuario['id']; ?>" <?php echo (isset($_GET['usuario_id']) && $_GET['usuario_id'] == $usuario['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($usuario['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><label for="fecha_desde" class="form-label">Desde</label><input type="date" name="fecha_desde" id="fecha_desde" class="form-control form-control-dark" value="<?php echo $_GET['fecha_desde'] ?? ''; ?>"></div>
        <div class="col-md-2"><label for="fecha_hasta" class="form-label">Hasta</label><input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control form-control-dark" value="<?php echo $_GET['fecha_hasta'] ?? ''; ?>"></div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Filtrar</button></div>
    </form>
</div>

<div class="card bg-secondary">
    <div class="card-body">
        <table class="table table-dark table-striped table-hover table-sm">
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Producto</th>
                    <th>Usuario</th>
                    <th>Tipo Movimiento</th>
                    <th class="text-end">Cant. Anterior</th>
                    <th class="text-end">Movimiento</th>
                    <th class="text-end">Cant. Nueva</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo date("d/m/Y H:i", strtotime($log['fecha_hora'])); ?></td>
                    <td><?php echo htmlspecialchars($log['nombre_producto']); ?></td>
                    <td><?php echo htmlspecialchars($log['nombre_usuario']); ?></td>
                    <td><span class="badge bg-info"><?php echo ucfirst(str_replace('_', ' ', $log['tipo_movimiento'])); ?></span></td>
                    <td class="text-end"><?php echo $log['cantidad_anterior']; ?></td>
                    <td class="text-end fw-bold <?php echo $log['cantidad_movimiento'] > 0 ? 'text-success' : 'text-danger'; ?>">
                        <?php echo ($log['cantidad_movimiento'] > 0 ? '+' : '') . $log['cantidad_movimiento']; ?>
                    </td>
                    <td class="text-end"><?php echo $log['cantidad_nueva']; ?></td>
                    <td><?php echo htmlspecialchars($log['motivo']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>