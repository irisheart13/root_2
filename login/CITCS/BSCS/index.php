<?php
    session_start(); 
    include '../../../conn.php'; 

    // Check if user is logged in
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: /Root_1/index.php");
    exit();
    }

    $user_name = htmlspecialchars($_SESSION['username']);

    if (isset($_POST["submit"])) {
        $title = htmlspecialchars($_POST["title"]);
        $main_author = htmlspecialchars($_POST["main_author"]);
        $co_author_1 = htmlspecialchars($_POST["co_author_1"]);
        $co_author_2 = htmlspecialchars($_POST["co_author_2"]);
        $others = htmlspecialchars($_POST["others"]);

        // Fetch user's department and program from tbl_user table
        $user_query = $conn->prepare("SELECT department, program FROM tbl_user WHERE username = ?");
        $user_query->bind_param("s", $user_name);
        $user_query->execute();
        $user_query->store_result();

        if ($user_query->num_rows > 0) {
            $user_query->bind_result($department, $program);
            $user_query->fetch();
        } else {
            exit();
        }
        $user_query->close();

        // Handle file upload
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
            $file_ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
            
            // Allow only PDF files
            if ($file_ext !== "pdf") {
                exit;
            }

            $unique_name = uniqid("file_", true) . '.' . $file_ext;
            $target_file = $target_dir . $unique_name;

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("INSERT INTO tbl_fileUpload (username, department, program, title, main_author, co_author_1, co_author_2, others, file_upload) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssssssss", $user_name, $department, $program, $title, $main_author, $co_author_1, $co_author_2, $others, $target_file);

                if ($stmt->execute()) {
                    // echo "File uploaded and data saved successfully!";
                } else {
                    // echo "Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                // echo "Error uploading file.";
            }
        } else {
            echo "No file uploaded or there was an upload error.";
        }

    }   

    // Pagination
    $limit = 5; // Max records per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch user data with pagination
    $tbl_fileUpload_query = $conn->prepare("SELECT date_of_submission, title, main_author, co_author_1, co_author_2, others, file_upload, research_status
                                        FROM tbl_fileUpload WHERE username = ? ORDER BY date_of_submission DESC LIMIT ? OFFSET ?");
    $tbl_fileUpload_query->bind_param("sii", $user_name, $limit, $offset);
    $tbl_fileUpload_query->execute();
    $tbl_fileUpload_query->store_result();
    $tbl_fileUpload_query->bind_result($date_of_submission, $title, $main_author, $co_author_1, $co_author_2, $others, $file_upload, $research_status);

    // Get total records for pagination
    $total_query = $conn->prepare("SELECT COUNT(*) FROM tbl_fileUpload WHERE username = ?");
    $total_query->bind_param("s", $user_name);
    $total_query->execute();
    $total_query->bind_result($total_rows);
    $total_query->fetch();
    $total_query->close();

    $total_pages = ceil($total_rows / $limit);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <meta charset="UTF-8">
    <title>BSCS</title>


    <link href="/Root_1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/Root_1/dist/js/bootstrap.bundle.min.js"></script>

</head>
<style>
    /* Poppins Regular */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Regular.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
    }

    /* Poppins Bold */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Bold.ttf') format('truetype');
        font-weight: 700;
        font-style: normal;
    }

    /* Poppins Italic */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Italic.ttf') format('truetype');
        font-weight: 400;
        font-style: italic;
    }

    /* Poppins Light */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Light.ttf') format('truetype');
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

        margin: 0;
        padding: 0;

        background-color: #212529;
    }
   .logout{
        display: flex;
        justify-content: flex-end;
        padding-top: 15px;
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
        padding-top: 15px;
        padding-left: 30px;

        text-align: start; 
        vertical-align: middle;
        font-size: 18px;
        color: white;
    }
    /*Nav Bar Design END*/

    /*Form Design START*/
    .dataEntry_uploadForm {
        display: flex;
        justify-content: center; 
        align-items: center;       
        
        margin-top: 60px;
        padding-left: 10px;  
        background-color: #ffb800;   
    }
    .custom-input{
        background-color: #f5f5f5;
    }
    .input_box{
        width: 100%;
        padding: 10px;
    }
    .btn-submit{
        margin-right: 20px;

        border: none;
        border-radius: 10px;

        width: 100px;
        height: 30px;
        color: white;
        background-color: #283618;

        font-size: 18px;
    }
    /*Form Design END*/

    /*Table Design START*/
    .dashboard{
        padding:0px;
    }
    .custom-table{
        word-wrap: break-word;
        white-space: normal;
        overflow-wrap: break-word;

        table-layout: fixed;
    }
    /*Table Design END*/

    /*For text alignment of "Co-author"*/
    @media(min-width: 576px) {
        .custom-label{
            text-align: left;
        }
    }

    @media(min-width: 768px) {
        .custom-label{
            text-align: right;
        }
    }



