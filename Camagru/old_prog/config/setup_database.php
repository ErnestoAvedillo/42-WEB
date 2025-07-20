<?php
// Database setup script
require_once 'php/database.php';

try {
    // Create database connection
    $database = new Database();
    $db = $database->connect();

    // Read and execute the SQL setup file
    $sql = file_get_contents('database_setup.sql');

    // Split SQL statements and execute them
    $statements = explode(';', $sql);

    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $db->exec($statement);
        }
    }

    echo "Database setup completed successfully!\n";
} catch (Exception $e) {
    echo "Error setting up database: " . $e->getMessage() . "\n";
}
