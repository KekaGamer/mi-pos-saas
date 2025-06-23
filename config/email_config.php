<?php
// config/email_config.php

// --- INSTRUCCIONES IMPORTANTES ---
// Debes reemplazar estos valores con los de tu propio servidor de correo SMTP.
// A continuación, se muestra un ejemplo para una cuenta de Gmail.

define('SMTP_HOST', 'smtp.gmail.com');          // El servidor SMTP de tu proveedor (ej: smtp.gmail.com)
define('SMTP_USERNAME', 'tu_correo@gmail.com'); // Tu dirección de correo electrónico completa
define('SMTP_PASSWORD', 'xxxxxxxxxxxxxxxx');   // La contraseña de tu correo O una "Contraseña de Aplicación"
define('SMTP_PORT', 587);                        // El puerto SMTP. 587 (TLS) o 465 (SSL) son comunes.
define('SMTP_SECURE', 'tls');                    // El tipo de encriptación: 'tls' o 'ssl'

define('EMAIL_FROM', 'tu_correo@gmail.com');     // El correo que aparecerá como remitente
define('EMAIL_FROM_NAME', 'Tu Negocio POS');     // El nombre que aparecerá como remitente

/*
 * --- NOTA ESPECIAL PARA USUARIOS DE GMAIL ---
 * Desde 2022, Gmail requiere que uses una "Contraseña de Aplicación" para iniciar sesión
 * desde aplicaciones de terceros como nuestro sistema POS. No puedes usar tu contraseña normal.
 * * ¿Cómo crear una Contraseña de Aplicación?
 * 1. Ve a la configuración de tu cuenta de Google.
 * 2. Asegúrate de tener la "Verificación en dos pasos" activada. Es un requisito.
 * 3. En la sección de "Seguridad", busca "Contraseñas de aplicaciones".
 * 4. Genera una nueva contraseña, ponle un nombre como "Sistema POS" y copia la contraseña de 16 letras que te da Google.
 * 5. Pega esa contraseña de 16 letras en el campo SMTP_PASSWORD de arriba.
 * * Para otros proveedores (Outlook, tu hosting, etc.), busca su documentación sobre "configuración SMTP".
 */
?>