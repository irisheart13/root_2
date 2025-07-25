<?php
session_start();
include '../../conn.php';

// Check if user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: general_User.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deparment = htmlspecialchars($_SESSION['department']);
    $program = htmlspecialchars($_SESSION['program']);

    $id = intval($_POST['id']);
    $title = htmlspecialchars($_POST['title']);
    $main_author = htmlspecialchars($_POST['main_author']);
    $co_author_1 = htmlspecialchars($_POST['co_author_1']);
    $co_author_2 = htmlspecialchars($_POST['co_author_2']);
    $others = htmlspecialchars($_POST['others']);

    // Paths for saving files in their respective folders
    $upload_dir_research = "uploadedFile/$deparment/$program/research/";
    $upload_dir_abstract = "uploadedFile/$deparment/$program/abstract/";

    if (!is_dir($upload_dir_research)) mkdir($upload_dir_research, 0777, true);
    if (!is_dir($upload_dir_abstract)) mkdir($upload_dir_abstract, 0777, true);

    // Fetch existing file names
    $query = $conn->prepare("SELECT file_research_paper, file_abstract FROM tbl_fileUpload WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $query->store_result();
    $query->bind_result($file_research_paper, $file_abstract);
    $query->fetch();
    $query->close();

    // Function to handle file upload and replacement
    function uploadAndReplaceFile($fileInput, $target_dir, $existing_file) {
        if (!empty($_FILES[$fileInput]['name'])) {
            $new_file = basename($_FILES[$fileInput]["name"]);
            $target_file = $target_dir . $new_file;

            // Delete the old file if it exists
            if (!empty($existing_file) && file_exists($target_dir . $existing_file)) {
                unlink($target_dir . $existing_file);
            }

            // Move the new file
            if (move_uploaded_file($_FILES[$fileInput]["tmp_name"], $target_file)) {
                return $new_file; // New file uploaded
            } else {
                echo "<script>alert('Failed to upload " . htmlspecialchars($new_file) . ". Please try again.');</script>";
                return $existing_file; // Keep old file if upload fails
            }
        }
        return $existing_file; // No new file uploaded
    }

    $new_research_paper = uploadAndReplaceFile("new_research_paper", $upload_dir_research, $file_research_paper);
    $new_abstract = uploadAndReplaceFile("new_abstract", $upload_dir_abstract, $file_abstract);

    // Update database
    $update_query = $conn->prepare("UPDATE tbl_fileUpload SET title=?, main_author=?, co_author_1=?, co_author_2=?, others=?, file_research_paper=?, file_abstract=? WHERE id=?");
    $update_query->bind_param("sssssssi", $title, $main_author, $co_author_1, $co_author_2, $others, $new_research_paper, $new_abstract, $id);

    if ($update_query->execute()) {
        echo "<script>alert('Submission updated successfully!'); window.location.href='general_User.php';</script>";
    } else {
        echo "<script>alert('Failed to update submission. Please try again.');</script>";
    }

    $update_query->close();
}
?>
