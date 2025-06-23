<?php
// importar.php -- VERSIÓN CON INSTRUCCIONES CORREGIDAS
require_once __DIR__ . '/src/views/partials/header.php';

if ($_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}
?>

<h1 class="mb-4">Importar Inventario desde CSV</h1>
<div class="row">
    <div class="col-md-6">
        <div class="card bg-secondary text-white p-4">
            <h4>Subir Archivo</h4><hr>
            <form action="src/controllers/procesar_importacion.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="csv_file" class="form-label">Seleccione el archivo CSV</label>
                    <input class="form-control form-control-dark" type="file" id="csv_file" name="csv_file" accept=".csv" required>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="index.php" class="btn btn-dark me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> Iniciar Importación</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-dark text-white p-4">
            <h4><i class="bi bi-info-circle-fill"></i> Instrucciones Importantes</h4><hr>
            <p>El archivo CSV debe tener las siguientes columnas **en este orden exacto**:</p>
            <ol>
                <li><strong>Nombre</strong> (Texto, obligatorio)</li>
                <li><strong>Categoria</strong> (Texto, si la categoría no existe, se creará automáticamente)</li>
                <li><strong>Codigo de Barra</strong> (Único. Si existe, se actualiza el producto. Si se deja en blanco, se crea un producto nuevo)</li>
                <li><strong>Precio Costo</strong> (Numérico, obligatorio)</li>
                <li><strong>Precio Venta</strong> (Numérico, obligatorio)</li>
                <li><strong>Stock</strong> (Numérico, obligatorio)</li>
                <li><strong>Stock Minimo</strong> (Numérico, opcional)</li>
                <li><strong>Impuesto Adicional</strong> (Numérico, opcional)</li>
            </ol>
            <p><strong>Recomendación:</strong> <a href="src/controllers/exportar_inventario.php" class="link-info">Descarga tu inventario actual</a> para usarlo como una plantilla perfecta.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>