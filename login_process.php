<?php
session_start();
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

if (empty($_POST['username']) || empty($_POST['pin'])) {
    $_SESSION['error'] = "Username and PIN are required.";
    header("Location: index.php");
    exit();
}

$username = trim($_POST['username']);
$password = trim($_POST['pin']); // Change this if using password hashing

// Validate login
$sql = "SELECT * FROM tbl_user WHERE username = ? AND pin = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();

    // Store user details in session
    $_SESSION['username'] = $row['username'];
    $_SESSION['department'] = $row['department'];
    $_SESSION['program'] = $row['program'];
    $_SESSION['role'] = $row['role']; 
    $_SESSION['first_name'] = $row['first_name'];

    // Redirect based on user role
    $redirectPaths = [
        'coor' => '/Root_2/login/research_coor/research_coor.php',
        'prog_head' => '/Root_2/login/program_head/program_head.php',
        'dean' => '/Root_2/login/dean/dean.php',
        'director' => '/Root_2/login/director/director.php'
    ];

    $redirectPath = $redirectPaths[$row['role']] ?? '/Root_2/login/general_user/general_User.php';

    // Close resources before redirection
    $stmt->close();
    $conn->close();

    header("Location: $redirectPath");
    exit();
} else {
    $_SESSION['error'] = "Invalid username or PIN."; 
    header("Location: index.php"); 
    exit();
}

$stmt->close();
$conn->close();
?>
