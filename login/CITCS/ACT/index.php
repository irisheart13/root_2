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

    }
    // Handle file upload
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $allowed_ext = "pdf"; // Allowed file type

    // Function to handle file upload
    function uploadFile($file, $target_dir, $prefix) {
        if (isset($_FILES[$file]) && $_FILES[$file]["error"] === 0) {
            $file_ext = strtolower(pathinfo($_FILES[$file]["name"], PATHINFO_EXTENSION));

            // Allow only PDF files
            if ($file_ext !== "pdf") {
                echo "<script>alert('The system only accepts PDF Files!'); window.location.href='index.php';</script>";
                exit();
            }

            // Generate a unique filename
            $unique_name = uniqid($prefix, true) . '.' . $file_ext;
            $target_file = $target_dir . $unique_name;

            // Move uploaded file to destination
            if (move_uploaded_file($_FILES[$file]["tmp_name"], $target_file)) {
                return $target_file;
            } else {
                return false;
            }
        }
        return false;
    }

    // Upload both research paper and abstract
    $file_research_paper = uploadFile("file_research_paper", $target_dir, "research_");
    $file_abstract = uploadFile("file_abstract", $target_dir, "abstract_");

    // Proceed only if both files are successfully uploaded
    if ($file_research_paper && $file_abstract) {
        $stmt = $conn->prepare("INSERT INTO tbl_fileUpload (username, department, program, title, main_author, co_author_1, co_author_2, others, file_research_paper, file_abstract, date_of_submission) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssssssss", $user_name, $department, $program, $title, $main_author, $co_author_1, $co_author_2, $others, $file_research_paper, $file_abstract);

        if ($stmt->execute()) {
            header("Location: index.php?success=1");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    // Pagination
    $limit = 5; // Max records per page
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch user data with pagination
    $tbl_fileUpload_query = $conn->prepare("SELECT id, date_of_submission, title, main_author, co_author_1, co_author_2, others, file_research_paper, file_abstract, notification, DATE_FORMAT(sched_proposal, '%b %d, %Y') AS formatted_sched_proposal, DATE_FORMAT(sched_final, '%b %d, %Y') AS formatted_sched_final, research_status
                                        FROM tbl_fileUpload WHERE username = ? ORDER BY date_of_submission DESC LIMIT ? OFFSET ?");
    $tbl_fileUpload_query->bind_param("sii", $user_name, $limit, $offset);
    $tbl_fileUpload_query->execute();
    $tbl_fileUpload_query->store_result();
    $tbl_fileUpload_query->bind_result($id, $date_of_submission, $title, $main_author, $co_author_1, $co_author_2, $others, $file_research_paper, $file_abstract, $notification, $sched_proposal, $sched_final, $research_status);

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
    <title>ACT</title>


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
    .table-responsive {
        width: 100%;
        overflow-x: auto; 
        sheight: 100%; 
    }
    .custom-table {
        table-layout: fixed; 
        width: 1500px; 
        white-space: nowrap; 
    }
    .custom-table th, .custom-table td {
        width: 150px; 
        text-overflow: ellipsis; 
        overflow: hidden;
    }
    .custom-table td.wrap, .custom-table th.wrap {
        white-space: normal !important; /* Allows text wrapping */
        word-wrap: break-word; 
        overflow-wrap: break-word;
    }

    .btn-resubmit{
        margin-right: 20px;

        border: none;
        border-radius: 10px;

        width: 100px;
        height: 30px;
        color: white;
        background-color: #283618;

        font-size: 18px;
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
                            <div class="row"> <!--File Attachment (Research Paper) START-->
                                <div class="col-12 col-md-6  p-1">
                                    <label>Attach your research paper here:</label>
                                </div>
                                <div class="col-12 col-md-7 p-1">
                                    <input type="file" name="file_research_paper" id="file_research_paper" class="form-control custom-input" required>
                                </div>
                            </div><!--File Attachment (Research Paper) END-->
                            <div class="row"> <!--File Attachment (Abstract) START-->
                                <div class="col-12 col-md-5  p-1">
                                    <label>Attach your abstract here:</label>
                                </div>
                                <div class="col-12 col-md-8 p-1">
                                    <input type="file" name="file_abstract" id="file_abstract" class="form-control custom-input" required>
                                </div>
                            </div><!--File Attachment (Abstract) END-->
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
            <div class="table-responsive">
                <table class="table table-striped custom-table">
                    <thead>
                        <tr>
                            <th class="wrap">Date of Submission</th>
                            <th>Title</th>
                            <th>Main Author</th>
                            <th>Co-Author 1</th>
                            <th>Co-Author 2</th>
                            <th>More Authors</th>
                            <th class="wrap">Uploaded Research Paper</th>
                            <th class="wrap">Uploaded Abstract</th>
                            <th>Notifications</th>
                            <th class="wrap">Schedule for Proposal Presentation</th>
                            <th class="wrap">Schedule for Final Presentation</th>
                            <th>Research Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // Fetch the results and display them as table rows
                            if ($tbl_fileUpload_query->num_rows > 0) {
                                while ($tbl_fileUpload_query->fetch()) {
                                    echo "<tr>";
                                    echo "<td class='wrap'>" . htmlspecialchars($date_of_submission) . "</td>";
                                    echo "<td class='wrap'>" . htmlspecialchars($title) . "</td>";
                                    echo "<td class='wrap'>" . htmlspecialchars($main_author) . "</td>";
                                    echo "<td>" . htmlspecialchars($co_author_1) . "</td>";
                                    echo "<td>" . htmlspecialchars($co_author_2) . "</td>";
                                    echo "<td>" . htmlspecialchars($others) . "</td>";
                                    echo "<td><a href='/Root_1/review_user.php?id=" . $id . "&type=research' target='_blank'>View PDF</a></td>";
                                    echo "<td><a href='/Root_1/review_user.php?id=" . $id . "&type=abstract' target='_blank'>View PDF</a></td>";

                                    echo "<td class='wrap'>" . htmlspecialchars($notification) . "</td>";
                                    echo "<td>" . htmlspecialchars($sched_proposal) . "</td>";
                                    echo "<td>" . htmlspecialchars($sched_final) . "</td>";
                                    echo "<td>" . htmlspecialchars($research_status) . "</td>";
                                    echo "<td><button href='edit_submission.php?id=" . $id . "' class='btn-resubmit'>Edit</button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No submissions found.</td></tr>";
                            }

                            $tbl_fileUpload_query->close();
                        ?>
                    </tbody>
                </table>
            </div>
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
