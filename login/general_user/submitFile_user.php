<?php
    session_start(); 
    include '../../conn.php'; 

    // Check if user is logged in
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
        header("Location: general_User.php");
        exit();
    }

    $user_name = htmlspecialchars($_SESSION['username']);
    $deparment = htmlspecialchars($_SESSION['department']);
    $program = htmlspecialchars($_SESSION['program']);

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

        // File upload function
        function uploadFile($file, $target_dir) {
            if (!isset($_FILES[$file]) || $_FILES[$file]["error"] !== 0) {
                return null;
            }

            $file_ext = strtolower(pathinfo($_FILES[$file]["name"], PATHINFO_EXTENSION));
            if ($file_ext !== "pdf") {
                echo "<script>alert('The system only accepts PDF Files!'); window.location.href='general_User.php';</script>";
                exit();
            }

            // Save without prefix and ensure replacement
            $file_name = basename($_FILES[$file]["name"]);
            $target_file = $target_dir . $file_name;

            // Delete existing file if it already exists (for replacement)
            if (file_exists($target_file)) {
                unlink($target_file);  // Removes the old file
            }

            if (!move_uploaded_file($_FILES[$file]["tmp_name"], $target_file)) {
                echo "<script>alert('File upload failed. Try again.'); window.location.href='general_User.php';</script>";
                exit();
            }

            return $file_name;
        }

        // Define target directories by document type
        $target_dir_research = "uploadedFile/$department/$program/research/";
        $target_dir_abstract = "uploadedFile/$department/$program/abstract/";

        if (!is_dir($target_dir_research)) mkdir($target_dir_research, 0777, true);
        if (!is_dir($target_dir_abstract)) mkdir($target_dir_abstract, 0777, true);

        // Handle uploads
        $file_research_paper = uploadFile("file_research_paper", $target_dir_research);
        $file_abstract = uploadFile("file_abstract", $target_dir_abstract);

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
                echo "<script>alert('Record successfully submitted.'); window.location.href='general_User.php';</script>";
            }

            $stmt->close();
        }
    }
?>
