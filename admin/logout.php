<?php
/**
 * Admin Logout
 * Destroys admin session and redirects to login
 */

session_start();
session_destroy();
header('Location: login.php');
exit;
?>
