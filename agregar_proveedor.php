<?php
// agregar_proveedor.php
require_once __DIR__ . '/src/views/partials/header.php';
if (!in_array($_SESSION['user_rol'], ['admin', 'contador'])) {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}
?>

<h1 class="mb-4">Agregar Nuevo Proveedor</h1>
<div class="card bg-secondary text-white p-4">
    <form action="src/controllers/guardar_proveedor.php" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Empresa Proveedora</label>
            <input type="text" class="form-control form-control-dark" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email de Contacto</label>
            <input type="email" class="form-control form-control-dark" id="email" name="email">
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control form-control-dark" id="telefono" name="telefono">
        </div>
        <div class="d-flex justify-content-end">
            <a href="proveedores.php" class="btn btn-secondary me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Proveedor</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>