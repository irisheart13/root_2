<?php
    session_start();
    include '../../conn.php';

    // Check if user is logged in
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
        header("Location: general_User.php");
        exit();
    }

    $user_name = htmlspecialchars($_SESSION['username']);
    $department = htmlspecialchars($_SESSION['department']);
    $program = htmlspecialchars($_SESSION['program']);

    $role = $_GET['role'] ?? 'general_user';
    $id = $_GET['id'] ?? null;
    $type = $_GET['type'] ?? 'research'; // Default to 'research'

    // Validate type early
    $allowed_types = ['research', 'abstract'];
    if (!in_array($type, $allowed_types)) {
        die("Invalid file type."); 
    }

    // Validate GET parameters
    if (!isset($_GET['id']) || !isset($_GET['type'])) {
        die("Invalid request.");
    }

    // Fetch file names from database
    $query = $conn->prepare("SELECT file_research_paper, file_abstract FROM tbl_fileUpload WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $query->store_result();
    $query->bind_result($file_research_paper, $file_abstract);
    $query->fetch();
    $query->close();

    // Determine the correct file path based on type
    $base_dir = "uploadedFile/$department/$program";

    if ($type === "research") {
        $file_path = "$base_dir/research/$file_research_paper";
    } elseif ($type === "abstract") {
        $file_path = "$base_dir/abstract/$file_abstract";
    } else {
        die("Invalid file type.");
    }

    // Check if file exists
    if (!$file_path || !file_exists($file_path)) {
        die("File not found.");
    }

   // Fetch comments with reviewer info and filter by file type
    $sql = "SELECT ac.title, ac.abstract, ac.others, u.username AS reviewer_name, ac.created_at
    FROM admin_comments ac
    LEFT JOIN tbl_user u ON ac.coor_id = u.id
    WHERE ac.file_id = ? AND ac.file_type = ?
    ORDER BY ac.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id, $type); // "i" for file_id, "s" for file_type (research/abstract)
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = $result->fetch_all(MYSQLI_ASSOC); // Fetch all results as an array

    $stmt->close();
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

    <link href="review_user.css" rel="stylesheet">
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
                <div class="col-6 col-md-3 offset-md-6 d-flex align-items-center justify-content-end ">
                    <span class="txt-email align-items-center">plmuncomm@plmun.edu.ph</span>
                </div>
            </div>
        </section>
        <!--Nav Section END-->

        <!--Pdf Comment Section START-->
        <section class="pdfCommentSection">
            <iframe id="pdfViewer" src="<?= htmlspecialchars($file_path) ?>" width="100%" height="600px" style="border: none;"></iframe>
        </section>
        <!--Pdf Comment Section END-->

    <!-- Recent Comment Section START -->
    <section class="recentCommentSection">
        <div class="row">
            <div class="col-9 mx-auto p-0">
                <span class="txt-recentComment">RECENT COMMENTS</span>
                <div class="row g-2">
                    <?php if (!empty($comments)) : ?>
                        <?php foreach ($comments as $comment) : ?>
                            <div class="col-12 comBox">
                                <div class="row">
                                    <!-- Reviewer Name -->
                                    <div class="col-12 txt-commentBox mt-2">
                                        <strong>Reviewer:</strong> <?php echo htmlspecialchars($comment['reviewer_name']); ?>
                                    </div>

                                    <!-- Date Posted -->
                                    <div class="col-12 txt-datePosted">
                                        <span>Posted on <?php echo date("F j, Y, g:i A", strtotime($comment['created_at'])); ?></span>
                                    </div>

                                    <!-- Title -->
                                    <div class="col-12 txt-title mt-4"><span>TITLE</span></div>
                                    <div class="col-12 txt-commentBox mt-2">
                                        <?php echo htmlspecialchars($comment['title']); ?>
                                    </div>

                                    <!-- Abstract -->
                                    <div class="col-12 txt-title mt-4"><span>ABSTRACT</span></div>
                                    <div class="col-12 txt-commentBox mt-2">
                                        <?php echo htmlspecialchars($comment['abstract']); ?>
                                    </div>

                                    <!-- Others -->
                                    <div class="col-12 txt-title mt-4"><span>OTHERS</span></div>
                                    <div class="col-12 txt-commentBox mt-2">
                                        <?php echo htmlspecialchars($comment['others']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="col-12 txt-commentBox mt-2">No comments available.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <!-- Recent Comment Section END -->
    </div> 
</body>
</html>
