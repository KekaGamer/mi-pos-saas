<?php
// cajas_admin.php -- VERSIÓN MEJORADA
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}

$puntos_caja = $pdo->query("SELECT * FROM puntos_caja WHERE negocio_id = 1 ORDER BY nombre ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Administrar Cajas Físicas</h1>
    <a href="agregar_caja_fisica.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Agregar Caja Física</a>
</div>

<div class="card bg-secondary">
    <div class="card-body">
        <table class="table table-dark table-striped table-hover">
            <thead>
                <tr><th>ID</th><th>Nombre de la Caja / Terminal</th><th>Estado</th></tr>
            </thead>
            <tbody>
                <?php if (empty($puntos_caja)): ?>
                    <tr>
                        <td colspan="3" class="text-center">No has creado ninguna caja física todavía. Haz clic en el botón de arriba para agregar la primera.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($puntos_caja as $caja): ?>
                    <tr>
                        <td><?php echo $caja['id']; ?></td>
                        <td><?php echo htmlspecialchars($caja['nombre']); ?></td>
                        <td><span class="badge bg-<?php echo $caja['estado'] == 'activo' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($caja['estado']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>