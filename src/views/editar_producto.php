<?php
// src/views/editar_producto.php -- VERSIÓN COMPLETA Y ACTUALIZADA
require_once __DIR__ . '/../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') { die("Acceso denegado."); }
if (!isset($_GET['id'])) { header("Location: /index.php"); exit(); }

$id_producto = $_GET['id'];
try {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch();
    if (!$producto) { header("Location: /index.php"); exit(); }
} catch (PDOException $e) { die("Error al buscar el producto: " . $e->getMessage()); }

require_once __DIR__ . '/partials/header.php';
?>

<h1 class="mb-4">Editar Producto</h1>
<div class="card bg-secondary text-white p-4">
    <form action="../controllers/actualizar_producto.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
        
        <div class="row">
            <div class="col-md-8 mb-3"><label for="nombre_producto" class="form-label">Nombre del Producto</label><input type="text" class="form-control form-control-dark" id="nombre_producto" name="nombre_producto" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required></div>
            <div class="col-md-4 mb-3"><label for="codigo_barra" class="form-label">Código de Barra</label><input type="text" class="form-control form-control-dark" id="codigo_barra" name="codigo_barra" value="<?php echo htmlspecialchars($producto['codigo_barra']); ?>"></div>
        </div>

        <hr>
        <h4 class="mt-4 mb-3">Precios y Ganancia</h4>
        <div class="row align-items-center">
            <div class="col-md-4"><div class="mb-3"><label for="precio_costo" class="form-label">Precio de Costo ($)</label><input type="number" class="form-control form-control-dark" id="precio_costo" name="precio_costo" value="<?php echo $producto['precio_costo']; ?>" step="0.01" required></div></div>
            <div class="col-md-4"><div class="mb-3"><label for="margen_ganancia" class="form-label">Margen de Ganancia (%)</label><div class="input-group"><input type="number" class="form-control form-control-dark" id="margen_ganancia" step="1" placeholder="Ej: 30"><span class="input-group-text">%</span></div></div></div>
            <div class="col-md-4"><div class="mb-3"><label for="precio_venta" class="form-label">Precio de Venta ($)</label><input type="number" class="form-control form-control-dark" id="precio_venta" name="precio_venta" value="<?php echo $producto['precio_venta']; ?>" step="0.01" required><small id="ganancia-calculada-info" class="form-text text-white-50"></small></div></div>
        </div>

        <hr>
        <h4 class="mt-4 mb-3">Inventario e Impuestos Específicos</h4>
        <div class="row">
             <div class="col-md-4 mb-3"><label for="stock" class="form-label">Cantidad en Stock</label><input type="number" class="form-control form-control-dark" id="stock" name="stock" value="<?php echo $producto['stock']; ?>" required></div>
             <div class="col-md-4 mb-3"><label for="stock_minimo" class="form-label">Stock Mínimo</label><input type="number" class="form-control form-control-dark" id="stock_minimo" name="stock_minimo" value="<?php echo $producto['stock_minimo']; ?>"></div>
             <div class="col-md-4 mb-3"><label for="impuesto_adicional" class="form-label">Impuesto Adicional Fijo (CLP)</label><input type="number" class="form-control form-control-dark" id="impuesto_adicional" name="impuesto_adicional" value="<?php echo htmlspecialchars($producto['impuesto_adicional']); ?>" step="1" placeholder="Dejar en blanco si no aplica"></div>
        </div>
        <div class="mb-3"><label for="motivo_ajuste" class="form-label">Motivo del Ajuste de Stock (Opcional)</label><input type="text" class="form-control form-control-dark" id="motivo_ajuste" name="motivo_ajuste" placeholder="Ej: Conteo físico, producto dañado..."></div>
        
        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
            <a href="/index.php" class="btn btn-dark me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle-fill"></i> Actualizar Producto</button>
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

    // Llamada inicial al cargar la página para mostrar la ganancia del producto actual
    calcularMargen(); 
});
</script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>