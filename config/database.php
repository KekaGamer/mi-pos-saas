<?php
// config/database.php

// --- Parámetros de Conexión ---
// Estos son los datos para tu base de datos local.
// En un futuro, cuando subas tu sistema a internet, cambiarás estos valores.
$host = 'localhost';        // El servidor de la base de datos (casi siempre localhost)
$dbname = 'mi_pos_saas';    // El nombre de la base de datos que creaste
$username = 'root';         // El usuario de la base de datos (por defecto en XAMPP es 'root')
$password = '';             // La contraseña (por defecto en XAMPP es vacía)

// --- Crear la Conexión (DSN) ---
try {
    // Intentamos crear una nueva conexión usando PDO.
    // PDO es la forma moderna y segura de hablar con bases de datos en PHP.
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Configuramos PDO para que nos informe de errores de forma clara.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Configuramos PDO para que nos devuelva los resultados como arrays asociativos.
    // Esto significa que podemos acceder a los datos por el nombre de la columna (ej: $fila['nombre']).
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Si algo falla en la conexión (ej: contraseña incorrecta, base de datos no existe),
    // el programa se detendrá y mostrará un mensaje de error claro.
    die("¡Error de conexión! No se pudo conectar a la base de datos: " . $e->getMessage());
}

// Si todo salió bien, la variable $pdo ya está lista para ser usada en otros archivos.
?>