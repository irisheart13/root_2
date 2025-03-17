<?php
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $fullname = $_POST['fullname'];
    $email = $_POST['username']; // Username is an email
    $pin = $_POST['pin'];
    $department = $_POST['department'];
    $program = $_POST['program'];
    $role = "user"; // Default role

    // Validate email domain
    if (!preg_match("/@plmun.edu.ph$/", $email)) {
        echo "<script>alert('Only @plmun.edu.ph emails are allowed.'); window.history.back();</script>";
        exit();
    }

    // Validate PIN (4-digit numbers only)
    if (!preg_match("/^\d{4}$/", $pin)) {
        echo "<script>alert('PIN must be exactly 4 digits.'); window.history.back();</script>";
        exit();
    }

    // Check if email already exists
    $checkUser = "SELECT * FROM tbl_user WHERE username='$email'";
    $result = $conn->query($checkUser);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location.href='index.php';</script>";
    } else {
        // Insert new user
        $sql = "INSERT INTO tbl_user (fullname, username, role, pin, department, program) 
                VALUES ('$fullname', '$email', '$role', '$pin', '$department', '$program')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful! You can now log in.'); window.location.href='index.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $conn->close();
}
?>
