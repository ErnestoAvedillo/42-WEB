<?php 
$pageTitle = "Home - Camagru";
include __DIR__ . '/../views/header.php'; 
?>

<main id="mainContent">
    <h1>Welcome to Camagru</h1>
    <p>This is a simple web application for sharing photos.</p>
    <p>Make sure to run the Docker containers using the Makefile commands.</p>
    <p>For more information, check the documentation.</p>
    
    <?php
    // Example PHP code to display the current date and time
    date_default_timezone_set('UTC');
    echo "<p>Current date and time: " . date("Y-m-d H:i:s") . "</p>";
    ?>
</main>

<form>
    <p>Enter an email address:</p>
    <input id='email'>
    <button type='submit' id='validate'>Validate!</button>
</form>
<h2 id='result'></h2>

<?php include __DIR__ . '/../views/footer.php'; ?>