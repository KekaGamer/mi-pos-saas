<?php
// abrir_caja.php -- VERSIÓN CON SELECTOR DE CAJA
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_rol'], ['admin', 'cajero'])) {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}
if (isset($_SESSION['caja_sesion_id'])) {
    header("Location: /ventas.php"); exit();
}

// Obtenemos las cajas físicas activas para el selector
$cajas_fisicas = $pdo->query("SELECT * FROM puntos_caja WHERE estado = 'activo' AND negocio_id = 1")->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="text-center mb-4">Abrir Caja</h1>
        <div class="card bg-secondary text-white p-4">
            <form action="src/controllers/procesar_apertura.php" method="POST">
                <div class="mb-3">
                    <label for="punto_caja_id" class="form-label fs-5">¿En qué caja estás trabajando?</label>
                    <select class="form-select form-select-lg form-control-dark" name="punto_caja_id" id="punto_caja_id" required>
                        <option value="">Selecciona una caja...</option>
                        <?php foreach($cajas_fisicas as $caja): ?>
                            <option value="<?php echo $caja['id']; ?>"><?php echo htmlspecialchars($caja['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="monto_apertura" class="form-label fs-5">Monto Inicial en Caja (Fondo)</label>
                    <div class="input-group"><span class="input-group-text">$</span><input type="number" class="form-control form-control-lg" id="monto_apertura" name="monto_apertura" step="1" required></div>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Iniciar Turno</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>