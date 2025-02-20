<?php
$servername = "localhost"; // Change if your database is hosted elsewhere
$username = "admin"; // Change if you have a different DB user
$password = "admin"; // Change if you have a password
$dbname = "college_directory"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$conn->close();
?>
