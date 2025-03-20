<?php
    session_start();
    include '../../conn.php';

    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coor') {
        echo "<script>alert('Error: coor not logged in. Please log in first.'); window.location.href='login.php';</script>";
        exit();
    }

    $user_name = htmlspecialchars($_SESSION['username']);
    $department = htmlspecialchars($_SESSION['department']);
    $program = htmlspecialchars($_SESSION['program']);

    // Validate GET parameters
    if (!isset($_GET['id']) || !isset($_GET['type'])) {
        die("Invalid request.");
    }

    $id = intval($_GET['id']);
    $type = $_GET['type']; // "research" or "abstract"

    // Fetch file names from database
    $query = $conn->prepare("SELECT file_research_paper, file_abstract FROM tbl_fileUpload WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $query->store_result();
    $query->bind_result($file_research_paper, $file_abstract);
    $query->fetch();
    $query->close();

    $base_dir = "../general_user/uploadedFile/$department/$program";

    if ($type === "research") {
        $file_path = "$base_dir/research/$file_research_paper";
    } elseif ($type === "abstract") {
        $file_path = "$base_dir/abstract/$file_abstract";
    } else {
        die("Invalid file type.");
    }

    // Debug the path
    // echo "File Path: " . $file_path . "<br>";

    // Check if file exists
    if (!$file_path || !file_exists($file_path)) {
        die("File not found.");
    }

    $conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Review Document</title>


    <link href="/root_2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/root_2/dist/js/bootstrap.bundle.min.js"></script>

    <link href="review_coor.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid main">
        <!--Nav Section START-->
        <section class="navBarSection">
            <div class="row">
                <div class="col-2 col-md-1 p-0 d-flex align-items-center justify-content-center justify-content-md-end logo">
                    <img src="/Root_2/img/plmun_logo.png" alt="logo" class="img-logo">
                </div>
                <div class="col-4 col-md-2 welcome p-0 ps-md-2 d-flex align-items-center">
                    <span class="txt-welcome">WELCOME</span>
                </div>
                <div class="col-6 col-md-3 offset-md-6 d-flex align-items-center justify-content-end p-0">
                    <span class="txt-email align-items-center wrap">plmuncomm@plmun.edu.ph</span>
                </div>
            </div>
        </section>
        <!--Nav Section END-->

        <!--Pdf Comment Section START-->
        <section class="pdfCommentSection">
            <div class=row>
                <div class="col-12 col-md-9 p-0">
                    <!-- PDF VIEWER USING IFRAME -->
                    <iframe id="pdfViewer" src="<?= htmlspecialchars($file_path) ?>" width="100%" height="600px" style="border: none;"></iframe>
                </div>
                <div class="col-12 col-md-3 p-0 commentSection">
                    <form method="POST" action="submitComment_coor.php">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>">
                        <input type="hidden" name="type" value="<?= htmlspecialchars($_GET['type'] ?? 'research') ?>">
                            <div class="row com_sec m-0 ">
                                <div class="col-12">
                                    <label class="txt-comment">COMMENT HERE YOUR INSIGHTS ABOUT THE</label>
                                </div>
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
                    </form>
                </div>
            </div>
        </section>
        <!--Pdf Comment Section END-->

        <!-- Recent Comment Section START -->
        <section class="recentCommentSection">
            <div class="row">
                <div class="col-11 mx-auto">

                </div>
            </div>
        </section>
        <!-- Recent Comment Section END -->
    </div>

</body>
</html>
