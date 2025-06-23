<?php
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if ($_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}

$categorias = $pdo->query("SELECT * FROM categorias WHERE negocio_id = 1 ORDER BY nombre ASC")->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Gestión de Categorías de Productos</h1>
    <a href="agregar_categoria.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Crear Categoría</a>
</div>
<div class="card bg-secondary">
    <div class="card-body">
        <table class="table table-dark table-striped table-hover">
            <thead><tr><th>ID</th><th>Nombre de la Categoría</th><th>Acciones</th></tr></thead>
            <tbody>
                <?php foreach ($categorias as $categoria): ?>
                <tr>
                    <td><?php echo $categoria['id']; ?></td>
                    <td><?php echo htmlspecialchars($categoria['nombre']); ?></td>
                    <td></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>