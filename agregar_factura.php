<?php
// agregar_factura.php
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if (!in_array($_SESSION['user_rol'], ['admin', 'contador'])) {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}

// Obtenemos la lista de proveedores para el menú desplegable
$stmt = $pdo->query("SELECT id, nombre FROM proveedores WHERE negocio_id = 1 ORDER BY nombre ASC");
$proveedores = $stmt->fetchAll();
?>

<h1 class="mb-4">Registrar Nueva Factura</h1>
<div class="card bg-secondary text-white p-4">
    <form action="src/controllers/guardar_factura.php" method="POST">
        <div class="mb-3">
            <label for="proveedor_id" class="form-label">Proveedor</label>
            <select class="form-select form-control-dark" id="proveedor_id" name="proveedor_id" required>
                <option value="">Seleccione un proveedor...</option>
                <?php foreach ($proveedores as $proveedor): ?>
                    <option value="<?php echo $proveedor['id']; ?>"><?php echo htmlspecialchars($proveedor['nombre']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="numero_factura" class="form-label">Número de Factura</label>
            <input type="text" class="form-control form-control-dark" id="numero_factura" name="numero_factura" required>
        </div>
        <div class="mb-3">
            <label for="monto" class="form-label">Monto Total</label>
            <input type="number" class="form-control form-control-dark" id="monto" name="monto" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="fecha_emision" class="form-label">Fecha de Emisión</label>
            <input type="date" class="form-control form-control-dark" id="fecha_emision" name="fecha_emision" required>
        </div>
        <div class="mb-3">
            <label for="estado_pago" class="form-label">Estado del Pago</label>
            <select class="form-select form-control-dark" id="estado_pago" name="estado_pago" required>
                <option value="pendiente">Pendiente</option>
                <option value="pagada">Pagada</option>
            </select>
        </div>
        <div class="d-flex justify-content-end">
            <a href="facturas.php" class="btn btn-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Factura</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>