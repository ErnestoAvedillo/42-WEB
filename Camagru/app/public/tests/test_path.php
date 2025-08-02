<?php
echo "<h2>Path Testing</h2>";

echo "<p><strong>Current file:</strong> " . __FILE__ . "</p>";
echo "<p><strong>Current directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Parent directory:</strong> " . dirname(__DIR__) . "</p>";

$envLoaderPath = __DIR__ . '/../EnvLoader.php';
echo "<p><strong>EnvLoader path:</strong> " . $envLoaderPath . "</p>";
echo "<p><strong>EnvLoader exists:</strong> " . (file_exists($envLoaderPath) ? 'YES' : 'NO') . "</p>";

$error_register_path = __DIR__ . '/../pages/register/error_register_handler.php';
echo "<p><strong>Error Register Handler path:</strong> " . $error_register_path . "</p>";
echo "<p><strong>Error Register Handler exists:</strong> " . (file_exists($error_register_path) ? 'YES' : 'NO') . "</p>";

if (file_exists($envLoaderPath)) {
    try {
        require_once $envLoaderPath;
        echo "<p style='color: green;'>✅ EnvLoader loaded successfully!</p>";

        echo "<p><strong>Testing environment variables:</strong></p>";
        echo "<ul>";
        echo "<li>PG_HOST: " . EnvLoader::get('PG_HOST', 'NOT_SET') . "</li>";
        echo "<li>PG_USER: " . EnvLoader::get('PG_USER', 'NOT_SET') . "</li>";
        echo "<li>PG_DATABASE: " . EnvLoader::get('PG_DATABASE', 'NOT_SET') . "</li>";
        echo "</ul>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error loading EnvLoader: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ EnvLoader.php not found!</p>";

    echo "<p><strong>Directory contents:</strong></p>";
    echo "<ul>";
    $parentDir = dirname(__DIR__);
    if (is_dir($parentDir)) {
        $files = scandir($parentDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "<li>" . htmlspecialchars($file) . "</li>";
            }
        }
    }
    echo "</ul>";
}
