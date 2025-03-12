<?php
	session_start();
    include 'conn.php';
    
	// Get admin's department and program from session
    $admin_department = $_SESSION['department']; 
    $admin_program = $_SESSION['program'];

    // Handle updating status (without affecting toggle switch)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
        $id = $_POST['id'];
        $notif = !empty($_POST['notification']) ? $_POST['notification'] : NULL;
        $sched_proposal = !empty($_POST['sched_proposal']) ? $_POST['sched_proposal'] : NULL;
        $sched_final = !empty($_POST['sched_final']) ? $_POST['sched_final'] : NULL;
        $research_status = !empty($_POST['research_status']) ? $_POST['research_status'] : NULL;

        $sql = "UPDATE tbl_fileUpload SET notification=?, sched_proposal=?, sched_final=?, research_status=? WHERE id=? AND department=? AND program=?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssssiss", $notif, $sched_proposal, $sched_final, $research_status, $id, $admin_department, $admin_program);
            if ($stmt->execute()) {
                echo "<script>alert('Record updated successfully!'); window.location.href='research_coor.php';</script>";
                exit();
            } else {
                echo "Error updating record: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error in preparing statement: " . $conn->error;
        }
    }

    // Handle toggle switch separately (only updates edit_access)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_edit_access'])) {
        $id = $_POST['id'];
        $edit_access = isset($_POST['edit_access']) ? 1 : 0;

        $sql = "UPDATE tbl_fileUpload SET edit_access=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ii", $edit_access, $id);
            if ($stmt->execute()) {
                echo "<script>window.location.href='research_coor.php';</script>";
                exit();
            } else {
                echo "Error updating edit access: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error in preparing statement: " . $conn->error;
        }
    }
?>
