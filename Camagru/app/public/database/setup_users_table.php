<?php
try {
    echo __DIR__; // Mostrar el directorio actual para depuración
    require_once '../EnvLoader.php';
    // Use Docker service name (from docker-compose.yml)
    $username = EnvLoader::get('PG_USER', 'camagru');
    $password = EnvLoader::get('PG_PASSWORD', 'camagru');
    $dsn = EnvLoader::get('PG_DSN', 'pgsql:host=postgre;port=5432;dbname=camagru_db');

    if (!$dsn || !$username || !$password) {
        throw new Exception("Database connection parameters are not set correctly.");
    }

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "<h2>Creating/Updating users table:</h2>";

    // Drop table if exists (for clean setup)
    $pdo->exec("DROP TABLE IF EXISTS users CASCADE");
    echo "<p>✓ Dropped existing users table</p>";

    // Create users table with all necessary columns
    $createTableSQL = "
        CREATE TABLE users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(50),
            last_name VARCHAR(50),
            email_verified BOOLEAN DEFAULT FALSE,
            verification_token VARCHAR(100),
            reset_token VARCHAR(100),
            reset_token_expires TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";

    $pdo->exec($createTableSQL);
    echo "<p>✓ Created users table with all columns</p>";

    // Create indexes for better performance
    $pdo->exec("CREATE INDEX idx_users_uuid ON users(id)");
    $pdo->exec("CREATE INDEX idx_users_username ON users(username)");
    $pdo->exec("CREATE INDEX idx_users_email ON users(email)");
    echo "<p>✓ Created indexes</p>";

    // Verify table structure
    $stmt = $pdo->query("SELECT column_name, data_type, is_nullable 
                        FROM information_schema.columns 
                        WHERE table_name = 'users' 
                        ORDER BY ordinal_position");

    echo "<h3>New table structure:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Column Name</th><th>Data Type</th><th>Nullable</th></tr>";

    while ($column = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>{$column['column_name']}</td>";
        echo "<td>{$column['data_type']}</td>";
        echo "<td>{$column['is_nullable']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<p><strong>✅ Users table is now ready for registration!</strong></p>";
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
try {
    $pdo->exec("DROP TABLE IF EXISTS documents CASCADE");
    echo "<p>✓ Dropped existing documents table</p>";

    // Create documents table
    $createDocumentsTableSQL = "
        CREATE TABLE documents (
            id SERIAL PRIMARY KEY,
            user_uuid UUID NOT NULL REFERENCES users(uuid),
            document_uuid UUID NOT NULL,
            document_type VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    $pdo->exec($createDocumentsTableSQL);
    echo "<p>✓ Created documents table with user_uuid as foreign key</p>";
} catch (PDOException $e) {
    echo "Database error while creating documents table: " . $e->getMessage();
}
