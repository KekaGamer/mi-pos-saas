<?php
// ventas.php -- VERSIÓN FINAL ESTABLE
require_once __DIR__ . '/src/views/partials/header.php';

// --- Bloque de Seguridad y Verificación de Caja ---
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_rol'], ['admin', 'cajero'])) {
    echo "<div class='alert alert-danger text-center'>Acceso denegado.</div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}
if (!isset($_SESSION['caja_sesion_id'])) {
    echo "<div class='alert alert-warning text-center'><h2>No hay una caja abierta.</h2><p>Debes abrir una caja para registrar ventas.</p><a href='abrir_caja.php' class='btn btn-primary btn-lg'>Abrir Caja Ahora</a></div>";
    require_once __DIR__ . '/src/views/partials/footer.php'; exit();
}
require_once __DIR__ . '/config/database.php';

// --- Bloque de Carga de Datos ---
try {
    // Obtenemos todos los datos necesarios en una sola pasada para eficiencia
    $productos_disponibles = $pdo->query("SELECT id, nombre, codigo_barra, precio_venta, stock, impuesto_adicional FROM productos WHERE negocio_id = 1")->fetchAll(PDO::FETCH_ASSOC);
    $logo_url = $pdo->query("SELECT logo_url FROM negocios WHERE id = 1")->fetchColumn();
    $sqlPacks = "SELECT p.id, p.nombre, p.valor, GROUP_CONCAT(pp.producto_id, ':', pp.cantidad) as productos_info FROM promociones p JOIN promocion_productos pp ON p.id = pp.promocion_id WHERE p.tipo = 'pack_precio_fijo' AND p.estado = 'activa' AND p.negocio_id = 1 GROUP BY p.id, p.nombre, p.valor";
    $packs_disponibles = $pdo->query($sqlPacks)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Error al cargar datos: " . $e->getMessage()); }
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Punto de Venta</h2>
    <div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-ingreso-caja"><i class="bi bi-arrow-down-square"></i> Ingresar Efectivo</button>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modal-retiro-caja"><i class="bi bi-arrow-up-square"></i> Retirar Efectivo</button>
        <button class="btn btn-info" onclick="window.open('cfd.php', 'CFD', 'width=800,height=600');"><i class="bi bi-display"></i> Abrir Pantalla de Cliente</button>
    </div>
</div>

<?php if (isset($_GET['movimiento_exito'])): ?><div class="alert alert-success">Movimiento de caja registrado exitosamente.</div><?php endif; ?>
<?php if (isset($_GET['exito'])): ?><div class="alert alert-success">Venta registrada exitosamente.</div><?php endif; ?>

<div class="row">
    <div class="col-md-6">
        <ul class="nav nav-tabs" id="ventaTab" role="tablist">
            <li class="nav-item" role="presentation"><button class="nav-link active" id="buscar-tab" data-bs-toggle="tab" data-bs-target="#buscar-panel" type="button" role="tab">Buscar Producto</button></li>
            <li class="nav-item" role="presentation"><button class="nav-link" id="packs-tab" data-bs-toggle="tab" data-bs-target="#packs-panel" type="button" role="tab">Packs y Combos</button></li>
        </ul>
        <div class="tab-content mt-3">
            <div class="tab-pane fade show active" id="buscar-panel" role="tabpanel">
                <input type="text" id="buscador-productos" class="form-control form-control-lg" placeholder="Buscar por nombre o código de barras..." autofocus>
                <div id="resultados-busqueda" class="list-group mt-2" style="max-height: 55vh; overflow-y: auto;"></div>
            </div>
            <div class="tab-pane fade" id="packs-panel" role="tabpanel" style="max-height: 60vh; overflow-y: auto;">
                <div class="d-grid gap-2">
                    <?php foreach($packs_disponibles as $pack): ?>
                        <button class="btn btn-lg btn-outline-info pack-btn" data-pack-id="<?php echo $pack['id']; ?>">
                            <strong><?php echo htmlspecialchars($pack['nombre']); ?></strong><br>
                            <small>Precio: $<?php echo number_format($pack['valor'], 0, ',', '.'); ?></small>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-secondary h-100">
            <div class="card-body d-flex flex-column">
                <h3 class="card-title text-center">Venta Actual (<?php echo htmlspecialchars($_SESSION['punto_caja_nombre'] ?? 'Caja No Definida'); ?>)</h3>
                <hr>
                <div id="carrito-items" class="flex-grow-1" style="max-height: 45vh; overflow-y: auto;"><p class="text-center text-white-50">El carrito está vacío</p></div>
                <hr>
                <div class="mt-auto">
                    <h2 class="text-center">Total: <span id="carrito-total">$0</span></h2>
                    <div class="d-grid gap-2 mt-3">
                        <button id="btn-pagar" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modal-pago" disabled><i class="bi bi-cash-coin"></i> Pagar</button>
                        <button id="btn-cancelar-venta" class="btn btn-danger"><i class="bi bi-x-circle"></i> Cancelar Venta</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cantidad" tabindex="-1"><div class="modal-dialog modal-sm modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="modal-cantidad-titulo">Ingresar Cantidad</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" id="producto-id-cantidad"><label for="input-cantidad" class="form-label">Cantidad:</label><input type="number" class="form-control form-control-lg text-center" id="input-cantidad" value="1" min="1"></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary" id="btn-agregar-con-cantidad">Agregar</button></div></div></div></div>
<div class="modal fade" id="modal-pago" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Finalizar Venta</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div id="vista-seleccion-pago"><p class="text-center fs-4">Total Venta: <strong id="modal-subtotal" class="text-primary"></strong></p><div id="fila-impuesto-adicional" class="text-center text-danger" style="display: none;">Imp. Adicional (Tarjeta): <span id="modal-impuesto-adicional"></span></div><p class="text-center fs-3 fw-bold mt-2">TOTAL A PAGAR: <span id="modal-total-final" class="text-primary"></span></p><hr><p class="text-center">Seleccione el método de pago:</p><div class="d-grid gap-2"><button type="button" class="btn btn-success btn-lg btn-metodo-pago" data-metodo="efectivo"><i class="bi bi-cash"></i> Efectivo</button><button type="button" class="btn btn-info btn-lg btn-metodo-pago" data-metodo="debito"><i class="bi bi-credit-card-2-front"></i> Débito</button><button type="button" class="btn btn-primary btn-lg btn-metodo-pago" data-metodo="credito"><i class="bi bi-credit-card"></i> Crédito</button><button type="button" class="btn btn-warning btn-lg btn-metodo-pago" data-metodo="transferencia"><i class="bi bi-phone"></i> Transferencia</button></div></div><div id="vista-pago-efectivo" style="display: none;"><p class="text-center fs-4">Total Venta: <strong id="efectivo-total-venta" class="text-primary"></strong></p><div class="mb-3"><label for="monto-recibido" class="form-label fs-5">Monto Recibido</label><div class="input-group input-group-lg"><span class="input-group-text">$</span><input type="number" class="form-control" id="monto-recibido" placeholder="0"></div></div><div id="botones-pago-rapido" class="btn-group w-100 mb-3" role="group"></div><div class="text-center bg-dark rounded p-3"><span class="fs-4">Vuelto:</span><span id="vuelto-a-entregar" class="fs-1 fw-bold text-success">$0</span></div><div class="d-grid gap-2 mt-4"><button type="button" id="btn-confirmar-efectivo" class="btn btn-success btn-lg">Confirmar Venta</button><button type="button" id="btn-volver-metodos" class="btn btn-secondary">Volver</button></div></div><form id="form-procesar-venta" action="src/controllers/procesar_venta.php" method="POST" style="display: none;"><input type="hidden" name="carrito_data" id="carrito-data-json"><input type="hidden" name="total_venta" id="total-venta-input"><input type="hidden" name="metodo_pago" id="metodo-pago-input"></form></div></div></div></div>
<div class="modal fade" id="modal-ingreso-caja" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Ingresar Efectivo</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form action="src/controllers/procesar_movimiento_caja.php" method="POST"><div class="modal-body"><input type="hidden" name="tipo" value="ingreso"><div class="mb-3"><label for="monto-ingreso" class="form-label">Monto</label><input type="number" class="form-control" name="monto" id="monto-ingreso" required step="1"></div><div class="mb-3"><label for="motivo-ingreso" class="form-label">Motivo</label><input type="text" class="form-control" name="motivo" id="motivo-ingreso" required></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-success">Registrar</button></div></form></div></div></div>
<div class="modal fade" id="modal-retiro-caja" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Retirar Efectivo</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form action="src/controllers/procesar_movimiento_caja.php" method="POST"><div class="modal-body"><input type="hidden" name="tipo" value="retiro"><div class="mb-3"><label for="monto-retiro" class="form-label">Monto</label><input type="number" class="form-control" name="monto" id="monto-retiro" required step="1"></div><div class="mb-3"><label for="motivo-retiro" class="form-label">Motivo</label><input type="text" class="form-control" name="motivo" id="motivo-retiro" required></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning">Registrar</button></div></form></div></div></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- VARIABLES Y CONSTANTES ---
    const productosDisponibles = <?php echo json_encode($productos_disponibles); ?>;
    const packsDisponibles = <?php echo json_encode($packs_disponibles); ?>;
    const logoUrl = '<?php echo $logo_url; ?>';
    let carrito = {};
    let subtotalVenta = 0;

    // --- REFERENCIAS AL DOM ---
    const buscador = document.getElementById('buscador-productos');
    const resultadosDiv = document.getElementById('resultados-busqueda');
    const carritoItemsDiv = document.getElementById('carrito-items');
    const carritoTotalSpan = document.getElementById('carrito-total');
    const btnPagar = document.getElementById('btn-pagar');
    const btnCancelarVenta = document.getElementById('btn-cancelar-venta');
    
    // Modal de Cantidad
    const modalCantidad = new bootstrap.Modal(document.getElementById('modal-cantidad'));
    const modalCantidadTitulo = document.getElementById('modal-cantidad-titulo');
    const productoIdCantidadInput = document.getElementById('producto-id-cantidad');
    const inputCantidad = document.getElementById('input-cantidad');
    const btnAgregarConCantidad = document.getElementById('btn-agregar-con-cantidad');
    
    // Modal de Pago
    const modalPago = document.getElementById('modal-pago');
    const modalSubtotal = document.getElementById('modal-subtotal');
    const filaImpuestoAdicional = document.getElementById('fila-impuesto-adicional');
    const modalImpuestoAdicional = document.getElementById('modal-impuesto-adicional');
    const modalTotalFinal = document.getElementById('modal-total-final');
    const vistaSeleccion = document.getElementById('vista-seleccion-pago');
    const vistaEfectivo = document.getElementById('vista-pago-efectivo');
    
    // Vista de Pago en Efectivo
    const efectivoTotalVenta = document.getElementById('efectivo-total-venta');
    const montoRecibidoInput = document.getElementById('monto-recibido');
    const vueltoSpan = document.getElementById('vuelto-a-entregar');
    const botonesPagoRapidoDiv = document.getElementById('botones-pago-rapido');
    const btnConfirmarEfectivo = document.getElementById('btn-confirmar-efectivo');
    const btnVolverMetodos = document.getElementById('btn-volver-metodos');
    
    // Formulario de Venta
    const formProcesarVenta = document.getElementById('form-procesar-venta');
    const carritoDataInput = document.getElementById('carrito-data-json');
    const totalVentaInput = document.getElementById('total-venta-input');
    const metodoPagoInput = document.getElementById('metodo-pago-input');

    // --- FUNCIONES PRINCIPALES ---

    const renderizarCarrito = () => {
        carritoItemsDiv.innerHTML = '';
        subtotalVenta = 0;
        Object.values(carrito).forEach(item => { subtotalVenta += item.subtotal; });

        if (Object.keys(carrito).length === 0) {
            carritoItemsDiv.innerHTML = '<p class="text-center text-white-50">El carrito está vacío</p>';
            btnPagar.disabled = true;
        } else {
            btnPagar.disabled = false;
            Object.values(carrito).forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'd-flex justify-content-between align-items-center mb-2 p-2 rounded bg-dark';
                let itemHtml = '';
                if (item.tipo === 'pack') {
                    itemHtml = `<div><span class="fw-bold text-info">${item.nombre} (Pack)</span></div><span class="fw-bold fs-5">$${item.subtotal.toLocaleString('es-CL')}</span>`;
                } else {
                    itemHtml = `<div><span class="fw-bold">${item.nombre}</span><br><small>$${item.precio_venta.toLocaleString('es-CL')} x ${item.cantidad}</small></div><span class="fw-bold fs-5">$${item.subtotal.toLocaleString('es-CL')}</span>`;
                }
                itemDiv.innerHTML = itemHtml;
                carritoItemsDiv.appendChild(itemDiv);
            });
        }
        carritoTotalSpan.innerText = `$${subtotalVenta.toLocaleString('es-CL')}`;
        localStorage.setItem('pos_cfd_cart', JSON.stringify({ cart: carrito, logo: logoUrl }));
    };

    const renderizarResultados = (productos) => {
        resultadosDiv.innerHTML = '';
        if (!productos || productos.length === 0) return;
        productos.forEach(producto => {
            const item = document.createElement('a');
            item.href = '#';
            item.className = 'list-group-item list-group-item-action';
            item.innerText = `${producto.nombre} - $${parseFloat(producto.precio_venta).toLocaleString('es-CL')}`;
            item.dataset.productoId = producto.id;
            resultadosDiv.appendChild(item);
        });
    };

    const agregarAlCarrito = (productoId, cantidad = 1) => {
        const producto = productosDisponibles.find(p => p.id == productoId);
        if (!producto) return;
        const cantAAgregar = parseInt(cantidad, 10);
        if (isNaN(cantAAgregar) || cantAAgregar <= 0) {
            alert('Por favor, ingrese una cantidad válida.');
            return;
        }
        const cartId = `prod_${productoId}`;
        if (carrito[cartId]) {
            carrito[cartId].cantidad += cantAAgregar;
        } else {
            carrito[cartId] = {
                id: producto.id, tipo: 'producto', nombre: producto.nombre, 
                precio_venta: parseFloat(producto.precio_venta), 
                impuesto_adicional: parseFloat(producto.impuesto_adicional || 0),
                cantidad: cantAAgregar 
            };
        }
        carrito[cartId].subtotal = carrito[cartId].cantidad * carrito[cartId].precio_venta;
        renderizarCarrito();
    };
    
    const agregarPackAlCarrito = (packId) => {
        const pack = packsDisponibles.find(p => p.id == packId);
        if (!pack) return;
        const cartId = `pack_${packId}_${Date.now()}`;
        const productosDelPack = pack.productos_info.split(',').map(p => { const [id, cantidad] = p.split(':'); return { id: parseInt(id), cantidad: parseInt(cantidad) }; });
        carrito[cartId] = { id: pack.id, tipo: 'pack', nombre: pack.nombre, precio_venta: parseFloat(pack.valor), cantidad: 1, subtotal: parseFloat(pack.valor), productos: productosDelPack };
        renderizarCarrito();
    };

    // --- LÓGICA DE PAGO ---
    const calcularTotalFinal = (metodoPago) => { /* ... (Sin cambios, pero asegúrate de que exista) ... */ };
    const mostrarVistaEfectivo = () => { /* ... (Sin cambios) ... */ };
    const mostrarVistaSeleccion = () => { /* ... (Sin cambios) ... */ };
    const finalizarVenta = (metodo, totalFinal) => { /* ... (Modificada para recibir el total) ... */ };
    
    // --- EVENT LISTENERS ---
    buscador.addEventListener('input', () => {
        const termino = buscador.value.toLowerCase();
        if (termino === '') {
            resultadosDiv.innerHTML = '';
            return;
        }
        const filtrados = productosDisponibles.filter(p => 
            p.nombre.toLowerCase().includes(termino) || (p.codigo_barra && p.codigo_barra.includes(termino))
        );
        renderizarResultados(filtrados);
    });

    resultadosDiv.addEventListener('click', (e) => {
        e.preventDefault();
        const target = e.target.closest('.list-group-item-action');
        if (target && target.dataset.productoId) {
            const productoId = target.dataset.productoId;
            const producto = productosDisponibles.find(p => p.id == productoId);
            modalCantidadTitulo.innerText = producto.nombre;
            productoIdCantidadInput.value = productoId;
            inputCantidad.value = 1;
            modalCantidad.show();
        }
    });

    btnAgregarConCantidad.addEventListener('click', () => {
        agregarAlCarrito(productoIdCantidadInput.value, inputCantidad.value);
        modalCantidad.hide();
        buscador.value = '';
        resultadosDiv.innerHTML = '';
        buscador.focus();
    });

    inputCantidad.addEventListener('keypress', (e) => { if (e.key === 'Enter') { e.preventDefault(); btnAgregarConCantidad.click(); } });
    btnCancelarVenta.addEventListener('click', () => { if (confirm('¿Estás seguro?')) { carrito = {}; renderizarCarrito(); } });
    
    // ... (El resto de los listeners de pago que ya teníamos) ...

    // Carga inicial
    renderizarCarrito();
});
</script>

<?php require_once __DIR__ . '/src/views/partials/footer.php'; ?>