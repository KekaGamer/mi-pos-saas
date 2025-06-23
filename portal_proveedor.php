<?php
// portal_proveedor.php
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

// Seguridad: solo usuarios con rol 'proveedor' y un ID de proveedor asociado
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] !== 'proveedor' || !isset($_SESSION['proveedor_id'])) {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}

$proveedor_id = $_SESSION['proveedor_id'];

// Obtenemos solo las facturas de este proveedor
try {
    $stmt = $pdo->prepare("SELECT * FROM facturas_proveedor WHERE proveedor_id = ? ORDER BY fecha_emision DESC");
    $stmt->execute([$proveedor_id]);
    $facturas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar sus facturas: " . $e->getMessage());
}
?>

<h1 class="mb-4">Portal de Proveedor - Estado de Facturas</h1>
<p>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_nombre']); ?>. Aquí puede ver el estado de las facturas que ha emitido.</p>

<div class="card bg-secondary">
    <div class="card-body">
        <table class="table table-dark table-striped table-hover">
            <thead>
                <tr>
                    <th>N° Factura</th>
                    <th>Monto</th>
                    <th>Fecha Emisión</th>
                    <th>Estado del Pago</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $factura): ?>
                <tr>
                    <td><?php echo htmlspecialchars($factura['numero_factura']); ?></td>
                    <td>$<?php echo number_format($factura['monto'], 0, ',', '.'); ?></td>
                    <td><?php echo date("d/m/Y", strtotime($factura['fecha_emision'])); ?></td>
                    <td><span class="badge bg-<?php echo $factura['estado_pago'] == 'pagada' ? 'success' : 'warning'; ?>"><?php echo ucfirst($factura['estado_pago']); ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>