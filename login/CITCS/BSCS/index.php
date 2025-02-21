<?php
    session_start(); 

    // Check if user is logged in
    if (isset($_SESSION['username'])) {
        $user_name = htmlspecialchars($_SESSION['username']);
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CS</title>

    <link href="/Root_1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/Root_1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

</head>
<style>
    body{
        margin: 0;
        padding: 0;

        font-family: 'Poppins';
    }

    .nav-bar{
        width: 100%;
        height: 40px;

        margin: 0;
        padding: 0;

        background-color: #283618;
    }
   .logout{
        display: flex;
        justify-content: flex-end;
        padding: 8px;
    }
    .btn-logout{
        margin-right: 20px;

        border: none;
        border-radius: 10px;

        width: 100px;
        height: 25px;
        background-color: #f5f5f5;
    }
    .user-display{
        display: flex;
        align-items: center;
        padding-top: 8px;
        padding-left: 30px;
        font-size: 18px;
        color: white;
    }

</style>
<body>
    <div class="row nav-bar">
        <div class="col-6 user-display">
            <p>Hello, <?php echo $user_name; ?>!</h1>
        </div>
        <div class="col-6 logout">
            <form action="/Root_1/logout.php" method="post">
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>
