<?php
session_start();
include '../../conn.php';  

// Debugging: Check if session variables exist
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coor') {
    echo "<script>alert('Error: coor not logged in. Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

if (!isset($_SESSION['coor_id'])) {
    echo "<script>alert('Error: coor_id is not set in session. Please log in again.'); window.location.href='login.php';</script>";
    exit();
}

// Store session values after validation
$coor_id = $_SESSION['coor_id'];

// Debugging: Check session contents
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Ensure session is saved
session_write_close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $file_id = $_POST['id'] ?? '';
    $type = $_POST['type'] ?? 'research';

    $title = $_POST['title'] ?? '';
    $abstract = $_POST['abstract'] ?? '';
    $others = $_POST['others'] ?? '';

    // Debugging: Check received values
    echo "<script>console.log('File ID: $file_id, Title: $title, Abstract: $abstract, Others: $others');</script>";

    // Ensure at least one comment field is filled
    if (!empty($file_id) && isset($coor_id) && 
        (trim($title) !== '' || trim($abstract) !== '' || trim($others) !== '')) {

        // Verify file exists
        $fileCheck = $conn->prepare("SELECT id FROM tbl_fileupload WHERE id = ?");
        $fileCheck->bind_param("i", $file_id);
        $fileCheck->execute();
        $result = $fileCheck->get_result();

        if ($result->num_rows > 0) {  
            // Insert comment
            $sql = "INSERT INTO admin_comments (title, abstract, others, file_id, coor_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $title, $abstract, $others, $file_id, $coor_id);

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
    } else {
        echo "<script>alert('Please enter a comment before submitting and ensure you are logged in.');</script>";
    }
}

// Close connection
$conn->close();
?>

