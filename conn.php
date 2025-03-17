<?php
$servername = "localhost"; 
$username = "admin"; 
$password = "admin"; 
$dbname = "root_2_dir"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
} 
?>