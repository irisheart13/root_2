<?php
    session_start();
    include '../../conn.php';  

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        $file_id = $_POST['id'] ?? '';  
        $type = $_POST['type'] ?? 'research';  

        $title = $_POST['title'] ?? '';
        $abstract = $_POST['abstract'] ?? '';
        $others = $_POST['others'] ?? '';

        // Get the logged-in admin ID
        $admin_id = $_SESSION['admin_id'] ?? null;

        if (!empty($file_id) && !empty($admin_id) && (!empty($title) || !empty($abstract) || !empty($others))) {
            // Check if the file exists in tbl_fileupload
            $fileCheck = $conn->prepare("SELECT id FROM tbl_fileupload WHERE id = ?");
            $fileCheck->bind_param("i", $file_id);
            $fileCheck->execute();
            $result = $fileCheck->get_result();

            if ($result->num_rows > 0) {  // File exists
                // Insert comment linked to file & admin
                $sql = "INSERT INTO admin_comments (admin_id, title, abstract, others, file_id) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssi", $admin_id, $title, $abstract, $others, $file_id);

                if ($stmt->execute()) {
                    echo "<script>
                        alert('Comment successfully submitted.');
                        window.location.href='review_coor.php?id=$file_id&type=$type';
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
            echo "<script>alert('Please enter a comment and ensure admin ID is set.');</script>";
        }
    }

?>
