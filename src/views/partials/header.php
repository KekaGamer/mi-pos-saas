<?php
// src/views/partials/header.php -- VERSIÓN FINAL RESPONSIVE
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Sistema POS Profesional</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style> 
        body { display: flex; flex-direction: column; min-height: 100vh; } 
        .main-container { flex: 1; } 
        /* Ajuste para que el nombre de usuario no se rompa en pantallas pequeñas */
        .navbar-text { white-space: nowrap; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/reportes.php">
                <i class="bi bi-shop"></i> Mi POS
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        
                        <?php if (in_array($_SESSION['user_rol'], ['admin', 'contador'])): ?>
                            <li class="nav-item"><a class="nav-link" href="/reportes.php"><i class="bi bi-graph-up"></i> Dashboard</a></li>
                        <?php endif; ?>

                        <?php if (in_array($_SESSION['user_rol'], ['admin', 'cajero'])): ?>
                            <li class="nav-item"><a class="nav-link" href="/ventas.php"><i class="bi bi-cart4"></i> Punto de Venta</a></li>
                        <?php endif; ?>

                        <?php if (in_array($_SESSION['user_rol'], ['admin', 'contador'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-journal-richtext"></i> Gestión
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="/historial_ventas.php">Historial de Ventas</a></li>
                                <li><a class="dropdown-item" href="/proveedores.php">Proveedores</a></li>
                                <li><a class="dropdown-item" href="/facturas.php">Facturas de Compra</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                         <?php if ($_SESSION['user_rol'] == 'admin'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-wrench-adjustable-circle"></i> Administración
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark">
                                <li><a class="dropdown-item" href="/index.php">Inventario</a></li>
                                <li><a class="dropdown-item" href="/historial_inventario.php">Log de Inventario</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/categorias.php">Categorías</a></li> <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/promociones.php">Promociones</a></li>
                                <li><a class="dropdown-item" href="/usuarios.php">Usuarios</a></li>
                                 <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/configuracion.php">Configuración del Negocio</a></li>
                                <li><a class="dropdown-item" href="/cajas_admin.php">Administrar Cajas Físicas</a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if (in_array($_SESSION['user_rol'], ['admin', 'cajero'])): ?>
                            <?php if (isset($_SESSION['caja_sesion_id'])): ?>
                                <a href="/cierre_caja.php" class="btn btn-danger me-2"><i class="bi bi-door-closed-fill"></i> Cerrar Caja</a>
                            <?php else: ?>
                                <a href="/abrir_caja.php" class="btn btn-success me-2"><i class="bi bi-door-open-fill"></i> Abrir Caja</a>
                            <?php endif; ?>
                        <?php endif; ?>
                    
                        <span class="navbar-text me-3">Hola, <?php echo htmlspecialchars($_SESSION['user_nombre']); ?></span>
                        <a href="/src/controllers/logout.php" class="btn btn-outline-danger me-2"><i class="bi bi-box-arrow-right"></i> Salir</a>
                    <?php endif; ?>

                    <button class="btn btn-outline-light" id="theme-toggler"><i class="bi bi-sun-fill"></i></button>
                </div>

            </div> </div>
    </nav>

    <div class="container mt-4 main-container">