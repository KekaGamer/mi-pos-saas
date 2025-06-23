<?php
// configuracion.php -- VERSIÓN FINAL CON ACCIONES MASIVAS
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if ($_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM negocios WHERE id = ?");
    $stmt->execute([1]);
    $negocio = $stmt->fetch();
} catch (PDOException $e) { die("Error al cargar la configuración: " . $e->getMessage()); }
?>

<h1 class="mb-4">Configuración del Negocio e Impuestos</h1>

<?php if (isset($_GET['exito'])): ?>
    <div class="alert alert-success">Configuración guardada exitosamente.</div>
<?php endif; ?>
<?php if (isset($_GET['exito_masivo'])): ?>
    <div class="alert alert-success">¡Éxito! Se han actualizado los precios de venta de <strong><?php echo htmlspecialchars($_GET['afectados']); ?></strong> productos según la configuración actual.</div>
<?php endif; ?>


<div class="card bg-secondary text-white p-4">
    <form action="src/controllers/actualizar_configuracion.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="negocio_id" value="<?php echo $negocio['id']; ?>"><h4 class="text-info">Datos Generales</h4><div class="row"><div class="col-md-8"><div class="mb-3"><label for="nombre_local" class="form-label">Nombre del Local</label><input type="text" class="form-control form-control-dark" id="nombre_local" name="nombre_local" value="<?php echo htmlspecialchars($negocio['nombre_local']); ?>"></div><div class="mb-3"><label for="direccion" class="form-label">Dirección</label><input type="text" class="form-control form-control-dark" id="direccion" name="direccion" value="<?php echo htmlspecialchars($negocio['direccion']); ?>"></div><div class="mb-3"><label for="telefono" class="form-label">Teléfono</label><input type="text" class="form-control form-control-dark" id="telefono" name="telefono" value="<?php echo htmlspecialchars($negocio['telefono']); ?>"></div></div><div class="col-md-4 text-center"><label class="form-label">Logo Actual</label><div><?php if (!empty($negocio['logo_url'])): ?><img src="<?php echo htmlspecialchars($negocio['logo_url']); ?>" alt="Logo Actual" class="img-fluid rounded mb-2" style="max-height: 150px;"><?php else: ?><div class="border rounded bg-dark d-flex align-items-center justify-content-center mb-2" style="height: 150px;"><span>Sin logo</span></div><?php endif; ?></div><label for="logo" class="form-label">Subir Nuevo Logo</label><input class="form-control form-control-dark" type="file" id="logo" name="logo"></div></div><hr class="my-4"><h4 class="text-info">Precios e Impuestos</h4><div class="row"><div class="col-md-4"><label for="ganancia" class="form-label">Porcentaje de Ganancia por Defecto (%)</label><input type="number" class="form-control form-control-dark" id="ganancia" name="porcentaje_ganancia_defecto" value="<?php echo htmlspecialchars($negocio['porcentaje_ganancia_defecto']); ?>" step="1"></div><div class="col-md-4"><label for="iva_porcentaje" class="form-label">Porcentaje de IVA (%)</label><input type="number" class="form-control form-control-dark" id="iva_porcentaje" name="iva_porcentaje" value="<?php echo htmlspecialchars($negocio['iva_porcentaje']); ?>" step="0.01"></div><div class="col-md-4"><label class="form-label">Cálculo de Precios</label><div class="form-check"><input class="form-check-input" type="radio" name="precios_incluyen_iva" id="iva_incluido" value="1" <?php echo ($negocio['precios_incluyen_iva'] == 1) ? 'checked' : ''; ?>><label class="form-check-label" for="iva_incluido">Precios de Venta YA incluyen IVA</label></div><div class="form-check"><input class="form-check-input" type="radio" name="precios_incluyen_iva" id="iva_agregado" value="0" <?php echo ($negocio['precios_incluyen_iva'] == 0) ? 'checked' : ''; ?>><label class="form-check-label" for="iva_agregado">Sumar el IVA al precio de venta</label></div></div></div><div class="d-grid mt-4"><button type="submit" class="btn btn-primary btn-lg">Guardar Configuración</button></div>
    </form>
</div>

<div class="card bg-danger-subtle mt-4">
    <div class="card-header text-danger-emphasis fw-bold">
        <i class="bi bi-exclamation-triangle-fill"></i> Zona de Acciones Masivas
    </div>
    <div class="card-body">
        <h5 class="card-title">Aplicar Precios a Todo el Inventario</h5>
        <p class="card-text">Esta acción recalculará el **Precio de Venta** de **TODOS** los productos en tu inventario basándose en su precio de costo y en la configuración de "Porcentaje de Ganancia" e "IVA" que hayas guardado arriba. Esta acción no se puede deshacer.</p>
        <form action="src/controllers/aplicar_precios_masivo.php" method="POST" onsubmit="return confirm('¿Estás absolutamente seguro? Esta acción sobrescribirá TODOS los precios de venta de tu inventario. Esta acción no se puede deshacer.');">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-calculator-fill"></i> Aplicar Precios a Todo el Inventario
            </button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>