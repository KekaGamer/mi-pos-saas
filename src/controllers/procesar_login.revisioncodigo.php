<?php
// src/controllers/procesar_login.php -- VERSIÓN DE DIAGNÓSTICO
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<pre style='font-family: monospace; background-color: #f4f4f4; padding: 15px; border: 1px solid #ccc;'>";
echo "--- INICIO DEL DIAGNÓSTICO DE LOGIN ---<br><br>";

require_once __DIR__ . '/../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    echo "<b>Paso 1: Datos recibidos del formulario</b><br>";
    echo "Email ingresado: " . htmlspecialchars($email) . "<br>";
    echo "Contraseña ingresada: " . htmlspecialchars($password) . "<br>";
    echo "------------------------------------<br><br>";

    // Buscamos al usuario por su email
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    echo "<b>Paso 2: Búsqueda del usuario en la Base de Datos</b><br>";
    if ($usuario) {
        echo "Resultado: ¡Usuario encontrado!<br>";
        echo "Datos del usuario recuperado:<br>";
        print_r($usuario);
        echo "------------------------------------<br><br>";

        // Verificamos la contraseña
        $password_from_db = $usuario['password_hash'];
        $is_password_correct = password_verify($password, $password_from_db);

        echo "<b>Paso 3: Verificación de la Contraseña</b><br>";
        echo "Contraseña ingresada: " . htmlspecialchars($password) . "<br>";
        echo "Hash guardado en la BD: " . htmlspecialchars($password_from_db) . "<br>";
        echo "Resultado de la verificación (password_verify): ";
        var_dump($is_password_correct);
        echo "<br>";
        echo "------------------------------------<br><br>";

        if ($is_password_correct) {
            echo "<b>Diagnóstico Final:</b><br>";
            echo "<span style='color: green; font-weight: bold;'>¡ÉXITO! La contraseña es correcta.</span> El problema debe estar en el inicio de sesión o la redirección posterior. Pero la validación en sí funciona.";
        } else {
            echo "<b>Diagnóstico Final:</b><br>";
            echo "<span style='color: red; font-weight: bold;'>FALLO.</span> El usuario fue encontrado, pero la contraseña NO coincide con el hash guardado.";
        }

    } else {
        echo "Resultado: Usuario NO encontrado.<br>";
        echo "------------------------------------<br><br>";
        echo "<b>Diagnóstico Final:</b><br>";
        echo "<span style='color: red; font-weight: bold;'>FALLO.</span> El sistema no encontró ningún usuario con el email '".htmlspecialchars($email)."'. Verifica que esté escrito exactamente igual en la base de datos.";
    }

} else {
    echo "Error: No se accedió a la página mediante el método POST.";
}

echo "<br><br>--- FIN DEL DIAGNÓSTICO ---";
echo "</pre>";
?>