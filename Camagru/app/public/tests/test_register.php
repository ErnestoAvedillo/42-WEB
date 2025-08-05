<?php
// Test de conexión MongoDB para debug
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>Test de Registro de Usuario</h2>";

// Test básico de la clase User
try {
    require_once '../database/User.php';
    echo "<p>✓ Clase User cargada correctamente</p>";

    $user = new User();
    echo "<p>✓ Instancia de User creada correctamente</p>";

    // Test de registro con datos de prueba
    $testUsername = 'testuser_' . time();
    $testEmail = 'test_' . time() . '@example.com';
    $testPassword = 'TestPass123!';

    echo "<p>Intentando registrar usuario de prueba:</p>";
    echo "<ul>";
    echo "<li>Username: $testUsername</li>";
    echo "<li>Email: $testEmail</li>";
    echo "<li>Password: [oculta]</li>";
    echo "</ul>";

    $result = $user->register($testUsername, $testEmail, $testPassword, 'Test', 'User');

    echo "<h3>Resultado del registro:</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";

    if ($result['success']) {
        echo "<p style='color: green;'>✓ Usuario registrado exitosamente!</p>";
    } else {
        echo "<p style='color: red;'>✗ Error en el registro: " . $result['message'] . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='/pages/register/register.php'>Volver al formulario de registro</a></p>";
echo "<p><a href='/pages/debug/debug.php'>Ir a página de debug</a></p>";
