<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Example - Camagru</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    $pageTitle = "Debug - Camagru";
    include 'views/header.php';
    ?>

    <?php include 'views/side_bar.php'; ?>

    <main id="mainContent">
        <h1>Debug Example</h1>

        <?php
        // Example variables for debugging
        $name = "John Doe";
        $age = 25;
        $skills = ["PHP", "JavaScript", "HTML", "CSS"];
        $user_data = [
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'active' => true
        ];

        // Set a breakpoint here (click on line number in VS Code)
        echo "<h2>User Information</h2>";
        echo "<p>Name: " . $name . "</p>";
        echo "<p>Age: " . $age . "</p>";

        // Loop example for debugging
        echo "<h3>Skills:</h3>";
        echo "<ul>";
        foreach ($skills as $index => $skill) {
            // Another good breakpoint location
            echo "<li>" . ($index + 1) . ". " . $skill . "</li>";
        }
        echo "</ul>";

        // Function call example
        function calculateTotal($items)
        {
            $total = 0;
            foreach ($items as $item) {
                $total += $item['price'];
            }
            return $total;
        }

        $cart_items = [
            ['name' => 'Laptop', 'price' => 999.99],
            ['name' => 'Mouse', 'price' => 29.99],
            ['name' => 'Keyboard', 'price' => 79.99]
        ];

        $total = calculateTotal($cart_items);
        echo "<h3>Shopping Cart Total: $" . number_format($total, 2) . "</h3>";

        // Database connection example (for debugging database issues)
        try {
            require_once 'User.php';
            $database = new Database();
            $manager = $database->connect();

            if ($manager) {
                echo "<p style='color: green;'>✓ Database connection successful</p>";
            } else {
                echo "<p style='color: red;'>✗ Database connection failed</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
        ?>

        <!-- JavaScript debugging example -->
        <script>
            console.log("JavaScript debugging example");

            // Set breakpoint here in browser dev tools
            function debugExample() {
                let data = {
                    name: "<?php echo $name; ?>",
                    age: <?php echo $age; ?>,
                    skills: <?php echo json_encode($skills); ?>
                };

                console.log("User data:", data);

                // Loop for debugging
                data.skills.forEach((skill, index) => {
                    console.log(`Skill ${index + 1}: ${skill}`);
                });

                return data;
            }

            // Call the function
            debugExample();
        </script>
    </main>

    <?php include 'views/footer.php'; ?>
    <script src="js/hide_bar.js"></script>
</body>

</html>