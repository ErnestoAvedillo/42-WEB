<?php
echo "<h2>User Class Test</h2>";

try {
    require_once __DIR__ . '/../database/User.php';
    require_once __DIR__ . '/../database/Profiles.php';
    echo "<p style='color: green;'>✅ User class loaded successfully!</p>";

    $user = new User();
    echo "<p style='color: green;'>✅ User instance created successfully!</p>";

    // Test user registration
    $testUsername = 'testuser_' . time();
    echo "<p><strong>Testing user registration:</strong></p>";
    echo "<ul>";
    echo "<li>Username: " . htmlspecialchars($testUsername) . "</li>";
    echo "<li>Email: test_" . time() . "@example.com</li>";
    echo "<li>Password: [hidden]</li>";
    echo "</ul>";

    $result = $user->register(
        $testUsername,
        'test_' . time() . '@example.com',
        'TestPass123',
        'Test',
        'User'
    );

    if ($result['success']) {
        echo "<p style='color: green;'>✅ User registration successful!</p>";
        echo "<p>Message: " . htmlspecialchars($result['message']) . "</p>";
    } else {
        echo "<p style='color: red;'>❌ User registration failed: " . htmlspecialchars($result['message']) . "</p>";
    }
    // Test user UUID retrieval
    $userId = $result['id'] ?? null;
    echo "<p style='color: green;'>✅ User ID retrieved: " . $userId . "</p>";
    if (!$userId) {
        throw new Exception('No se pudo obtener el ID del usuario registrado');
    }
    $userUuid = $result['uuid'] ?? null;
    echo "<p style='color: green;'>✅ User UUID retrieved: " . $userUuid . "</p>";
    if (!$userUuid) {
        throw new Exception('No se pudo obtener el UUID del usuario registrado');
    }
    $profile = new Profiles();
    $profileData = [
        'user_uuid' => $userUuid,
        'nationality' => '',
        'date_of_birth' => '1900-01-01',
        'street' => '',
        'city' => '',
        'state' => '',
        'zip_code' => '',
        'country' => '',
        'phone_number' => '',
    ];
    $profileResult = $profile->registerUserProfile($profileData);
    echo "<p>✓ Registro de perfil: " . $profileResult['message'] . "</p>";
    if (!$profileResult['success']) {
        throw new Exception('Error al registrar el perfil del usuario');
    } else {
        // ✅ DEBUG: Ver qué devuelve el perfil
        echo "<p>✓ Perfil registrado exitosamente.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Error details:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
