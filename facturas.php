<?php
// facturas.php
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if (!in_array($_SESSION['user_rol'], ['admin', 'contador'])) {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}

// Obtenemos las facturas, uniendo la tabla de proveedores para saber el nombre
try {
    $sql = "SELECT f.*, p.nombre as nombre_proveedor
            FROM facturas_proveedor f
            JOIN proveedores p ON f.proveedor_id = p.id
            WHERE p.negocio_id = 1
            ORDER BY f.fecha_emision DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $facturas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar las facturas: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Gestión de Facturas de Proveedores</h1>
    <a href="agregar_factura.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Registrar Factura</a>
</div>

<div class="card bg-secondary">
    <div class="card-body">
        <table class="table table-dark table-striped table-hover">
            <thead>
                <tr>
                    <th>N° Factura</th>
                    <th>Proveedor</th>
                    <th>Monto</th>
                    <th>Fecha Emisión</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $factura): ?>
                <tr>
                    <td><?php echo htmlspecialchars($factura['numero_factura']); ?></td>
                    <td><?php echo htmlspecialchars($factura['nombre_proveedor']); ?></td>
                    <td>$<?php echo number_format($factura['monto'], 0, ',', '.'); ?></td>
                    <td><?php echo date("d/m/Y", strtotime($factura['fecha_emision'])); ?></td>
                    <td><span class="badge bg-<?php echo $factura['estado_pago'] == 'pagada' ? 'success' : 'warning'; ?>"><?php echo ucfirst($factura['estado_pago']); ?></span></td>
                    <td></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>