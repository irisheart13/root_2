<?php
    session_start();
    include '../../conn.php';

    // Verification of user if they are admin
    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'coor') {
        header("Location: index.php");
        exit();
    }
    $user_name = htmlspecialchars($_SESSION['username']);

    // Get admin's department and program from session
    $admin_department = $_SESSION['department']; 
    $admin_program = $_SESSION['program'];

    // Pagination
    $limit = 5;
    $page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch user data with pagination (Filter by department & program)
    $sql = "SELECT id, 
                DATE_FORMAT(date_of_submission, '%b %d, %Y %h:%i %p') AS formatted_date_of_submission, username,
                title, main_author, co_author_1, co_author_2, others, file_research_paper, file_abstract, notification, 
                DATE_FORMAT(sched_proposal, '%b %d, %Y') AS formatted_sched_proposal, 
                DATE_FORMAT(sched_final, '%b %d, %Y') AS formatted_sched_final, 
                research_status, edit_access
            FROM tbl_fileUpload 
            WHERE department = ? AND program = ?
            ORDER BY date_of_submission DESC 
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $admin_department, $admin_program, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    // Total records for pagination
    $total_query = $conn->prepare("SELECT COUNT(*) FROM tbl_fileUpload WHERE department = ? AND program = ?");
    $total_query->bind_param("ss", $admin_department, $admin_program);
    $total_query->execute();
    $total_query->bind_result($total_rows);
    $total_query->fetch();
    $total_query->close();
    
    $total_pages = max(ceil($total_rows / $limit), 1);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">

    <link rel="stylesheet" href="./research_coor.css">
    <link href="/Root_2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/Root_2/dist/js/bootstrap.bundle.min.js"></script>
    <title>ADMIN</title>
</head>
<body>
    <div class="container-fluid main p-0">
        <!--Nav Section START-->
        <section class="navBarSection">
            <div class="row p-1">
                <!-- Hello -->
                <div class="col-4 order-1 col-md-2 order-md-1 hello p-0 ps-md-2 d-flex align-items-center">
                    <span class="txt-hello">Hello,</span>
                    <span class="txt-username"><?php echo $user_name; ?>!</span>
                </div>

                <!-- Logout (Comes second on mobile, last on desktop) -->
                <div class="col-4 offset-4 order-2 col-md-2 order-md-3 offset-md-0 d-flex align-items-center justify-content-end">
                    <form action="/Root_2/logout.php" method="post">
                        <button type="submit" class="btn-logout">Logout</button>
                    </form>
                </div>

                <!-- Search (New row on mobile, in between on desktop) -->
                <div class="col-6 order-3 col-md-2 order-md-2 d-flex align-items-center justify-content-center mx-auto search">
                    <input type="text" class="input-search fa-search" id="searchInput" placeholder="Search..." onkeyup="filterTable()">

                </div>
            </div>
        </section>
        <!--Nav Section END-->

        <!--TABLE START-->
        <section class="tableSection">
            <div class="table-responsive">
                <table class="table table-striped custom-table" id="researchTable">
                    <thead>
                        <tr>
                            <th class="wrap">Date of Submission</th>
                            <th>Username</th>
                            <th>Title</th>
                            <th>Main Author</th>
                            <th>Co-Author 1</th>
                            <th>Co-Author 2</th>
                            <th>More Authors</th>
                            <th class="wrap">Uploaded Research Paper</th>
                            <th class="wrap">Uploaded Abstract</th>
                            <th>Notifications</th>
                            <th class="wrap">Schedule for Proposal Presentation</th>
                            <th class="wrap">Schedule for Final Presentation</th>
                            <th>Research Status</th>
                            <th>Edit Access</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="wrap"><?= htmlspecialchars($row['formatted_date_of_submission']) ?></td>
                                <td class="wrap"><?= htmlspecialchars($row['username']) ?></td>
                                <td class="wrap"><?= htmlspecialchars($row['title']) ?></td>
                                <td class="wrap"><?= htmlspecialchars($row['main_author']) ?></td>
                                <td class="wrap"><?= htmlspecialchars($row['co_author_1']) ?></td>
                                <td class="wrap"><?= htmlspecialchars($row['co_author_2']) ?></td>
                                <td class="wrap"><?= htmlspecialchars($row['others']) ?></td>
                                <td>
                                    <a href='review_coor.php?id=<?= $row['id'] ?>&type=research' target='_blank'>View File</a>
                                </td>
                                <td>
                                    <a href='review_coor.php?id=<?= $row['id'] ?>&type=abstract' target='_blank'>View File</a>
                                </td>
                                <form action="update_coor.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <td>
                                        <select name="notification" class="custom-select">
                                            <option class="custom-option" value="">Select</option>
                                            <option class="custom-option" value="For Revision" <?= ($row['notification'] == 'For Revision') ? 'selected' : '' ?>>For Revision</option>
                                            <option class="custom-option" value="Scheduled for Research Proposal Presentation" <?= ($row['notification'] == 'Scheduled for Research Proposal Presentation') ? 'selected' : '' ?>>Scheduled for Research Proposal Presentation</option>
                                            <option class="custom-option" value="Scheduled for Final Research Presentation" <?= ($row['notification'] == 'Scheduled for Final Research Presentation') ? 'selected' : '' ?>>Scheduled for Final Research Presentation</option>
                                            <option class="custom-option" value="Please See Comments" <?= ($row['notification'] == 'Please See Comments') ? 'selected' : '' ?>>Please See Comments</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="date" class="custom-date" name="sched_proposal" value="<?= isset($row['sched_proposal']) ? htmlspecialchars($row['sched_proposal']) : '' ?>">
                                    </td>
                                    <td>
                                        <input type="date" class="custom-date" name="sched_final" value="<?= isset($row['sched_final']) ? htmlspecialchars($row['sched_final']) : '' ?>">
                                    </td>
                                    <td>
                                        <select name="research_status" class="custom-select">
                                            <option class="custom-option" value="">Select</option>
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
                    <?php else: ?>
                        <tr>
                            <td colspan="13">No submissions found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <!--TABLE END-->

        <!-- Pagination Links -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <a class="page-link">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
    </div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("searchInput").addEventListener("keyup", filterTable);
    });

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

    for (let i = 1; i < rows.length; i++) { // Skip header row
            let cells = rows[i].getElementsByTagName("td");
            let rowText = "";

            for (let j = 0; j < cells.length; j++) {
                // Get visible text content
                rowText += cells[j].textContent.toLowerCase().trim() + " ";

                // Get selected values from dropdowns
                let selects = cells[j].getElementsByTagName("select");
                for (let select of selects) {
                    rowText += " " + (select.options[select.selectedIndex]?.text.toLowerCase() || "");
                }

                // Get values from date inputs
                let inputs = cells[j].getElementsByTagName("input");
                for (let input of inputs) {
                    if (input.type === "date" && input.value) {
                        rowText += " " + input.value.toLowerCase();
                    }
                }
            }

            // Show row if input matches any text in row, hide otherwise
            rows[i].style.display = rowText.includes(input) ? "" : "none";
        }
    }

</script>
</body>
</html>