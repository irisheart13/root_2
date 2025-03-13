<?php
    session_start();
    include 'conn.php';

    // Verification of user if they are admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
        header("Location: index.php");
        exit();
    }
    $user_name = htmlspecialchars($_SESSION['username']);

    // Get admin's department and program from session
    $admin_department = $_SESSION['department']; 
    $admin_program = $_SESSION['program'];

    // Fetch only records from the admin's department and program
    $sql = "SELECT * FROM tbl_fileUpload WHERE department=? AND program=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $admin_department, $admin_program);
    $stmt->execute();
    $result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ADMIN</title>

    <link href="/Root_1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/Root_1/dist/js/bootstrap.bundle.min.js"></script>

</head>
<style>
    /* Poppins Regular */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Regular.ttf') format('truetype');
        font-weight: 400;
        font-style: normal;
    }

    /* Poppins Bold */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Bold.ttf') format('truetype');
        font-weight: 700;
        font-style: normal;
    }

    /* Poppins Italic */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Italic.ttf') format('truetype');
        font-weight: 400;
        font-style: italic;
    }

    /* Poppins Light */
    @font-face {
        font-family: 'Poppins';
        src: url('/Root_1/Poppins/Poppins-Light.ttf') format('truetype');
        font-weight: 300;
        font-style: normal;
    }

    body{
        margin: 0;
        padding: 0;
        overflow-y: visible;
        overflow-x:hidden;

        font-family: 'Poppins';

        background-color: #dee2e6;
    }

    .container-fluid{
        margin:0;
        padding:0;
    }
    /*Nav Bar Design START*/
    .nav-bar{
        position: absolute;
        top: 0;
        left: 0;

        width: 100vw;

        margin: 0;
        padding: 0;

        background-color: #212529;
    }
   .logout{
        display: flex;
        justify-content: flex-end;
        padding-top: 15px;
    }
    .btn-logout{
        margin-right: 20px;

        border: none;
        border-radius: 10px;

        width: 100px;
        height: 25px;
        background-color: #f5f5f5;
    }
    .user-display{
        display: flex;
        align-items: center;
        padding-top: 15px;
        padding-left: 30px;

        text-align: start; 
        vertical-align: middle;
        font-size: 18px;
        color: white;
    }
    /*Nav Bar Design END*/

    /*Table Design START*/
    .dashboard{
        padding:0px;
        margin-top: 60px;
    }
    .table-responsive {
        width: 100%;
        overflow-x: auto; 
        sheight: 100%; 
    }
    .custom-table {
        table-layout: fixed; 
        width: 1500px; 
        white-space: nowrap; 
    }
    .custom-table th, .custom-table td {
        width: 150px; 
        text-overflow: ellipsis; 
        overflow: hidden;
    }
    .custom-table td.wrap, .custom-table th.wrap {
        white-space: normal !important; /* Allows text wrapping */
        word-wrap: break-word; 
        overflow-wrap: break-word;
    }
    .custom-select{
        width: 90%;
        border-radius: 5px;
        border-style: none;
        padding: 5px;

        background-color: #80b918;
        color: white;
    }
    .custom-option{
        background-color: #f5f5f5;
        color: black;
    }
    .custom-date{
        width: 100%;
    }
    .btn-submit{
        margin-top: 5px;
        border-radius: 5px;
        border-width: 1px;

        background-color: #ffff3f;
    }
    /*Table Design END*/

    .switch {
        position: relative;
        display: inline-block;
        width: 34px;
        height: 20px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 20px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 14px;
        width: 14px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #4CAF50;
    }

    input:checked + .slider:before {
        transform: translateX(14px);
    }

    .btn-edit {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
    }

    .btn-edit:disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }
