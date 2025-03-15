<?php
    session_start(); 
    include '../../../conn.php'; 

    // Check if user is logged in
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
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

        // Handle file upload
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        function uploadFile($file, $target_dir, $prefix) {
            if (!isset($_FILES[$file]) || $_FILES[$file]["error"] !== 0) {
                return null;
            }

            $file_ext = strtolower(pathinfo($_FILES[$file]["name"], PATHINFO_EXTENSION));

            if ($file_ext !== "pdf") {
                echo "<script>alert('The system only accepts PDF Files!'); window.location.href='index.php';</script>";
                exit();
            }

            $file_name = $prefix . "_" . basename($_FILES[$file]["name"]);
            $target_file = $target_dir . $file_name;

            if (!move_uploaded_file($_FILES[$file]["tmp_name"], $target_file)) {
                echo "<script>alert('File upload failed. Try again.'); window.location.href='index.php';</script>";
                exit();
            }

            return $file_name;
        }

        // Handle Research Paper Upload
        $file_research_paper = uploadFile("file_research_paper", $target_dir, "Research");
        
        // Handle Abstract Upload
        $file_abstract = uploadFile("file_abstract", $target_dir, "Abstract");

        if ($file_research_paper && $file_abstract) {
            $stmt = $conn->prepare("INSERT INTO tbl_fileUpload 
            (username, department, program, title, main_author, co_author_1, co_author_2, others, file_research_paper, file_abstract, date_of_submission) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

            $stmt->bind_param("ssssssssss", $user_name, $department, $program, $title, 
                              $main_author, $co_author_1, $co_author_2, $others, 
                              $file_research_paper, $file_abstract);

            if (!$stmt->execute()) {
                echo "<script>alert('Error inserting record: " . htmlspecialchars($stmt->error) . "');</script>";
            } else {
                echo "<script>alert('Record successfully submitted.'); window.location.href='index.php';</script>";
            }

            $stmt->close();
        }
    }
?>
