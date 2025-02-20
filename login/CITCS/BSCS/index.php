<?php
    session_start(); 

    // Check if user is logged in
    if (isset($_SESSION['username'])) {
        $user_name = htmlspecialchars($_SESSION['username']);
    } else {
        $user_name = "Guest"; 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CS Document</title>
</head>
<body>
    <h1>Hello, <?php echo $user_name; ?>!</h1>
</body>
</html>
