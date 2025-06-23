<?php
// src/controllers/logout.php
session_start();

// Destruye todas las variables de sesión.
$_SESSION = array();

// Finalmente, destruye la sesión.
session_destroy();

// Redirige a la página de login.
header("location: /login.php");
exit;
?>