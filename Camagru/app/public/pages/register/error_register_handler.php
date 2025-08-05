<h2>Error: Registration Failed</h2>
<p>There was an error during registration. Please try again.</p>
<p><a href="/pages/register/register.php">Go back to registration</a></p>
<?php
echo "<p>Error details:</p>";
if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])) {
    echo "<ul>";
    foreach ($_SESSION['errors'] as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No specific error details available.</p>";
}
if (isset($_SESSION['success_message'])) {
    echo "<p>" . htmlspecialchars($_SESSION['success_message']) . "</p>";
}
if (isset($_SESSION['registered_user'])) {
    echo "<p>Registered user: " . htmlspecialchars($_SESSION['registered_user']) . "</p>";
}
// Clear session variables related to registration errors
unset($_SESSION['login_data']);
unset($_SESSION['fromRegister']);
unset($_SESSION['registeredUser']);
unset($_SESSION['data']);
unset($_SESSION['errors']);
unset($_SESSION['success_message']);
unset($_SESSION['registered_user']);
?>