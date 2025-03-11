<?php
    session_start();
    include '../../../conn.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = intval($_POST['id']);
        $title = htmlspecialchars($_POST['title']);
        $main_author = htmlspecialchars($_POST['main_author']);
        $co_author_1 = htmlspecialchars($_POST['co_author_1']);
        $co_author_2 = htmlspecialchars($_POST['co_author_2']);
        $others = htmlspecialchars($_POST['others']);
        $upload_dir = "uploads/";

        // Fetch existing file names
        $query = $conn->prepare("SELECT file_research_paper, file_abstract FROM tbl_fileUpload WHERE id = ?");
        $query->bind_param("i", $id);
        $query->execute();
        $query->store_result();
        $query->bind_result($file_research_paper, $file_abstract);
        $query->fetch();
        $query->close();

        // Handle Research Paper Upload
        if (!empty($_FILES['new_research_paper']['name'])) {
            $new_research_paper = basename($_FILES["new_research_paper"]["name"]);
            move_uploaded_file($_FILES["new_research_paper"]["tmp_name"], $upload_dir . $new_research_paper);
        } else {
            $new_research_paper = $file_research_paper;
        }

        // Handle Abstract Upload
        if (!empty($_FILES['new_abstract']['name'])) {
            $new_abstract = basename($_FILES["new_abstract"]["name"]);
            move_uploaded_file($_FILES["new_abstract"]["tmp_name"], $upload_dir . $new_abstract);
        } else {
            $new_abstract = $file_abstract;
        }

        // Update database
        $update_query = $conn->prepare("UPDATE tbl_fileUpload SET title=?, main_author=?, co_author_1=?, co_author_2=?, others=?, file_research_paper=?, file_abstract=? WHERE id=?");
        $update_query->bind_param("sssssssi", $title, $main_author, $co_author_1, $co_author_2, $others, $new_research_paper, $new_abstract, $id);

        if ($update_query->execute()) {
            echo "Submission updated successfully!";
        } else {
            echo "Failed to update submission.";
        }

        $update_query->close();
    }
?>
