<?php
// agregar_producto.php -- VERSIÓN COMPLETA Y ACTUALIZADA
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/../../config/database.php';
if ($_SESSION['user_rol'] != 'admin') { /* ... */ }

// Obtenemos las categorías para el selector
$categorias = $pdo->query("SELECT * FROM categorias WHERE negocio_id = 1 ORDER BY nombre ASC")->fetchAll();
?>
<h1 class="mb-4">Agregar Nuevo Producto</h1>
<div class="card bg-secondary text-white p-4">
    <form action="../../src/controllers/guardar_producto.php" method="POST">
        <div class="row">
            <div class="col-md-6 mb-3"><label for="nombre_producto" class="form-label">Nombre del Producto</label><input type="text" class="form-control form-control-dark" id="nombre_producto" name="nombre_producto" required></div>
            <div class="col-md-6 mb-3">
                <label for="categoria_id" class="form-label">Categoría</label>
                <select name="categoria_id" id="categoria_id" class="form-select form-control-dark">
                    <option value="">(Sin categoría)</option>
                    <?php foreach($categorias as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-3"><label for="codigo_barra" class="form-label">Código de Barra</label><input type="text" class="form-control form-control-dark" id="codigo_barra" name="codigo_barra"></div><hr><h4 class="mt-4 mb-3">Precios y Ganancia</h4><div class="row align-items-center"><div class="col-md-4"><div class="mb-3"><label for="precio_costo" class="form-label">Precio de Costo ($)</label><input type="number" class="form-control form-control-dark" id="precio_costo" name="precio_costo" step="0.01" required></div></div><div class="col-md-4"><div class="mb-3"><label for="margen_ganancia" class="form-label">Margen de Ganancia (%)</label><div class="input-group"><input type="number" class="form-control form-control-dark" id="margen_ganancia" step="1" placeholder="Ej: 30"><span class="input-group-text">%</span></div></div></div><div class="col-md-4"><div class="mb-3"><label for="precio_venta" class="form-label">Precio de Venta ($)</label><input type="number" class="form-control form-control-dark" id="precio_venta" name="precio_venta" step="0.01" required><small id="ganancia-calculada-info" class="form-text text-white-50"></small></div></div></div><hr><h4 class="mt-4 mb-3">Inventario e Impuestos Específicos</h4><div class="row"><div class="col-md-4 mb-3"><label for="stock" class="form-label">Cantidad en Stock</label><input type="number" class="form-control form-control-dark" id="stock" name="stock" required></div><div class="col-md-4 mb-3"><label for="stock_minimo" class="form-label">Stock Mínimo para Alertas</label><input type="number" class="form-control form-control-dark" id="stock_minimo" name="stock_minimo" value="5"></div><div class="col-md-4 mb-3"><label for="impuesto_adicional" class="form-label">Impuesto Adicional Fijo (CLP)</label><input type="number" class="form-control form-control-dark" id="impuesto_adicional" name="impuesto_adicional" step="1" placeholder="Ej: 150 para cigarrillos"></div></div><div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4"><a href="/index.php" class="btn btn-dark me-2">Cancelar</a><button type="submit" class="btn btn-primary">Guardar Producto</button></div>
    </form>
</div>
<script>
// ...
</script>
<?php require_once __DIR__ . '/partials/footer.php';

// Seguridad: Solo el admin puede acceder
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado. Esta función es solo para administradores.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}
?>

<h1 class="mb-4">Agregar Nuevo Producto</h1>
<div class="card bg-secondary text-white p-4">
    <form action="src/controllers/guardar_producto.php" method="POST">
        
        <div class="row">
            <div class="col-md-8 mb-3"><label for="nombre_producto" class="form-label">Nombre del Producto</label><input type="text" class="form-control form-control-dark" id="nombre_producto" name="nombre_producto" required></div>
            <div class="col-md-4 mb-3"><label for="codigo_barra" class="form-label">Código de Barra</label><input type="text" class="form-control form-control-dark" id="codigo_barra" name="codigo_barra"></div>
        </div>
        
        <hr>
        <h4 class="mt-4 mb-3">Precios y Ganancia</h4>

        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="precio_costo" class="form-label">Precio de Costo ($)</label>
                    <input type="number" class="form-control form-control-dark" id="precio_costo" name="precio_costo" step="0.01" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="margen_ganancia" class="form-label">Margen de Ganancia (%)</label>
                    <div class="input-group">
                        <input type="number" class="form-control form-control-dark" id="margen_ganancia" step="1" placeholder="Ej: 30">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="precio_venta" class="form-label">Precio de Venta ($)</label>
                    <input type="number" class="form-control form-control-dark" id="precio_venta" name="precio_venta" step="0.01" required>
                    <small id="ganancia-calculada-info" class="form-text text-white-50"></small>
                </div>
            </div>
        </div>

        <hr>
        <h4 class="mt-4 mb-3">Inventario e Impuestos Específicos</h4>
        
        <div class="row">
             <div class="col-md-4 mb-3">
                <label for="stock" class="form-label">Cantidad en Stock</label>
                <input type="number" class="form-control form-control-dark" id="stock" name="stock" required>
             </div>
             <div class="col-md-4 mb-3">
                <label for="stock_minimo" class="form-label">Stock Mínimo para Alertas</label>
                <input type="number" class="form-control form-control-dark" id="stock_minimo" name="stock_minimo" value="5">
             </div>
             <div class="col-md-4 mb-3">
                <label for="impuesto_adicional" class="form-label">Impuesto Adicional Fijo (CLP)</label>
                <input type="number" class="form-control form-control-dark" id="impuesto_adicional" name="impuesto_adicional" step="1" placeholder="Ej: 150 para cigarrillos">
             </div>
        </div>
        
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <a href="/index.php" class="btn btn-dark me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Producto</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const costoInput = document.getElementById('precio_costo');
    const ventaInput = document.getElementById('precio_venta');
    const margenInput = document.getElementById('margen_ganancia');
    const gananciaInfo = document.getElementById('ganancia-calculada-info');

    function calcularVenta() {
        const costo = parseFloat(costoInput.value);
        const margen = parseFloat(margenInput.value);
        if (!isNaN(costo) && !isNaN(margen) && costo > 0) {
            const precioVenta = costo * (1 + (margen / 100));
            ventaInput.value = precioVenta.toFixed(2);
            actualizarInfoGanancia();
        }
    }

    function calcularMargen() {
        const costo = parseFloat(costoInput.value);
        const venta = parseFloat(ventaInput.value);
        if (!isNaN(costo) && !isNaN(venta) && costo > 0) {
            const margen = ((venta / costo) - 1) * 100;
            margenInput.value = margen.toFixed(2);
            actualizarInfoGanancia();
        } else {
            margenInput.value = '';
        }
    }
    
    function actualizarInfoGanancia() {
        const costo = parseFloat(costoInput.value);
        const venta = parseFloat(ventaInput.value);
        if (!isNaN(costo) && !isNaN(venta) && costo > 0) {
             const margen = ((venta / costo) - 1) * 100;
             gananciaInfo.innerText = `Ganancia actual: ${margen.toFixed(1)}%`;
        } else {
             gananciaInfo.innerText = '';
        }
    }

    costoInput.addEventListener('input', calcularVenta);
    margenInput.addEventListener('input', calcularVenta);
    ventaInput.addEventListener('input', calcularMargen);
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>