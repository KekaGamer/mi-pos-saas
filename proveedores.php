<?php
// proveedores.php
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if (!in_array($_SESSION['user_rol'], ['admin', 'contador'])) {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM proveedores WHERE negocio_id = 1 ORDER BY nombre ASC");
    $stmt->execute();
    $proveedores = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al cargar proveedores: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Gestión de Proveedores</h1>
    <a href="agregar_proveedor.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Agregar Proveedor</a>
</div>

<div class="card bg-secondary">
    <div class="card-body">
        <table class="table table-dark table-striped table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proveedores as $proveedor): ?>
                <tr>
                    <td><?php echo htmlspecialchars($proveedor['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['email']); ?></td>
                    <td><?php echo htmlspecialchars($proveedor['telefono']); ?></td>
                    <td></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>