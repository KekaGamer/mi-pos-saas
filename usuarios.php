<?php
// usuarios.php -- VERSIÓN CON EDICIÓN PARA TODOS
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

// Seguridad
if (!isset($_SESSION['user_rol']) || $_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}

try {
    $stmt = $pdo->query("SELECT id, nombre, email, rol, activo FROM usuarios WHERE negocio_id = 1 ORDER BY nombre ASC");
    $usuarios = $stmt->fetchAll();
} catch (PDOException $e) { die("Error al cargar los usuarios: " . $e->getMessage()); }
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Gestión de Usuarios</h1>
    <a href="crear_usuario.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Crear Nuevo Usuario</a>
</div>

<div class="card bg-secondary">
    <div class="card-body">
        <table class="table table-dark table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario['id']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($usuario['nombre']); ?>
                        <?php if ($usuario['id'] == 1): ?>
                            <span class="badge bg-info">Super Admin</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo ucfirst($usuario['rol']); ?></td>
                    <td><span class="badge bg-<?php echo $usuario['activo'] ? 'success' : 'danger'; ?>"><?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?></span></td>
                    <td class="text-center">
                        <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil-fill"></i> Editar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>