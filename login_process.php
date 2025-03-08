<?php
session_start();
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['pin'];

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

        // Redirect based on user role
        if ($row['role'] == 'admin') {
            header("Location: research_coor.php");
        } else {
            $department = $row['department'];
            $program = $row['program'];
            header("Location: login/$department/$program/index.php");
        }
        exit();
    } else {
        $_SESSION['error'] = "Invalid username or PIN."; 
        header("Location: login.php"); 
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>