<?php
$servername = "localhost"; 
$username = "admin"; 
$password = "admin"; 
$dbname = "college_directory"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
} 
?>