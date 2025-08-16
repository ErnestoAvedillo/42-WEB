<?php
// Test de conexión MongoDB para debug
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>Test de Post</h2>";

// Test básico de la clase Post
try {
    require_once '../database/posts.php';
    echo "<p>✓ Clase Post cargada correctamente</p>";

    $post = new Posts();
    echo "<p>✓ Instancia de Post creada correctamente</p>";

    // Test de registro con datos de prueba
    $testUserUuid = 'f6d728c8-196a-454b-8ae2-9b1e0c6ce69c';
    $testDocUuid = '309ee1d6-86cd-4adc-b257-e5d5e58cf365';
    $testCaption = 'Test caption for post';

    echo "<p>Intentando registrar post de prueba:</p>";
    echo "<ul>";
    echo "<li>User UUID: $testUserUuid</li>";
    echo "<li>Document UUID: $testDocUuid</li>";
    echo "<li>Caption: $testCaption</li>";
    echo "</ul>";

    $result = $post->addPost($testUserUuid, $testDocUuid, $testCaption);

    echo "<h3>Resultado del registro del post:</h3>";
    echo "<pre>";
    print_r($result);
    echo "</pre>";

    if ($result) {
        echo "<p style='color: green;'>✓ Post registrado exitosamente!</p>";
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
