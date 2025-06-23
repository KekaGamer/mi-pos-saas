<?php
// editar_usuario.php -- VERSIÓN FINAL CON PERMISOS
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

// Seguridad: Solo un admin puede entrar aquí
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}
// Obtenemos el ID del usuario a editar desde la URL
if (!isset($_GET['id'])) {
    header("Location: /usuarios.php"); exit();
}

$id_usuario_a_editar = $_GET['id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id_usuario_a_editar]);
    $usuario = $stmt->fetch();
    if (!$usuario) { header("Location: /usuarios.php"); exit(); }
} catch (PDOException $e) { die("Error al buscar el usuario: " . $e->getMessage()); }
?>

<h1 class="mb-4">Editar Usuario: <?php echo htmlspecialchars($usuario['nombre']); ?></h1>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-secondary text-white p-4">
            <form action="src/controllers/actualizar_usuario.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                
                <div class="mb-3"><label for="nombre" class="form-label">Nombre Completo</label><input type="text" class="form-control form-control-dark" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required></div>
                <div class="mb-3"><label for="email" class="form-label">Correo Electrónico</label><input type="email" class="form-control form-control-dark" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required></div>
                <div class="mb-3"><label for="password" class="form-label">Nueva Contraseña (dejar en blanco para no cambiar)</label><input type="password" class="form-control form-control-dark" id="password" name="password"></div>
                
                <div class="mb-3">
                    <label for="rol" class="form-label">Rol del Usuario</label>
                    <select class="form-select form-control-dark" id="rol" name="rol" <?php echo ($usuario['id'] == 1) ? 'disabled' : ''; ?>>
                         <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == 1): // Solo el Super Admin (ID 1) puede ver y asignar el rol de Admin ?>
                            <option value="admin" <?php echo ($usuario['rol'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                        <?php elseif ($usuario['rol'] == 'admin'): // Si un admin normal edita a otro admin, la opción debe aparecer seleccionada pero no podrá cambiarla a otro usuario ?>
                            <option value="admin" selected>Administrador</option>
                        <?php endif; ?>
                        <option value="cajero" <?php echo ($usuario['rol'] == 'cajero') ? 'selected' : ''; ?>>Cajero</option>
                        <option value="contador" <?php echo ($usuario['rol'] == 'contador') ? 'selected' : ''; ?>>Contador</option>
                        <option value="proveedor" <?php echo ($usuario['rol'] == 'proveedor') ? 'selected' : ''; ?>>Proveedor</option>
                    </select>
                    <?php if ($usuario['id'] == 1): ?>
                        <small class="form-text">El rol del Super Administrador no se puede cambiar.</small>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="usuarios.php" class="btn btn-dark me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>