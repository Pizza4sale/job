<?php
// Database connection settings
$server = "localhost";
$user = "root";
$password = "";
$db = "job";

// Create a new MySQLi instance
$mysqli = new mysqli($server, $user, $password, $db);

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


?>