</style>
<body>
    <div class="container-fluid">
        <!--Nav Section START-->
        <div class="row nav-bar">
            <div class="col-6 user-display">
                <p>Hello, <?php echo $user_name; ?>!</p>
            </div>
            <div class="col-6 logout">
                <form action="/Root_1/logout.php" method="post">
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>
        </div>
        <!--Nav Section END-->

        <!--File Dashboard START-->
        <div class="dashboard">
            <div class="table-responsive">
                <div class="row search-row p-2">
                    <div class="col-3">
                        <input type="text" id="searchInput" placeholder="Search..." onkeyup="filterTable()">
                    </div>
                    <!-- <div class="col-1">
                        <label for="sortSelect">Sort by:</label>
                        <select id="sortSelect" onchange="sortTableByDropdown()">
                            <option value="">Select Column</option>
                            <option value="0">Date of Submission</option>
                            <option value="1">Title</option>
                            <option value="2">Main Author</option>
                            <option value="3">Co-Author 1</option>
                            <option value="4">Co-Author 2</option>
                            <option value="5">More Authors</option>
                            <option value="11">Research Status</option>
                        </select>
                    </div> -->
                </div>
                <table class="table table-striped custom-table" id="researchTable">
                    <thead>
                        <tr>
                            <th class="wrap" onclick="sortTable(0)">Date of Submission</th>
                            <th onclick="sortTable(1)">Title</th>
                            <th onclick="sortTable(2)">Main Author</th>
                            <th onclick="sortTable(3)">Co-Author 1</th>
                            <th onclick="sortTable(4)">Co-Author 2</th>
                            <th onclick="sortTable(5)">More Authors</th>
                            <th class="wrap">Uploaded Research Paper</th>
                            <th class="wrap">Uploaded Abstract</th>
                            <th onclick="sortTable(8)">Notifications</th>
                            <th class="wrap">Schedule for Proposal Presentation</th>
                            <th class="wrap">Schedule for Final Presentation</th>
                            <th onclick="sortTable(11)">Research Status</th>
                            <th>Edit Access</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="wrap"><?= htmlspecialchars($row['date_of_submission']) ?></td>
                                <td class="wrap"><?= htmlspecialchars($row['title']) ?></td>
                                <td class="wrap"><?= htmlspecialchars($row['main_author']) ?></td>
                                <td class="wrap"><?= htmlspecialchars($row['co_author_1']) ?></td>
                                <td class="wrap"><?= htmlspecialchars($row['co_author_2']) ?></td>
                                <td class="wrap"><?= htmlspecialchars($row['others']) ?></td>
                                <td>
                                    <a href='/Root_1/review_admin.php?id=<?= $row['id'] ?>&type=research' target='_blank'>View File</a>
                                </td>
                                <td>
                                    <a href='/Root_1/review_admin.php?id=<?= $row['id'] ?>&type=abstract' target='_blank'>View File</a>
                                </td>
                                    <form action="update_coor.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">

                                        <td>
                                            <select name="notification" class="custom-select">
                                                <option class="custom-option" value="" disabled selected>Select</option>
                                                <option class="custom-option" value="For Revision" <?= ($row['notification'] == 'For Revision') ? 'selected' : '' ?>>For Revision</option>
                                                <option class="custom-option" value="Scheduled for Research Proposal Presentation" <?= ($row['notification'] == 'Scheduled for Research Proposal Presentation') ? 'selected' : '' ?>>Scheduled for Research Proposal Presentation</option>
                                                <option class="custom-option" value="Scheduled for Final Research Presentation" <?= ($row['notification'] == 'Scheduled for Final Research Presentation') ? 'selected' : '' ?>>Scheduled for Final Research Presentation</option>
                                                <option class="custom-option" value="Please See Comments" <?= ($row['notification'] == 'Please See Comments') ? 'selected' : '' ?>>Please See Comments</option>
                                            </select>
                                        </td>

                                        <td>
                                            <input type="date" class="custom-date" name="sched_proposal" value="<?= htmlspecialchars($row['sched_proposal']) ?>">
                                        </td>

                                        <td>
                                            <input type="date" class="custom-date" name="sched_final" value="<?= htmlspecialchars($row['sched_final']) ?>">
                                        </td>

                                        <td>
                                            <select name="research_status" class="custom-select">
                                                <option class="custom-option" value="" disabled selected>Select</option>
                                                <option class="custom-option" value="Presented" <?= ($row['research_status'] == 'Presented') ? 'selected' : '' ?>>Presented</option>
                                                <option class="custom-option" value="Implemented" <?= ($row['research_status'] == 'Implemented') ? 'selected' : '' ?>>Implemented</option>
                                            </select>
                                        </td>

                                        <td>
                                            <label class="switch">
                                                <input type="checkbox" name="edit_access" value="1" <?= $row['edit_access'] ? 'checked' : '' ?> onchange="this.form.submit()">
                                                <span class="slider round"></span>
                                            </label>
                                            <input type="hidden" name="toggle_edit_access" value="1">
                                        </td>

                                        <td>
                                            <button type="submit" name="update_status" class="btn-submit">Save</button>
                                        </td>
                                    </form>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <!--File Dashboard END-->
    </div>
<script>
    // Script for toggle button of edit access START
    function toggleEditButton(id) {
        var toggle = document.getElementById("toggle-" + id);
        var editButton = document.getElementById("edit-btn-" + id);

        if (toggle.checked) {
            editButton.removeAttribute("disabled");
        } else {
            editButton.setAttribute("disabled", "true");
        }
    }
    // Script for toggle button of edit access END

    // Script for search function 
    function filterTable() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let table = document.getElementById("researchTable");
        let rows = table.getElementsByTagName("tr");

        for (let i = 1; i < rows.length; i++) {
            let cells = rows[i].getElementsByTagName("td");
            let rowText = "";
            for (let j = 0; j < cells.length; j++) {
                rowText += cells[j].textContent.toLowerCase();
            }
            rows[i].style.display = rowText.includes(input) ? "" : "none";
        }
    }
</script>
</body>
</html>