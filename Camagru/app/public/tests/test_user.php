<?php
echo "<h2>User Class Test</h2>";

try {
    require_once 'User.php';
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
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Error details:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
