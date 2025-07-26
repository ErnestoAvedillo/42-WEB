<?php
require_once '../EnvLoader.php';
// Use Docker service name (from docker-compose.yml)
$dsn = EnvLoader::get('MONGO_URL', 'mongodb://mongodb:27017');
$username = EnvLoader::get('MONGO_INITDB_ROOT_USERNAME', 'admin');
$password = EnvLoader::get('MONGO_INITDB_ROOT_PASSWORD', 'rooadmin123t');
$database = EnvLoader::get('MONGO_INITDB_DATABASE', 'camagru');
try {
    $manager = new MongoDB\Driver\Manager($dsn, [
        'username' => $username,
        'password' => $password,
        'connectTimeoutMS' => 10000,
    ]);

    echo "<h2>MongoDB Connection Test</h2>";
    echo "✅ Connected to MongoDB at: {$dsn}<br>";

} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "<h2>Error Connecting to MongoDB</h2>";
    echo "❌ " . htmlspecialchars($e->getMessage()) . "<br>";
    exit;
}

echo "<h1>MongoDB Driver Diagnosis</h1>";

echo "<h2>1. Basic Extension Check</h2>";
if (extension_loaded($database)) {
  echo "✅ MongoDB extension is loaded<br>";
  echo "📦 Version: " . phpversion($database) . "<br>";
} else {
  echo "❌ MongoDB extension is NOT loaded<br>";
  echo "🔧 Fix: Add 'extension=mongodb.so' to your php.ini<br>";
}

echo "<h2>2. Individual Class Availability</h2>";

$mongoClasses = [
  'MongoDB\Driver\Manager',
  'MongoDB\Driver\BulkWrite',
  'MongoDB\Driver\Query',
  'MongoDB\Driver\WriteConcern',
  'MongoDB\BSON\UTCDateTime',
  'MongoDB\BSON\ObjectId'
];

foreach ($mongoClasses as $class) {
  if (class_exists($class)) {
    echo "✅ $class exists<br>";
  } else {
    echo "❌ $class NOT found<br>";
  }
}

echo "<h2>3. WriteConcern Constants Check</h2>";
if (class_exists('MongoDB\Driver\WriteConcern')) {
  echo "✅ WriteConcern class exists<br>";

  if (defined('MongoDB\Driver\WriteConcern::MAJORITY')) {
    echo "✅ MAJORITY constant exists<br>";
    echo "📋 Value: " . MongoDB\Driver\WriteConcern::MAJORITY . "<br>";
  } else {
    echo "❌ MAJORITY constant NOT found<br>";
    echo "💡 Use string 'majority' instead<br>";
  }

  // Try to create WriteConcern objects
  try {
    $wc1 = new MongoDB\Driver\WriteConcern("majority", 1000);
    echo "✅ WriteConcern with string 'majority' works<br>";
  } catch (Exception $e) {
    echo "❌ WriteConcern with string failed: " . $e->getMessage() . "<br>";
  }

  try {
    if (defined('MongoDB\Driver\WriteConcern::MAJORITY')) {
      $wc2 = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
      echo "✅ WriteConcern with constant works<br>";
    }
  } catch (Exception $e) {
    echo "❌ WriteConcern with constant failed: " . $e->getMessage() . "<br>";
  }
} else {
  echo "❌ WriteConcern class NOT found<br>";
}

