<?php
    session_start();
    include '../../conn.php';

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo "<script>alert('Error: Admin not logged in. Please log in first.'); window.location.href='login.php';</script>";
        exit();
    }

    $user_name = htmlspecialchars($_SESSION['username']);
    $department = $_SESSION['department']; 
    $program = $_SESSION['program'];

    if (!isset($_GET['id']) || !isset($_GET['type'])) {
        die("Invalid request.");
    }
    
    $id = intval($_GET['id']);
    $type = $_GET['type']; // either "research" or "abstract"
    
    // Fetch file path from database
    $query = $conn->prepare("SELECT file_research_paper, file_abstract FROM tbl_fileUpload WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $query->store_result();
    $query->bind_result($file_research_paper, $file_abstract);
    $query->fetch();
    $query->close();
    
    // Identify which file to display
    $file_name = ($type === "research") ? $file_research_paper : $file_abstract;

    if (!$file_name) {
        die("File not found.");
    }

    // Construct full file path
    $file_path = "/root_2/login/general_user/uploadedFile/$department/$program/uploads/" . $file_name;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Review Document</title>


    <link href="/root_2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/root_2/dist/js/bootstrap.bundle.min.js"></script>

</head>
<style>
    /* Poppins Regular */
    @font-face {
        font-family: 'Poppins';
        src: url('/root_2/Poppins/Poppins-Regular.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
    }

    /* Poppins Bold */
    @font-face {
        font-family: 'Poppins';
        src: url('/root_2/Poppins/Poppins-Bold.ttf') format('truetype');
        font-weight: 700;
        font-style: normal;
    }

    /* Poppins Italic */
    @font-face {
        font-family: 'Poppins';
        src: url('/root_2/Poppins/Poppins-Italic.ttf') format('truetype');
        font-weight: 400;
        font-style: italic;
    }

    /* Poppins Light */
    @font-face {
        font-family: 'Poppins';
        src: url('/root_2/Poppins/Poppins-Light.ttf') format('truetype');
        font-weight: 300;
        font-style: normal;
    }

    body{
        margin: 0;
        padding: 0;
        overflow-y: visible;
        overflow-x:hidden;

        font-family: 'Poppins';

        background-color: #dee2e6;
    }

    .container-fluid{
        margin:0;
        padding:0;
    }

    /*Nav Bar Design START*/
    .nav-bar{
        position: absolute;
        top: 0;
        left: 0;

        width: 100vw;
        height: 40px;

        margin: 0;
        padding: 0;

        background-color: #212529;
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
        padding-bottom: 0px;

        background-color: #f5f5f5;
    }
    .user-display{
        display: flex;
        align-items: center;
        padding-top: 8px;
        padding-left: 30px;
        padding-bottom: 0px;
        font-size: 18px;
        color: white;
    }
    /*Nav Bar Design END*/

    /*Viewer START*/
    .viewer{
        margin-top: 40px;
        padding: 0;
        width: 100vw;
    }
    /*Viewer END*/

    /*PDF Viewer START*/
    #pdfViewer{
        height:100vh;
    }
    /*PDF Viewer END*/

    /*Comment Section Design START*/
    .com_sec{
        background-color: #283618;
        color: white;
        height: 100vh;
        padding: 10px;
    }
    label{
        padding-top: 15px;
    }
    .custom-textarea{
        padding: 10px;
        width: 100%;
        height: 130px;
    }
    .btn-submit{
        margin-right: 20px;

        border-width: 1px;
        border-radius: 10px;

        width: 100px;
        height: 30px;
        color: white;
        background-color: #212529;

        font-size: 18px;
    }

    /*Comment Section Design END*/
    
</style>
<body>
    <div class="container-fluid">
        <!--Nav Section START-->
        <div class="row nav-bar">
            <div class="col-6 user-display">
                <p>Hello, <?php echo $user_name; ?>!</p>
            </div>
            <div class="col-6 logout">
                <form action="/root_2/logout.php" method="post">
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </div>
        <!--Nav Section END-->

        <!--Content Section START-->
        <div class="viewer">
            <form method="POST" action="submitComment_coor.php">
                <div class=" container-fluid row">
                    <div class="col-12 col-md-9">
                        <!-- PDF VIEWER USING IFRAME -->
                         <iframe id="pdfViewer" src="<?= htmlspecialchars($file_path) ?>" width="100%" style="border: none;"></iframe>
                    </div>
                    <div class="col-12 col-md-3">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
                        <input type="hidden" name="type" value="<?= htmlspecialchars($_GET['type'] ?? 'research') ?>">
                        <div class="row com_sec">
                            <div class="col-12">
                                <label>Title:</label>
                            </div>
                            <div class="col-12">
                                <textarea name="title" placeholder="Type here..." class="form-control custom-textarea"></textarea>
                            </div>
                            <div class="col-12">
                                <label>Abstract:</label>
                            </div>
                            <div class="col-12">
                                <textarea name="abstract" placeholder="Type here..." class="form-control custom-textarea"></textarea>
                            </div>
                            <div class="col-12">
                                <label>Others:</label>
                            </div>
                            <div class="col-12">
                                <textarea name="others" placeholder="Type here..." class="form-control custom-textarea"></textarea>
                            </div>
                            <div class="col-12 pt-3">
                                <button type="submit" name="submit" class="btn-submit">SUBMIT</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!--Content Section END-->
    </div>

</body>
</html>
