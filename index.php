<?php
session_start();
include 'login/conn.php'; // Ensure database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['pin'];
    $department = $_POST['department'];
    $program = $_POST['program'];

    // Validate login
    $sql = "SELECT * FROM users WHERE username=? AND pin=? AND department=? AND program=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $password, $department, $program);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['username'] = $username;
        $_SESSION['department'] = $department;
        $_SESSION['program'] = $program;

        // Redirect based on department and program
        header("Location: login/$department/$program/index.php");
        exit();
    } 


    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="index.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <title>Login</title>
</head>
<body>
    <div class = "container">
        <div class="position-absolute top-50 start-50 translate-middle">
            <div id="login" >
                <form method="POST" action="" class="form_login">
                    <label for="department">Department:</label>
                    <select id="department" name="department" onchange="loadPrograms(this.value)" required>
                        <option value="">Select Department</option>
                        <option value="CITCS">CITCS</option>
                        <option value="CBA">CBA</option>
                        <option value="CAS">CAS</option>
                        <option value="CTE">CTE</option>
                        <option value="CCJ">CCJ</option>
                        <!-- Add more departments as needed -->
                    </select>

                    <label for="program">Program:</label>
                    <select id="program" name="program" required>
                        <option value="">Select Program</option>
                        <!-- Programs will be dynamically loaded here -->
                    </select>

                    <br>

                    <label>Username:</label>
                    <input type="text" name="username" required>
                    <br>
                    <label>Pin:</label>
                    <input type="password" name="pin" required>
                    <br>
                    <input type="submit" value="Login">
                </form>

            <?php
            if (!empty($error_message)) {
                echo "<p style='color: red;'>$error_message</p>";
            }
            ?>
        </div>
    </div>
</body>
<script>
    function loadPrograms(department) {
        const programs = {
            CITCS: ['ACT','BSCS', 'BSIT'],
            CBA: ['BSA','BSBA - HRDM', 'BSBA - MM', 'BSBA - OM'],
            CAS: ['ABPolSci', 'ABMassComm', 'BSPsy'],
            CTE: ['BEED - GEE', 'BEED - ECE','BEED - SE', 'BSED - Science', 'BSED - English', 'BSED - Filipino', 'BSED - Mathematics', 'BSED - MAPEH', 'BSED - Social Science']
        };

        const programSelect = document.getElementById('program');
        programSelect.innerHTML = '<option value="">Select Program</option>';

        if (programs[department]) {
            programs[department].forEach(prog => {
                const option = document.createElement('option');
                option.value = prog;
                option.textContent = prog;
                programSelect.appendChild(option);
            });
        }
    }
</script>
</html>