</style>
<body>
    <div class="container-fluid">
        <!--Nav Section START-->
        <div class="row nav-bar">
            <div class="col-6 user-display">
                <p>Hello, <?php echo $user_name; ?>!</p>
            </div>
            <div class="col-6 logout">
                <form action="/Root_1/logout.php" method="post">
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </div>
        <!--Nav Section END-->

        <!--dataEntry_uploadForm Section START-->
        <div class="dataEntry_uploadForm">
            <form method="POST" enctype="multipart/form-data">
                <div class="row input_box">
                    <div class="col-12 col-sm-2 p-1"> <!--Research title START-->
                        <label>Research Title:</label>
                    </div>
                    <div class="col-12 col-sm-10 p-1">
                        <input type="text" name="title" placeholder="TITLE" class="form-control text-center custom-input" required>
                    </div><!--Research title END-->

                    <div class="col-12 col-sm-6 col-md-4"> <!--Main Author START-->
                        <div class="row">
                            <div class="col-12 col-sm-5 p-1">
                                <label>Main Author:</label>
                            </div>
                            <div class="col-12 col-sm-7 p-1">
                                <input type="text" name="main_author" placeholder="Name" class="form-control text-center custom-input" required>
                            </div>
                        </div>
                    </div><!--Main Author END-->
                    <div class="col-12 col-sm-6 col-md-4"> <!--Co-author_1 START-->
                        <div class="row">
                            <div class="col-12 col-sm-4 p-1 custom-label">
                                <label>Co-author:</label>
                            </div>
                            <div class="col-12 col-sm-8 p-1">
                                <input type="text" name="co_author_1" placeholder="Name" class="form-control text-center custom-input">
                            </div>
                        </div>
                    </div><!--Co-author_1 END-->
                    <div class="col-12 col-sm-12 col-md-4"> <!--Co-author_2 START-->
                        <div class="row">
                            <div class="col-12 col-sm-2 col-md-4 p-1 custom-label">
                                <label>Co-author:</label>
                            </div>
                            <div class="col-12 col-sm-10 col-md-8 p-1">
                                <input type="text" name="co_author_2" placeholder="Name" class="form-control text-center custom-input">
                            </div>
                        </div>
                    </div><!--Co-author_2 END-->
                    <div class="col-12"> <!--Co-author_2 START-->
                        <div class="row">
                            <div class="col-12 col-sm-1 p-1">
                                <label>More Authors:</label>
                            </div>
                            <div class="col-12 col-sm-10  offset-sm-1 p-1">
                                <textarea type="text" name="others" placeholder="Type here..." class="form-control custom-input"></textarea>
                            </div>
                        </div>
                    </div><!--Co-author_2 END-->

                    <div class="col-12 col-md-6">
                        <div class="row">
                            <div class="row"> <!--File Attachment START-->
                                <div class="col-12 col-md-5  p-1">
                                    <label>Attach File Here:</label>
                                </div>
                                <div class="col-12 col-md-8 p-1">
                                    <input type="file" name="file" id="tbl_fileUpload" class="form-control custom-input" required>
                                </div>
                            </div><!--File Attachment END-->
                            <div class="col-5 col-md-2 p-1">
                                <button type="submit" name="submit" class="btn-submit">SUBMIT</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!--dataEntry_uploadForm Section END-->

        <!--Uploaded File Dashboard START-->
        <div class="dashboard">
            <table class="table table-striped custom-table">
                <thead>
                    <tr>
                        <th>Date of Submission</th>
                        <th>Title</th>
                        <th>Main Author</th>
                        <th>Co-Author 1</th>
                        <th>Co-Author 2</th>
                        <th>More Authors</th>
                        <th>Uploaded File</th>
                        <th>Research Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        // Fetch the results and display them as table rows
                        if ($tbl_fileUpload_query->num_rows > 0) {
                            while ($tbl_fileUpload_query->fetch()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($date_of_submission) . "</td>";
                                echo "<td>" . htmlspecialchars($title) . "</td>";
                                echo "<td>" . htmlspecialchars($main_author) . "</td>";
                                echo "<td>" . htmlspecialchars($co_author_1) . "</td>";
                                echo "<td>" . htmlspecialchars($co_author_2) . "</td>";
                                echo "<td>" . htmlspecialchars($others) . "</td>";
                                echo "<td><a href='review_doc.php?file=" . urlencode($file_upload) . "' target='_blank'>View PDF</a></td>";
                                echo "<td>" . htmlspecialchars($research_status) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No submissions found.</td></tr>";
                        }

                        $tbl_fileUpload_query->close();
                    ?>
                </tbody>
            </table>
            <!-- Pagination Links -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>

        <!--Uploaded File Dashboard START-->

    </div>
</body>
</html>
