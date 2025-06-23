<?php
// promociones.php
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

// Seguridad: solo el admin puede ver esta página
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}

try {
    $promociones = $pdo->query("SELECT * FROM promociones WHERE negocio_id = 1 ORDER BY nombre ASC")->fetchAll();
} catch (PDOException $e) {
    // Si da un error aquí, es probable que las tablas de promociones no se hayan creado.
    // Revisa los pasos anteriores donde creamos las tablas `promociones` y `promocion_productos`.
    die("Error al cargar las promociones: " . $e->getMessage() . ". Asegúrate de haber ejecutado los comandos SQL para crear las tablas de promociones.");
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Gestión de Packs y Promociones</h1>
    <a href="agregar_promocion.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Crear Pack/Promo</a>
</div>

<div class="card bg-secondary">
    <div class="card-body">
        <table class="table table-dark table-striped table-hover">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($promociones)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Aún no has creado ninguna promoción o pack.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($promociones as $promo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($promo['nombre']); ?></td>
                        <td><?php echo $promo['tipo'] == 'pack_precio_fijo' ? 'Pack Precio Fijo' : 'Descuento %'; ?></td>
                        <td>$<?php echo number_format($promo['valor'], 0, ',', '.'); ?></td>
                        <td><span class="badge bg-<?php echo $promo['estado'] == 'activa' ? 'success' : 'danger'; ?>"><?php echo ucfirst($promo['estado']); ?></span></td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>