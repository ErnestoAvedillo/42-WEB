<?php
try {
    // Use Docker service name (from docker-compose.yml)
    $dsn = "pgsql:host=postgre;port=5432;dbname=camagru_db";
    $username = "camagru";
    $password = "camagru";
    
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $users = [
        ['username' => 'alice', 'email' => 'alice@example.com', 'password' => password_hash('password1', PASSWORD_DEFAULT)],
        ['username' => 'bob', 'email' => 'bob@example.com', 'password' => password_hash('password2', PASSWORD_DEFAULT)],
        ['username' => 'charlie', 'email' => 'charlie@example.com', 'password' => password_hash('password3', PASSWORD_DEFAULT)],
    ];

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");

    foreach ($users as $user) {
        $stmt->execute([
            ':username' => $user['username'],
            ':email' => $user['email'],
            ':password' => $user['password'],
        ]);
        echo "Inserted user: {$user['username']}<br>";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
