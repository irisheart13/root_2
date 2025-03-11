<?php
    session_start(); 
    include '../../../conn.php'; 

    // Check Database Connection
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Check if user is logged in
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        header("Location: /Root_1/index.php");
        exit();
    }

    if (!isset($_SESSION['username'])) {
        die("Error: User session expired. Please log in again.");
    }

    $user_name = htmlspecialchars($_SESSION['username']);

    if (isset($_POST["submit"])) {
        $title = htmlspecialchars($_POST["title"]);
        $main_author = htmlspecialchars($_POST["main_author"]);
        $co_author_1 = htmlspecialchars($_POST["co_author_1"]);
        $co_author_2 = htmlspecialchars($_POST["co_author_2"]);
        $others = htmlspecialchars($_POST["others"]);

        // Fetch user's department and program
        $user_query = $conn->prepare("SELECT department, program FROM tbl_user WHERE username = ?");
        $user_query->bind_param("s", $user_name);
        $user_query->execute();
        $user_query->store_result();

        if ($user_query->num_rows > 0) {
            $user_query->bind_result($department, $program);
            $user_query->fetch();
        } else {
            exit("Error: User details not found.");
        }
        $user_query->close();
    }

    // Handle file upload
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    function uploadFile($file, $target_dir, $prefix) {
        if (!isset($_FILES[$file]) || $_FILES[$file]["error"] !== 0) {
            echo "<script>alert('File upload error! Please try again.'); window.location.href='index.php';</script>";
            exit();
        }

        $file_ext = strtolower(pathinfo($_FILES[$file]["name"], PATHINFO_EXTENSION));

        if ($file_ext !== "pdf") {
            echo "<script>alert('The system only accepts PDF Files!'); window.location.href='index.php';</script>";
            exit();
        }

        $unique_name = uniqid($prefix, true) . '.' . $file_ext;
        $target_file = $target_dir . $unique_name;

        if (!move_uploaded_file($_FILES[$file]["tmp_name"], $target_file)) {
            echo "<script>alert('File upload failed. Try again.'); window.location.href='index.php';</script>";
            exit();
        }

        return $target_file;
    }

    // Handle Research Paper Upload or Retain Existing File
    $file_research_paper = !empty($_FILES['file_research_paper']['name']) 
        ? uploadFile("file_research_paper", $target_dir, "research_") 
        : (isset($_POST['existing_research_paper']) ? $_POST['existing_research_paper'] : null);

    // Handle Abstract Upload or Retain Existing File
    $file_abstract = !empty($_FILES['file_abstract']['name']) 
        ? uploadFile("file_abstract", $target_dir, "abstract_") 
        : (isset($_POST['existing_abstract']) ? $_POST['existing_abstract'] : null);

    if ($file_research_paper && $file_abstract) {
        if (!empty($_POST['submission_id'])) {
            $submission_id = htmlspecialchars($_POST['submission_id']);
            $stmt = $conn->prepare("UPDATE tbl_fileUpload 
                        SET title = ?, main_author = ?, co_author_1 = ?, co_author_2 = ?, others = ?, 
                            file_research_paper = ?, file_abstract = ? 
                        WHERE submission_id = ?");
            $stmt->bind_param("ssssssss", $title, $main_author, $co_author_1, $co_author_2, $others, 
                              $file_research_paper, $file_abstract, $submission_id);
        } else {
            // New Submission
            $submission_id = uniqid('SUB-', true);
            $stmt = $conn->prepare("INSERT INTO tbl_fileUpload 
            (submission_id, username, department, program, title, main_author, co_author_1, co_author_2, others, file_research_paper, file_abstract, date_of_submission) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

            $stmt->bind_param("sssssssssss", $submission_id, $user_name, $department, $program, $title, 
                              $main_author, $co_author_1, $co_author_2, $others, 
                              $file_research_paper, $file_abstract);
        }

        // Execute the appropriate query
        if (!$stmt->execute()) {
            echo "<script>alert('Error updating record: " . htmlspecialchars($stmt->error) . "');</script>";
        } else {
            echo "<script>alert('Record successfully updated.'); window.location.href='index.php';</script>";
        }

        $stmt->close();
    }

    // Pagination
    $limit = 5;
    $page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch user data with pagination
    $tbl_fileUpload_query = $conn->prepare("SELECT id, submission_id, date_of_submission, title, main_author, co_author_1, co_author_2, others, file_research_paper, file_abstract, notification, DATE_FORMAT(sched_proposal, '%b %d, %Y') AS formatted_sched_proposal, DATE_FORMAT(sched_final, '%b %d, %Y') AS formatted_sched_final, research_status
                                        FROM tbl_fileUpload WHERE username = ? ORDER BY date_of_submission DESC LIMIT ? OFFSET ?");
    $tbl_fileUpload_query->bind_param("sii", $user_name, $limit, $offset);
    $tbl_fileUpload_query->execute();
    $tbl_fileUpload_query->store_result();
    $tbl_fileUpload_query->bind_result($id, $submission_id, $date_of_submission, $title, $main_author, $co_author_1, $co_author_2, $others, $file_research_paper, $file_abstract, $notification, $sched_proposal, $sched_final, $research_status);

    // Total records for pagination
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
                <input type="hidden" name="submission_id" id="submission_id" 
                     value="<?php echo isset($submission_id) ? htmlspecialchars($submission_id) : ''; ?>">
                <input type="hidden" name="existing_research_paper" id="existing_research_paper" value="<?php echo isset($file_research_paper) ? htmlspecialchars($file_research_paper) : ''; ?>">
                <input type="hidden" name="existing_abstract" id="existing_abstract" value="<?php echo isset($file_abstract) ? htmlspecialchars($file_abstract) : ''; ?>">

                <div class="row input_box">
                    <div class="col-12 col-sm-2 p-1"> <!--Research title START-->
                        <label>Research Title:</label>
                    </div>
                    <div class="col-12 col-sm-10 p-1">
                        <input type="text" name="title" id="title" placeholder="TITLE" class="form-control text-center custom-input" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
                    </div><!--Research title END-->

                    <div class="col-12 col-sm-6 col-md-4"> <!--Main Author START-->
                        <div class="row">
                            <div class="col-12 col-sm-5 p-1">
                                <label>Main Author:</label>
                            </div>
                            <div class="col-12 col-sm-7 p-1">
                                <input type="text" name="main_author" id="main_author"placeholder="Name" class="form-control text-center custom-input"  value="<?php echo isset($main_author) ? htmlspecialchars($main_author) : ''; ?>"required>
                            </div>
                        </div>
                    </div><!--Main Author END-->
                    <div class="col-12 col-sm-6 col-md-4"> <!--Co-author_1 START-->
                        <div class="row">
                            <div class="col-12 col-sm-4 p-1 custom-label">
                                <label>Co-author:</label>
                            </div>
                            <div class="col-12 col-sm-8 p-1">
                                <input type="text" name="co_author_1" id="co_author_1" placeholder="Name" class="form-control text-center custom-input" value="<?php echo isset($co_author_1) ? htmlspecialchars($co_author_1) : ''; ?>" >
                            </div>
                        </div>
                    </div><!--Co-author_1 END-->
                    <div class="col-12 col-sm-12 col-md-4"> <!--Co-author_2 START-->
                        <div class="row">
                            <div class="col-12 col-sm-2 col-md-4 p-1 custom-label">
                                <label>Co-author:</label>
                            </div>
                            <div class="col-12 col-sm-10 col-md-8 p-1">
                                <input type="text" name="co_author_2" id="co_author_2" placeholder="Name" class="form-control text-center custom-input" value="<?php echo isset($co_author_2) ? htmlspecialchars($co_author_2) : ''; ?>">
                            </div>
                        </div>
                    </div><!--Co-author_2 END-->
                    <div class="col-12"> <!--others START-->
                        <div class="row">
                            <div class="col-12 col-sm-1 p-1">
                                <label>More Authors:</label>
                            </div>
                            <div class="col-12 col-sm-10  offset-sm-1 p-1">
                                <textarea type="text" name="others" id="others" placeholder="Type here..." class="form-control custom-input" value="<?php echo isset($others) ? htmlspecialchars($others) : ''; ?>"></textarea>
                            </div>
                        </div>
                    </div><!--Others END-->

                    <div class="col-12 col-md-6">
                        <div class="row">
                            <div class="row"> <!--File Attachment (Research Paper) START-->
                                <div class="col-12 col-md-6  p-1">
                                    <label>Attach your research paper here:</label>
                                </div>
                                <div class="col-12 col-md-7 p-1">
                                    <input type="file" name="file_research_paper" id="file_research_paper" class="form-control custom-input" <?php echo isset($_POST['submission_id']) ? '' : 'required'; ?> required>
                                    <small id="current_research_paper"></small>
                                </div>
                            </div><!--File Attachment (Research Paper) END-->
                            <div class="row"> <!--File Attachment (Abstract) START-->
                                <div class="col-12 col-md-5  p-1">
                                    <label>Attach your abstract here:</label>
                                </div>
                                <div class="col-12 col-md-8 p-1">
                                    <input type="file" name="file_abstract" id="file_abstract" class="form-control custom-input" <?php echo isset($_POST['submission_id']) ? '' : 'required'; ?> required>
                                    <small id="current_abstract"></small>
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
                                    echo "<td><button 
                                            class='btn-resubmit' 
                                            data-id='" . $id . "' 
                                            data-title='" . htmlspecialchars($title) . "'
                                            data-main_author='" . htmlspecialchars($main_author) . "'
                                             data-co_author_1='" . htmlspecialchars($co_author_1) . "'
                                            data-co_author_2='" . htmlspecialchars($co_author_2) . "'
                                            data-others='" . htmlspecialchars($others) . "'
                                            data-research_paper='" . htmlspecialchars($file_research_paper) . "'
                                            data-abstract='" . htmlspecialchars($file_abstract) . "'
                                        >
                                        Edit
                                        </button></td>";
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editButtons = document.querySelectorAll('.btn-resubmit');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const submissionId = this.getAttribute('data-id');
                document.getElementById('submission_id').value = submissionId;

                document.getElementById('title').value = this.getAttribute('data-title');
                document.getElementById('main_author').value = this.getAttribute('data-main_author');
                document.getElementById('co_author_1').value = this.getAttribute('data-co_author_1');
                document.getElementById('co_author_2').value = this.getAttribute('data-co_author_2');
                document.getElementById('others').value = this.getAttribute('data-others');

                const researchPaper = this.getAttribute('data-research_paper');
                const abstractFile = this.getAttribute('data-abstract');

                document.getElementById('existing_research_paper').value = researchPaper;
                document.getElementById('existing_abstract').value = abstractFile;

                document.getElementById('current_research_paper').innerHTML = 
                    `Current: <a href="/uploads/${researchPaper}" target="_blank">${researchPaper}</a>`;

                document.getElementById('current_abstract').innerHTML = 
                    `Current: <a href="/uploads/${abstractFile}" target="_blank">${abstractFile}</a>`;

                document.getElementById('file_research_paper').required = false;
                document.getElementById('file_abstract').required = false;


                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    });
</script>

</body>
</html>
