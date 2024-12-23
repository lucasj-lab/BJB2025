<?php
/**
 * logout.php
 * Destroys the current session and redirects to login or home page.
 */
session_start();
session_unset();
session_destroy();

// Redirect to login page (or anywhere you want)
header('Location: login.php');
exit;
?>
