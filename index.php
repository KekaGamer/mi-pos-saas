<?php
// index.php -- VERSIÓN FINAL CON BÚSQUEDA Y CATEGORÍAS
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- Bloque de Seguridad ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Solo el 'admin' puede ver la gestión de inventario
if ($_SESSION['user_rol'] != 'admin') {
    echo "<div class='alert alert-danger text-center'>Acceso denegado. Esta sección es solo para administradores.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}
// --- Fin del Bloque de Seguridad ---

require_once __DIR__ . '/config/database.php';

// --- NUEVA LÓGICA DE BÚSQUEDA Y FILTRADO ---
$search_term = $_GET['q'] ?? ''; // Obtenemos el término de búsqueda de la URL, si existe
$where_clause = '';
$params = [1]; // El negocio_id = 1 siempre es el primer parámetro

if (!empty($search_term)) {
    // Si hay un término de búsqueda, añadimos condiciones a la consulta
    $where_clause = "AND (p.nombre LIKE ? OR p.codigo_barra LIKE ?)";
    // Usamos '%' para que busque coincidencias parciales
    $params[] = "%$search_term%";
    $params[] = "%$search_term%";
}
// --- FIN DE LA LÓGICA DE BÚSQUEDA ---

$productos = [];
try {
    // --- CONSULTA SQL AHORA DINÁMICA ---
    // Se ha añadido un LEFT JOIN para obtener el nombre de la categoría
    // y se concatena la cláusula WHERE que construimos arriba.
    $sql = "SELECT p.*, c.nombre as nombre_categoria 
            FROM productos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.negocio_id = ? $where_clause 
            ORDER BY p.nombre ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll();

} catch (PDOException $e) {
    $error_message = "Error al obtener los productos: " . $e->getMessage();
}

require_once __DIR__ . '/src/views/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Inventario de Productos</h1>
    <div>
        <a href="importar.php" class="btn btn-info"><i class="bi bi-upload"></i> Importar</a>
        <a href="src/controllers/exportar_inventario.php" class="btn btn-secondary"><i class="bi bi-download"></i> Exportar</a>
        <a href="src/views/agregar_producto.php" class="btn btn-success"><i class="bi bi-plus-circle-fill"></i> Agregar</a>
    </div>
</div>

<?php if (isset($_GET['import_success'])): ?>
    <div class="alert alert-success">Importación exitosa: <?php echo htmlspecialchars($_GET['inserted']); ?> productos nuevos agregados, <?php echo htmlspecialchars($_GET['updated']); ?> productos actualizados.</div>
<?php endif; ?>
<?php if (isset($_GET['import_error'])): ?>
    <div class="alert alert-danger">Error durante la importación: <?php echo htmlspecialchars($_GET['import_error']); ?></div>
<?php endif; ?>

<div class="card bg-dark text-white p-3 mb-4">
    <form method="GET" action="index.php">
        <div class="input-group">
            <input type="text" class="form-control form-control-dark" name="q" placeholder="Buscar por nombre o código de barras..." value="<?php echo htmlspecialchars($search_term); ?>">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Buscar</button>
            <a href="index.php" class="btn btn-secondary" title="Limpiar búsqueda"><i class="bi bi-x-lg"></i></a>
        </div>
    </form>
</div>

<div class="card bg-secondary">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th> <th>Código de Barra</th>
                        <th>Precio Venta</th>
                        <th>Stock</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($productos)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No se encontraron productos que coincidan con la búsqueda.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['nombre_categoria'] ?? 'Sin categoría'); ?></td> <td><?php echo htmlspecialchars($producto['codigo_barra']); ?></td>
                            <td>$<?php echo number_format($producto['precio_venta'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                            <td class="text-center">
                                <a href="src/views/editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-primary" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                                <form action="src/controllers/eliminar_producto.php" method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?');" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/src/views/partials/footer.php';
?>