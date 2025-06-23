<?php
// cfd.php - Customer Facing Display
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Su Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Estilos para una pantalla de cliente limpia y legible */
        body {
            background-color: #1a1a2e; /* Un azul oscuro profundo */
            color: #e0e0e0;
            overflow: hidden; /* Evita barras de scroll innecesarias */
        }
        .container-fluid {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header-cfd {
            text-align: center;
            padding: 1rem;
        }
        .header-cfd .logo {
            max-height: 80px;
            margin-bottom: 1rem;
        }
        .lista-items {
            flex-grow: 1;
            overflow-y: auto;
            font-size: 1.5rem; /* Fuente más grande para los items */
        }
        .item-compra {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 1rem;
            border-bottom: 1px dashed #4f4f7a;
        }
        .item-compra-nombre {
            flex-grow: 1;
        }
        .item-compra-precio {
            font-weight: bold;
            margin-left: 1rem;
        }
        .footer-cfd {
            background-color: #162447;
            padding: 1.5rem 2rem;
            text-align: center;
        }
        .total-label {
            font-size: 2rem;
            font-weight: bold;
            color: #92b1d3;
        }
        .total-amount {
            font-size: 4.5rem; /* Total bien grande */
            font-weight: bold;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="header-cfd">
            <img id="logo-negocio" src="/uploads/logos/default.png" alt="Logo" class="logo" style="display: none;">
        </div>
        
        <div id="lista-items" class="lista-items px-3">
            <h2 class="text-center text-white-50 mt-5">¡Bienvenido!</h2>
        </div>

        <div class="footer-cfd">
            <div class="total-label">TOTAL</div>
            <div id="total-compra" class="total-amount">$0</div>
        </div>
    </div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const listaItemsDiv = document.getElementById('lista-items');
    const totalCompraSpan = document.getElementById('total-compra');
    const logoImg = document.getElementById('logo-negocio');

    // Función para dibujar/renderizar el carrito en la pantalla del cliente
    function renderCustomerCart(data) {
        const cartData = data.cart || {};
        const logoUrl = data.logo;

        listaItemsDiv.innerHTML = ''; // Limpiamos la lista

        if (Object.keys(cartData).length === 0) {
            listaItemsDiv.innerHTML = '<h2 class="text-center text-white-50 mt-5">¡Bienvenido!</h2>';
            totalCompraSpan.innerText = '$0';
        } else {
            let total = 0;
            for (const id in cartData) {
                const item = cartData[id];
                total += item.subtotal;
                
                const itemDiv = document.createElement('div');
                itemDiv.className = 'item-compra animate__animated animate__fadeIn';

                let nombreMostrado = item.nombre;
                if(item.tipo === 'pack'){
                    nombreMostrado = `${item.nombre} (Pack)`;
                } else {
                    nombreMostrado = `${item.cantidad} x ${item.nombre}`;
                }

                itemDiv.innerHTML = `
                    <span class="item-compra-nombre">${nombreMostrado}</span>
                    <span class="item-compra-precio">$${item.subtotal.toLocaleString('es-CL')}</span>
                `;
                listaItemsDiv.appendChild(itemDiv);
            }
            totalCompraSpan.innerText = `$${total.toLocaleString('es-CL')}`;
        }

        // Actualizamos el logo
        if (logoUrl) {
            logoImg.src = logoUrl;
            logoImg.style.display = 'block';
        } else {
            logoImg.style.display = 'none';
        }
    }

    // Función para manejar los cambios que vienen de la otra ventana
    function handleStorageChange(event) {
        if (event.key === 'pos_cfd_cart') {
            // Si el nuevo valor es nulo (se borró), limpiamos la pantalla
            if (event.newValue === null) {
                renderCustomerCart({});
                return;
            }
            const data = JSON.parse(event.newValue);
            renderCustomerCart(data);
        }
    }

    // El "escuchador" de eventos. Se activa cuando otra ventana modifica el localStorage
    window.addEventListener('storage', handleStorageChange);

    // Sincronización inicial: Al abrir la ventana, revisamos si ya hay un carrito activo
    const initialDataRaw = localStorage.getItem('pos_cfd_cart');
    if (initialDataRaw) {
        const initialData = JSON.parse(initialDataRaw);
        renderCustomerCart(initialData);
    }
});
</script>

</body>
</html>