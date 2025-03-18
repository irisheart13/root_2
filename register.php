<?php
include 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $first_name = $_POST['first_name'];
    $middle_initial = $_POST['middle_initial'];
    $last_name = $_POST['last_name'];
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

    // Confirmation of the pin
    if ($_POST['pin'] !== $_POST['confirm_pin']) {
        echo "<script>alert('PIN and Confirm PIN do not match!'); window.history.back();</script>";
        exit();
    }

    // Check if email already exists
    $checkUser = "SELECT * FROM tbl_user WHERE username='$email'";
    $result = $conn->query($checkUser);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered!'); window.location.href='index.php';</script>";
    } else {
        // Insert new user with the updated database fields
        $sql = "INSERT INTO tbl_user (last_name, first_name, middle_initial, username, role, pin, department, program) 
                VALUES ('$last_name', '$first_name', '$middle_initial', '$email', '$role', '$pin', '$department', '$program')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful! You can now log in.'); window.location.href='index.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $conn->close();
}
?>
