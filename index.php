<?php
session_start();
include 'login/conn.php'; // Ensure database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['pin'];

    // Validate login
    $sql = "SELECT * FROM users WHERE username=? AND pin=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        $_SESSION['username'] = $username;
        $_SESSION['department'] = $department;
        $_SESSION['program'] = $program;

        // Redirect based on department and program
        $department = $row['department'];
        $program = $row['program'];
        header("Location: login/$department/$program/index.php");
        exit();
    } 


    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Login</title>
</head>
<style>
    body{
        background-color: #283618;
    }

    .login-box{
        width: 400px;
        height: 300px;

        background-color: #f5f5f5;
        border-radius: 10px;
    }
    #login{
        margin: 10px;
    }
    .custom-input{
        border-radius: 15px;
        color: black;
    }
    .btn-login{
        width:100%;
        height:35px;

        border-width: 1px;
        border-radius: 20px;
    }
    .plmun_logo{
        width: 120px;
        height: 120px;
    }

</style>
<body>
    <div class = "container">
        <div class="position-absolute top-50 start-50 translate-middle login-box">
            <div id="login" >
                <form method="POST" action="" class="form_login row">
                    <div class="col-12 d-flex justify-content-center align-items-center">
                        <img src="plmun_logo.png" alt="plmun_logo" class="plmun_logo">
                    </div>
                    <div class="col-8 p-2 mx-auto">
                        <input type="text" name="username" placeholder="USERNAME" class="form-control text-center custom-input" required>
                    </div>
                    <div class="col-8 p-2 mx-auto">
                        <input type="password" name="pin" placeholder="PIN" class="form-control text-center custom-input" required>
                    </div>
                    <div class="col-5 mx-auto p-2">
                        <button type="submit" class="btn-login">LOGIN</button>
                    </div>
                </form>
            </div>
            <?php
            if (!empty($error_message)) {
                echo "<p style='color: red;'>$error_message</p>";
            }
            ?>
        </div>
    </div>



</body>

</html>
