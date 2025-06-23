<?php
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if ($_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}

try {
    $stmtStock = $pdo->prepare("SELECT id, nombre, stock, stock_minimo FROM productos WHERE stock <= stock_minimo AND negocio_id = 1 ORDER BY stock ASC");
    $stmtStock->execute();
    $productos_bajos_stock = $stmtStock->fetchAll();
} catch (PDOException $e) { $productos_bajos_stock = []; }
?>

<h1 class="mb-4">Reporte de Productos con Stock Bajo</h1>
<div class="card bg-secondary">
    <div class="card-body">
        <?php if (empty($productos_bajos_stock)): ?>
            <div class="alert alert-success text-center">¡Excelente! No hay productos con stock bajo.</div>
        <?php else: ?>
            <table class="table table-dark table-striped table-hover">
                <thead>
                    <tr><th>Producto</th><th>Stock Actual</th><th>Stock Mínimo Definido</th><th class="text-center">Acciones</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($productos_bajos_stock as $producto): ?>
                    <tr class="text-danger">
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td class="fw-bold"><?php echo $producto['stock']; ?></td>
                        <td><?php echo $producto['stock_minimo']; ?></td>
                        <td class="text-center"><a href="src/views/editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-warning">Editar / Reponer</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>