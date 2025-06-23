<?php
// agregar_promocion.php -- VERSIÓN MEJORADA Y DINÁMICA
require_once __DIR__ . '/src/views/partials/header.php';
require_once __DIR__ . '/config/database.php';

if ($_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}

$productos = $pdo->query("SELECT id, nombre FROM productos WHERE negocio_id = 1 ORDER BY nombre ASC")->fetchAll();
?>

<h1 class="mb-4">Crear Nuevo Pack de Precio Fijo</h1>
<div class="card bg-secondary text-white p-4">
    <form id="form-promocion" action="src/controllers/guardar_promocion.php" method="POST">
        <input type="hidden" name="tipo" value="pack_precio_fijo">
        
        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="nombre" class="form-label">Nombre del Pack (ej: Combo Almuerzo, Six-Pack...)</label>
                <input type="text" class="form-control form-control-dark" id="nombre" name="nombre" required>
            </div>
            <div class="col-md-4 mb-3">
                <label for="valor" class="form-label">Precio Fijo de Venta del Pack ($)</label>
                <input type="number" class="form-control form-control-dark" id="valor" name="valor" step="1" required>
            </div>
        </div>
        
        <hr>
        <h4 class="mt-4">Productos Incluidos en el Pack</h4>
        
        <div class="row g-3 align-items-end mb-3">
            <div class="col-md-6">
                <label for="producto-selector" class="form-label">Seleccionar Producto</label>
                <select id="producto-selector" class="form-select form-control-dark">
                    <option value="">-- Elige un producto --</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?php echo $producto['id']; ?>" data-nombre="<?php echo htmlspecialchars($producto['nombre']); ?>"><?php echo htmlspecialchars($producto['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                 <label for="producto-cantidad" class="form-label">Cantidad</label>
                <input type="number" id="producto-cantidad" class="form-control form-control-dark" value="1" min="1">
            </div>
            <div class="col-md-3">
                <button type="button" id="btn-agregar-producto" class="btn btn-info w-100">Añadir Producto al Pack</button>
            </div>
        </div>

        <table class="table table-dark">
            <thead>
                <tr><th>Producto</th><th>Cantidad</th><th>Acción</th></tr>
            </thead>
            <tbody id="productos-en-pack">
                </tbody>
        </table>
        
        <div class="d-flex justify-content-end mt-4">
            <a href="promociones.php" class="btn btn-dark me-2">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Pack</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selector = document.getElementById('producto-selector');
    const cantidadInput = document.getElementById('producto-cantidad');
    const btnAgregar = document.getElementById('btn-agregar-producto');
    const tablaBody = document.getElementById('productos-en-pack');
    const form = document.getElementById('form-promocion');

    btnAgregar.addEventListener('click', function() {
        const selectedOption = selector.options[selector.selectedIndex];
        if (!selectedOption.value) return; // No hacer nada si no hay selección

        const productoId = selectedOption.value;
        const productoNombre = selectedOption.dataset.nombre;
        const cantidad = cantidadInput.value;

        // Crear la fila de la tabla para visualización
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${productoNombre}</td>
            <td>${cantidad}</td>
            <td><button type="button" class="btn btn-sm btn-danger btn-remover">Quitar</button></td>
        `;
        tablaBody.appendChild(newRow);

        // Crear los inputs ocultos que se enviarán con el formulario
        const hiddenIdInput = document.createElement('input');
        hiddenIdInput.type = 'hidden';
        hiddenIdInput.name = 'producto_ids[]';
        hiddenIdInput.value = productoId;
        form.appendChild(hiddenIdInput);
        
        const hiddenCantidadInput = document.createElement('input');
        hiddenCantidadInput.type = 'hidden';
        hiddenCantidadInput.name = 'cantidades[]';
        hiddenCantidadInput.value = cantidad;
        form.appendChild(hiddenCantidadInput);

        // Asociar el botón de remover con los inputs ocultos
        newRow.querySelector('.btn-remover').addEventListener('click', function() {
            newRow.remove();
            hiddenIdInput.remove();
            hiddenCantidadInput.remove();
        });
    });
});
</script>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>