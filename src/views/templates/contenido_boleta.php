<?php
// src/views/templates/contenido_boleta.php -- VERSIÓN CORREGIDA
?>
<div class="boleta-container">
    <div class="header">
        <?php if ($negocio && !empty($negocio['logo_url'])): ?>
            <img src="http://<?php echo $_SERVER['HTTP_HOST'] . htmlspecialchars($negocio['logo_url']); ?>" alt="Logo" style="max-width: 100px; margin-bottom: 10px;">
        <?php endif; ?>
        <h2><?php echo htmlspecialchars($negocio['nombre_local'] ?? 'Mi Negocio'); ?></h2>
        <p><?php echo htmlspecialchars($negocio['direccion'] ?? 'Dirección no especificada'); ?></p>
    </div>
    <div class="info-venta">
        <p><strong>Boleta de Venta</strong></p>
        <p>Boleta N°: <?php echo $venta['id']; ?></p>
        <p>Fecha: <?php echo date("d/m/Y H:i:s", strtotime($venta['fecha_hora'])); ?></p>
        <p>Cajero: <?php echo htmlspecialchars($venta['nombre_cajero']); ?></p>
    </div>
    <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
        <thead><tr><th style="text-align: left; border-bottom: 1px solid #000; font-size: 0.8em; padding: 3px 0;">CANT</th><th style="text-align: left; border-bottom: 1px solid #000; font-size: 0.8em; padding: 3px 0;">PRODUCTO</th><th style="text-align: right; border-bottom: 1px solid #000; font-size: 0.8em; padding: 3px 0;">TOTAL</th></tr></thead>
        <tbody>
            <?php foreach ($detalles as $detalle): ?>
            <tr>
                <td style="font-size: 0.8em; padding: 3px 0;"><?php echo $detalle['cantidad']; ?></td>
                <td style="font-size: 0.8em; padding: 3px 0;"><?php echo htmlspecialchars($detalle['nombre_producto']); ?></td>
                <td style="text-align: right; font-size: 0.8em; padding: 3px 0;">$<?php echo number_format($detalle['subtotal'], 0, ',', '.'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="totales">
        <p>Neto: <span style="float: right;">$<?php echo number_format($venta['neto'], 0, ',', '.'); ?></span></p>
        <p>IVA (<?php echo $negocio['iva_porcentaje']; ?>%): <span style="float: right;">$<?php echo number_format($venta['impuestos'], 0, ',', '.'); ?></span></p>
        <p><strong>TOTAL:</strong> <strong style="float: right;">$<?php echo number_format($venta['total'], 0, ',', '.'); ?></strong></p>
        <p>Método de Pago: <?php echo ucfirst($venta['metodo_pago']); ?></p>
    </div>
    <div class="footer"><p>¡Gracias por su compra!</p></div>
</div>