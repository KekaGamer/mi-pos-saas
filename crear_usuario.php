<?php
// crear_usuario.php -- VERSIÓN MEJORADA CON MENSAJE DE ÉXITO
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

// Seguridad
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado. Esta función es solo para administradores.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}

$proveedores = $pdo->query("SELECT id, nombre FROM proveedores ORDER BY nombre ASC")->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="text-center mb-4">Crear Nuevo Usuario</h1>

        <?php if (isset($_GET['exito'])): ?>
            <div class="alert alert-success">
                ¡Usuario creado exitosamente! Puedes crear otro o <a href="usuarios.php" class="alert-link">volver a la lista de usuarios</a>.
            </div>
        <?php endif; ?>

        <div class="card bg-secondary text-white p-4">
            <form id="form-registro" action="src/controllers/guardar_usuario.php" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control form-control-dark" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control form-control-dark" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control form-control-dark" id="password" name="password" required>
                </div>
                
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol del Usuario</label>
                    <select class="form-select form-control-dark" id="rol" name="rol" required>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
                            <option value="admin">Administrador</option>
                        <?php endif; ?>
                        <option value="cajero">Cajero</option>
                        <option value="contador">Contador</option>
                        <option value="proveedor">Proveedor</option>
                    </select>
                </div>

                <div class="mb-3" id="campo-proveedor" style="display: none;">
                    <label for="proveedor_id" class="form-label">Asociar a la Empresa Proveedora</label>
                    <select class="form-select form-control-dark" id="proveedor_id" name="proveedor_id">
                        <option value="">Seleccione una empresa...</option>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?php echo $proveedor['id']; ?>"><?php echo htmlspecialchars($proveedor['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-grid"><button type="submit" class="btn btn-primary btn-lg">Crear Usuario</button></div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('rol').addEventListener('change', function() {
    const campoProveedor = document.getElementById('campo-proveedor');
    const selectProveedor = document.getElementById('proveedor_id');
    if (this.value === 'proveedor') {
        campoProveedor.style.display = 'block';
        selectProveedor.setAttribute('required', 'required');
    } else {
        campoProveedor.style.display = 'none';
        selectProveedor.removeAttribute('required');
    }
});
</script>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>