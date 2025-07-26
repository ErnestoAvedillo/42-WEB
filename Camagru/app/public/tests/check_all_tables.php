<?php
try {
    require_once '../EnvLoader.php';
    // Use Docker service name (from docker-compose.yml)
    $dsn = EnvLoader::get('PG_DSN', 'pgsql:host=postgre;port=5432;dbname=camagru_db');
    $username = EnvLoader::get('PG_USER', 'camagru');
    $password = EnvLoader::get('PG_PASSWORD', 'camagru');

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "<h2>Checking all tables in the database:</h2>";

    // Get all tables
    $stmt = $pdo->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        ORDER BY table_name
    ");

    $tables = $stmt->fetchAll();

    if ($tables) {
        echo "<p><strong>Tables found:</strong></p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table['table_name']) . "</li>";
        }
        echo "</ul>";

        // Check each table structure
        foreach ($tables as $table) {
            $tableName = $table['table_name'];
            echo "<h3>Structure of table: {$tableName}</h3>";

            $stmt = $pdo->query("
                SELECT column_name, data_type, is_nullable, column_default 
                FROM information_schema.columns 
                WHERE table_name = '{$tableName}' 
                ORDER BY ordinal_position
            ");

            $columns = $stmt->fetchAll();

            if ($columns) {
                echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
                echo "<tr><th>Column</th><th>Type</th><th>Nullable</th><th>Default</th></tr>";

                foreach ($columns as $column) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($column['column_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['data_type']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['is_nullable']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['column_default'] ?? 'NULL') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ No tables found in the database!</p>";
    }

    // Check if initialization script was executed
    echo "<h2>Database Logs Check:</h2>";
    echo "<p>If only the users table exists, it means the pg-init.sql script stopped executing after the first table.</p>";
    echo "<p>This could be due to:</p>";
    echo "<ul>";
    echo "<li>PostgreSQL container credentials mismatch</li>";
    echo "<li>SQL syntax error in pg-init.sql</li>";
    echo "<li>Container restart without clearing volumes</li>";
    echo "</ul>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>This suggests the PostgreSQL container might not be running properly.</p>";
}
