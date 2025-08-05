<!DOCTYPE html>
<html>

<head>
    <title>Database Setup and Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .section {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
        }
    </style>
</head>

<body>
    <h1>Camagru Database Setup and Test</h1>

    <div class="section">
        <h2>1. Setup Database Table</h2>
        <?php include '../database/setup_users_table.php'; ?>
    </div>

    <div class="section">
        <h2>2. Test User Registration</h2>
        <?php
        try {
            require_once '../database/User.php';

            $user = new User();

            // Test user data
            $testUser = [
                'username' => 'testuser_' . time(),
                'email' => 'test_' . time() . '@example.com',
                'password' => 'TestPass123',
                'first_name' => 'Test',
                'last_name' => 'User'
            ];

            echo "<p>Testing registration with:</p>";
            echo "<ul>";
            echo "<li>Username: " . htmlspecialchars($testUser['username']) . "</li>";
            echo "<li>Email: " . htmlspecialchars($testUser['email']) . "</li>";
            echo "<li>Password: " . htmlspecialchars($testUser['password']) . "</li>";
            echo "<li>First Name: " . htmlspecialchars($testUser['first_name']) . "</li>";
            echo "<li>Last Name: " . htmlspecialchars($testUser['last_name']) . "</li>";
            echo "</ul>";

            $result = $user->register(
                $testUser['username'],
                $testUser['email'],
                $testUser['password'],
                $testUser['first_name'],
                $testUser['last_name']
            );

            if ($result['success']) {
                echo "<p class='success'>✅ Registration successful!</p>";
                echo "<p>Message: " . htmlspecialchars($result['message']) . "</p>";

                // Test login
                echo "<h3>Testing Login</h3>";
                $loginResult = $user->login($testUser['username'], $testUser['password']);

                if ($loginResult['success']) {
                    echo "<p class='success'>✅ Login successful!</p>";
                    echo "<p>Welcome back: " . htmlspecialchars($loginResult['user']['first_name']) . " " . htmlspecialchars($loginResult['user']['last_name']) . "</p>";
                } else {
                    echo "<p class='error'>❌ Login failed: " . htmlspecialchars($loginResult['message']) . "</p>";
                }
            } else {
                echo "<p class='error'>❌ Registration failed: " . htmlspecialchars($result['message']) . "</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>3. Check Current Users</h2>
        <?php
        try {
            require_once '../EnvLoader.php';
            $dsn = EnvLoader::get('PG_DSN', 'pgsql:host=postgre;port=5432;dbname=camagru_db');
            $username = EnvLoader::get('PG_USER', 'camagru');
            $password = EnvLoader::get('PG_PASSWORD', 'camagru');



            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            $stmt = $pdo->query("SELECT id, username, email, first_name, last_name, created_at FROM users ORDER BY created_at DESC LIMIT 5");
            $users = $stmt->fetchAll();

            if ($users) {
                echo "<p>Recent users in database:</p>";
                echo "<table border='1' style='border-collapse: collapse;'>";
                echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Created</th></tr>";

                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No users found in database.</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error checking users: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
        ?>
    </div>

    <div class="section">
        <h2>4. Test Your Registration Form</h2>
        <p>Now you can test your registration form:</p>
        <a href="/pages/register/register.php" style="background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Go to Registration Form</a>
    </div>

</body>

</html>