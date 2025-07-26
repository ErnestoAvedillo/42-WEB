<?php
try {
    // Use Docker service name (from docker-compose.yml)
    $username = EnvLoader::get('PG_USER', 'camagru');
    $password = EnvLoader::get('PG_PASSWORD', 'camagru');
    $dsn = EnvLoader::get('PG_DSN', 'pgsql:host=postgre;port=5432;dbname=camagru_db');

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "<h2>Checking users table structure:</h2>";

    // Check if table exists
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'users'");
    $tableExists = $stmt->fetch();

    if ($tableExists) {
        echo "<p>✓ Users table exists</p>";

        // Get table structure
        $stmt = $pdo->query("SELECT column_name, data_type, is_nullable, column_default 
                            FROM information_schema.columns 
                            WHERE table_name = 'users' 
                            ORDER BY ordinal_position");

        echo "<h3>Current columns:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Column Name</th><th>Data Type</th><th>Nullable</th><th>Default</th></tr>";

        while ($column = $stmt->fetch()) {
            echo "<tr>";
            echo "<td>{$column['column_name']}</td>";
            echo "<td>{$column['data_type']}</td>";
            echo "<td>{$column['is_nullable']}</td>";
            echo "<td>{$column['column_default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ Users table does not exist</p>";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
