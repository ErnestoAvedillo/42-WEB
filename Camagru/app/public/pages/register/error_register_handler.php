<h2>Error: Registration Failed</h2>
<p>There was an error during registration. Please try again.</p>
<p><a href="index.php?page=register">Go back to registration</a></p>
<?php
unset($_SESSION['errors']);
unset($_SESSION['success_message']);
unset($_SESSION['registered_user']);
?>