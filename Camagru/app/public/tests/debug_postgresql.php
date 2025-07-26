<?php
require_once '../EnvLoader.php';
echo "<h1>PHP PDO Drivers Test</h1>";

echo "<h2>1. Available PDO Drivers:</h2>";
$drivers = PDO::getAvailableDrivers();
echo "<ul>";
foreach ($drivers as $driver) {
    echo "<li><strong>$driver</strong>";
    if ($driver === 'pgsql') {
        echo " ✅ (PostgreSQL driver found!)";
    } elseif ($driver === 'mysql') {
        echo " ✅ (MySQL driver found!)";
    } elseif ($driver === 'sqlite') {
        echo " ✅ (SQLite driver found!)";
    }
    echo "</li>";
}
echo "</ul>";

echo "<h2>2. PostgreSQL Driver Status:</h2>";
if (in_array('pgsql', $drivers)) {
    echo "✅ <strong>PostgreSQL PDO driver is available!</strong><br>";

    echo "<h3>2.1 Connection Test:</h3>";
    try {
        $dsn = "pgsql:host=postgre;port=5432;dbname=camagru_db";
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

        echo "✅ <strong>PostgreSQL connection successful!</strong><br>";

        // Test query
        $stmt = $pdo->query("SELECT version()");
        $version = $stmt->fetchColumn();
        echo "📋 PostgreSQL Version: " . $version . "<br>";

        // Check if users table exists
        $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_name = 'users'");
        $tableExists = $stmt->fetchColumn() > 0;

        if ($tableExists) {
            echo "✅ 'users' table exists<br>";

            // Count users
            $stmt = $pdo->query("SELECT COUNT(*) FROM users");
            $userCount = $stmt->fetchColumn();
            echo "📊 Users in table: $userCount<br>";
        } else {
            echo "❌ 'users' table does not exist<br>";
            echo "💡 This might be normal if the initialization script hasn't run yet.<br>";
        }
    } catch (PDOException $e) {
        echo "❌ <strong>PostgreSQL connection failed:</strong><br>";
        echo "Error: " . $e->getMessage() . "<br>";
        echo "<h4>Common solutions:</h4>";
        echo "<ul>";
        echo "<li>Make sure PostgreSQL container is running</li>";
        echo "<li>Check if the service name is 'postgre' in docker-compose.yml</li>";
        echo "<li>Verify the database credentials</li>";
        echo "<li>Ensure containers are on the same network</li>";
        echo "</ul>";
    }
} else {
    echo "❌ <strong>PostgreSQL PDO driver is NOT available!</strong><br>";
    echo "🔧 <strong>Solution:</strong> Rebuild your PHP container with PostgreSQL support.<br>";
    echo "Add this to your PHP Dockerfile:<br>";
    echo "<code>RUN apt-get install -y libpq-dev && docker-php-ext-install pdo_pgsql</code>";
}

echo "<h2>3. PHP Configuration:</h2>";
echo "<pre>";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Extensions loaded: " . count(get_loaded_extensions()) . "\n";
echo "</pre>";

if (extension_loaded('pdo')) {
    echo "✅ PDO extension is loaded<br>";
} else {
    echo "❌ PDO extension is NOT loaded<br>";
}
