<?php
// Unset the session cookie
setcookie('session_id', '', time() - 3600, '/');

// Redirect to the login page or any other page
header("Location: index.html");
exit();
?>
