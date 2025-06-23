<?php
// reportes.php -- VERSIÓN FINAL CON ALERTA COLAPSABLE
require_once __DIR__ . '/src/views/partials/header.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_rol'], ['admin', 'contador'])) {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php';
    exit();
}

require_once __DIR__ . '/config/database.php';

// La lógica para obtener los productos con stock bajo no cambia
try {
    $stmtStock = $pdo->prepare("SELECT id, nombre, stock, stock_minimo FROM productos WHERE stock <= stock_minimo AND negocio_id = 1 ORDER BY stock ASC");
    $stmtStock->execute();
    $productos_bajos_stock = $stmtStock->fetchAll();
} catch (PDOException $e) {
    $productos_bajos_stock = [];
    $error_stock = "Error al cargar alertas de stock.";
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">Dashboard de Reportes</h1>
    <div class="btn-group" role="group" aria-label="Filtros de tiempo">
        <button type="button" class="btn btn-primary active" data-periodo="day">Hoy</button>
        <button type="button" class="btn btn-primary" data-periodo="week">Semana</button>
        <button type="button" class="btn btn-primary" data-periodo="month">Mes</button>
        <button type="button" class="btn btn-primary" data-periodo="year">Año</button>
    </div>
</div>

<?php if (!empty($productos_bajos_stock)): ?>
<div class="alert alert-danger" role="alert">
    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> ¡Alerta de Inventario Bajo!</h4>
    <p>Tienes <?php echo count($productos_bajos_stock); ?> producto(s) en su nivel mínimo de stock o por debajo. Considera hacer un nuevo pedido.
       <a class="alert-link fw-bold" data-bs-toggle="collapse" href="#listaStockBajo" role="button" aria-expanded="false" aria-controls="listaStockBajo">
           (Ver / Ocultar Lista)
       </a>
    </p>

    <div class="collapse" id="listaStockBajo">
        <hr>
        <ul>
            <?php foreach ($productos_bajos_stock as $producto): ?>
                <li>
                    <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong> - 
                    Stock Actual: <?php echo $producto['stock']; ?> 
                    (Mínimo: <?php echo $producto['stock_minimo']; ?>)
                    <a href="src/views/editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-sm btn-outline-danger ms-2">Revisar</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-secondary text-white text-center">
            <div class="card-body">
                <h5 class="card-title">Ventas Totales (Hoy)</h5>
                <p id="kpi-ventas-hoy" class="card-text fs-2 fw-bold">$0</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-secondary text-white text-center">
            <div class="card-body">
                <h5 class="card-title">N° de Transacciones (Hoy)</h5>
                <p id="kpi-transacciones-hoy" class="card-text fs-2 fw-bold">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-secondary text-white text-center">
            <div class="card-body">
                <h5 class="card-title">Ticket Promedio (Hoy)</h5>
                <p id="kpi-ticket-promedio" class="card-text fs-2 fw-bold">$0</p>
            </div>
        </div>
    </div>
</div>

<div class="card bg-secondary">
    <div class="card-body">
        <canvas id="salesChart"></canvas>
    </div>
</div>

<script>
// El script de Chart.js no necesita cambios
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    let salesChartInstance;
    const kpiVentas = document.getElementById('kpi-ventas-hoy');
    const kpiTransacciones = document.getElementById('kpi-transacciones-hoy');
    const kpiTicketPromedio = document.getElementById('kpi-ticket-promedio');

    async function fetchAndRenderData(periodo) {
        try {
            const response = await fetch(`/api/api_ventas.php?periodo=${periodo}`);
            if (!response.ok) throw new Error('Error al obtener los datos de la API');
            const data = await response.json();

            kpiVentas.textContent = `$${data.kpi.totalVentas.toLocaleString('es-CL')}`;
            kpiTransacciones.textContent = data.kpi.numTransacciones;
            kpiTicketPromedio.textContent = `$${data.kpi.ticketPromedio.toLocaleString('es-CL')}`;

            if (salesChartInstance) salesChartInstance.destroy();
            
            salesChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.chart.labels,
                    datasets: [{
                        label: 'Total de Ventas',
                        data: data.chart.data,
                        backgroundColor: 'rgba(91, 110, 225, 0.7)',
                        borderColor: 'rgba(91, 110, 225, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: { y: { beginAtZero: true, ticks: { callback: value => '$' + value.toLocaleString('es-CL') } } },
                    plugins: { legend: { display: false }, tooltip: { callbacks: { label: context => 'Ventas: $' + context.parsed.y.toLocaleString('es-CL') } } }
                }
            });
        } catch (error) {
            console.error('Error:', error);
        }
    }

    const filterButtons = document.querySelectorAll('.btn-group .btn');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            fetchAndRenderData(this.dataset.periodo);
        });
    });

    fetchAndRenderData('day');
});
</script>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>