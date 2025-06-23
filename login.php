<?php
// login.php -- VERSIÓN SIN REGISTRO PÚBLICO
require_once __DIR__ . '/src/views/partials/header.php';

$error = $_GET['error'] ?? null;
$registro_exitoso = isset($_GET['registro']);
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <h1 class="text-center mb-4">Iniciar Sesión</h1>
        <?php if ($error): ?><div class="alert alert-danger">Correo o contraseña incorrectos.</div><?php endif; ?>
        <?php if ($registro_exitoso): ?><div class="alert alert-success">¡Usuario creado exitosamente!</div><?php endif; ?>
        <div class="card bg-secondary text-white p-4">
            <div class="card-body">
                <form action="src/controllers/procesar_login.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control form-control-dark" id="email" name="email" required>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control form-control-dark" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
</div>

<?php
require_once __DIR__ . '/src/views/partials/footer.php';
?>