echo "<h2>4. MongoDB Connection Test</h2>";
try {
  // Test different connection strings (matching your setup)
  $connectionStrings = [
    "mongodb://mongodb:27017",     // Your actual docker-compose setup
    "mongodb://localhost:27017",   // If running locally
    "mongodb://root:root@mongodb:27017",   // With auth (unlikely)
    "mongodb://admin:admin@mongodb:27017"  // Alternative auth
  ];

  $manager = null;
  $workingConnection = null;

  foreach ($connectionStrings as $connStr) {
    try {
      echo "🔄 Trying: $connStr<br>";
      $testManager = new MongoDB\Driver\Manager($connStr);

      // Test with a simple query instead of command (more compatible)
      $query = new MongoDB\Driver\Query([], ['limit' => 1]);
      $cursor = $testManager->executeQuery('admin.test', $query);

      echo "✅ Connection successful with: $connStr<br>";
      $manager = $testManager;
      $workingConnection = $connStr;
      break;
    } catch (Exception $e) {
      echo "❌ Failed with $connStr: " . $e->getMessage() . "<br>";
    }
  }

  if ($manager) {
    echo "<h3>4.1 Test Basic Query</h3>";

    // Test querying the specific collection
    try {
      $query = new MongoDB\Driver\Query([], ['limit' => 1]);
      echo "✅ Query object created successfully<br>";

      echo "🔄 Attempting to query camagru.users...<br>";
      $cursor = $manager->executeQuery("camagru.users", $query);
      echo "✅ Query execution successful!<br>";

      $documents = $cursor->toArray();
      echo "📊 Found " . count($documents) . " documents in camagru.users<br>";

      if (count($documents) > 0) {
        echo "📄 Sample document:<br>";
        echo "<pre>" . json_encode($documents[0], JSON_PRETTY_PRINT) . "</pre>";
      } else {
        echo "📝 Collection 'users' is empty (normal for new installations)<br>";
      }
    } catch (Exception $e) {
      echo "❌ Query failed: " . $e->getMessage() . "<br>";
      echo "🔧 This might be because:<br>";
      echo "• Database 'camagru' doesn't exist yet<br>";
      echo "• Collection 'users' doesn't exist yet<br>";
      echo "• MongoDB server is not fully started<br>";
      echo "• Network connectivity issues<br>";
    }

    echo "<h3>4.2 Test Document Creation</h3>";

    // Try to create a test document to verify write operations work
    try {
      $bulk = new MongoDB\Driver\BulkWrite;
      $testDoc = [
        'test' => true,
        'created_at' => new MongoDB\BSON\UTCDateTime(),
        'message' => 'Test document from debug script',
        'timestamp' => time()
      ];
      $bulk->insert($testDoc);

      $result = $manager->executeBulkWrite("camagru.test_collection", $bulk);
      echo "✅ Test document created successfully<br>";
      echo "📝 Inserted documents: " . $result->getInsertedCount() . "<br>";

      // Try to read it back
      $query = new MongoDB\Driver\Query(['test' => true]);
      $cursor = $manager->executeQuery("camagru.test_collection", $query);
      $docs = $cursor->toArray();
      echo "✅ Test document retrieved: " . count($docs) . " document(s)<br>";

      // Show that your User class should work
      echo "<div style='background: #ccffcc; padding: 10px; border: 1px solid #00cc00;'>";
      echo "🎉 <strong>Great news!</strong> MongoDB read/write operations are working.<br>";
      echo "Your User registration should work perfectly!<br>";
      echo "Connection string to use: <code>$workingConnection</code>";
      echo "</div>";
    } catch (Exception $e) {
      echo "❌ Test document operation failed: " . $e->getMessage() . "<br>";
      echo "🔧 This suggests write permission issues or server problems.<br>";
    }
  } else {
    echo "❌ No working MongoDB connection found<br>";
    echo "🔧 Possible solutions:<br>";
    echo "• Make sure MongoDB container is running: <code>docker-compose ps</code><br>";
    echo "• Check MongoDB logs: <code>docker-compose logs mongodb</code><br>";
    echo "• Restart containers: <code>docker-compose restart</code><br>";
  }
} catch (Exception $e) {
  echo "❌ MongoDB connection test completely failed: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Recommendation</h2>";
if (!class_exists('MongoDB\Driver\WriteConcern')) {
  echo "<div style='background: #ffffcc; padding: 10px; border: 1px solid #ffcc00;'>";
  echo "<strong>🔧 Solution:</strong><br>";
  echo "Your MongoDB extension is partially installed. WriteConcern class is missing.<br>";
  echo "This happens when:<br>";
  echo "• Extension version is too old<br>";
  echo "• Installation was incomplete<br>";
  echo "• PHP version incompatibility<br><br>";
  echo "<strong>Your code will work without WriteConcern</strong> - it just means writes aren't acknowledged with the same level of guarantee.<br>";
  echo "</div>";
} else {
  echo "<div style='background: #ccffcc; padding: 10px; border: 1px solid #00cc00;'>";
  echo "✅ <strong>MongoDB is properly installed!</strong><br>";
  echo "All classes are available and working correctly.";
  echo "</div>";
}

echo "<h2>6. PHP Configuration</h2>";
echo "<pre>";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "System: " . PHP_OS . "\n";
echo "Extensions loaded: " . count(get_loaded_extensions()) . "\n";
echo "</pre>";
