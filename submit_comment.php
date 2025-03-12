<?php
include 'conn.php';  // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $file_id = $_POST['id'] ?? '';  // Get the file ID from the form
    $type = $_POST['type'] ?? 'research';  // Get the type from the form

    $title = $_POST['title'] ?? '';
    $abstract = $_POST['abstract'] ?? '';
    $others = $_POST['others'] ?? '';

    // Ensure at least one comment field is filled and file_id is valid
    if (!empty($file_id) && (!empty($title) || !empty($abstract) || !empty($others))) {
        // Check if the file_id exists in tbl_fileupload
        $fileCheck = $conn->prepare("SELECT id FROM tbl_fileupload WHERE id = ?");
        $fileCheck->bind_param("i", $file_id);
        $fileCheck->execute();
        $result = $fileCheck->get_result();

        if ($result->num_rows > 0) {  // File exists
            // Insert the comment linked to the file
            $sql = "INSERT INTO admin_comments (title, abstract, others, file_id) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $title, $abstract, $others, $file_id);

            if ($stmt->execute()) {
                echo "<script>
                    alert('Comment successfully submitted.');
                    window.location.href='review_admin.php?id=$file_id&type=$type';
                </script>";
            } else {
                echo "<script>alert('Error submitting comment.');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('Invalid file.');</script>";
        }

        $fileCheck->close();
        $conn->close();
    } else {
        echo "<script>alert('Please enter a comment before submitting.');</script>";
    }
}
?>
