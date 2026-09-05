<?php
$servername = "localhost";  // XAMPP MySQL runs on localhost
$username = "root";         // Default XAMPP username
$password = "";             // Default XAMPP password (empty)
$database = "unihunt";        // Your database name (make sure it exists)

// Create connection
$conn = new mysqli($servername, $username, $password, $unihunt);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>