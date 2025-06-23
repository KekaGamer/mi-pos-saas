<?php
require_once __DIR__ . '/src/views/partials/header.php';
if ($_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}
?>
<h1 class="mb-4">Crear Nueva Categoría</h1>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-secondary text-white p-4">
            <form action="src/controllers/guardar_categoria.php" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Categoría</label>
                    <input type="text" class="form-control form-control-dark" id="nombre" name="nombre" placeholder="Ej: Bebidas, Lácteos, Abarrotes" required>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="categorias.php" class="btn btn-dark me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>