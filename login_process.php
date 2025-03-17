<?php
session_start();
include 'conn.php';

if (!isset($_POST['username']) || !isset($_POST['pin'])) {
    $_SESSION['error'] = "Username and PIN are required.";
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['pin'];

    // Validate login
    $sql = "SELECT * FROM tbl_user WHERE username = ? AND pin = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $password);
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
        if ($row['role'] == 'coor') {
            // research coor
            $_SESSION['coor_id'] = $row['id'];
            header("Location: /Root_2/login/research_coor/research_coor.php");
        } elseif($row['role'] == 'prog_head'){
            // program head
            header("Location: /Root_2/login/program_head/program_head.php");
        } elseif($row['role'] == 'dean'){
            // dean
            header("Location: /Root_2/login/dean/dean.php");
        }else {
            // general user
            header("Location: /Root_2/login/general_user/general_User.php");
        }
        exit();
    } else {
        $_SESSION['error'] = "Invalid username or PIN."; 
        header("Location: index.php"); 
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
