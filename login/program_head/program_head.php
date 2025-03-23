<?php
    session_start(); 
    include '../../conn.php'; 

    // Check if user is logged in
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'prog_head') {
        header("Location: index.php");
        exit();
    }

    $user_name = htmlspecialchars($_SESSION['username']);
    $first_name = htmlspecialchars($_SESSION['first_name']);
    $progHead_department = $_SESSION['department']; 
    $progHead_program = $_SESSION['program'];

    // Pagination
    $limit = 5;
    $page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch user data with pagination
    $sql = "SELECT id, 
                DATE_FORMAT(date_of_submission, '%b %d, %Y %h:%i %p') AS formatted_date_of_submission, 
                username, title, main_author, co_author_1, co_author_2, others, 
                file_research_paper, file_abstract, notification, 
                DATE_FORMAT(sched_proposal, '%b %d, %Y') AS formatted_sched_proposal, 
                DATE_FORMAT(sched_final, '%b %d, %Y') AS formatted_sched_final, 
                research_status, edit_access
            FROM tbl_fileUpload 
            WHERE department = ? AND program = ?
            ORDER BY date_of_submission DESC 
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $progHead_department, $progHead_program, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    // Total records for pagination
    $total_query = $conn->prepare("SELECT COUNT(*) FROM tbl_fileUpload WHERE department = ? AND program = ?");
    $total_query->bind_param("ss", $progHead_department, $progHead_program);
    $total_query->execute();
    $total_query->bind_result($total_rows);
    $total_query->fetch();
    $total_query->close();
    
    $total_pages = max(ceil($total_rows / $limit), 1);
    $conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="program_head.css" rel="stylesheet">
    <link href="/Root_2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/Root_2/dist/js/bootstrap.bundle.min.js"></script>

    <title><?php echo $progHead_program; ?></title>
</head>
<body>
    <div class="container-fluid main p-0">
        <!--Nav Section START-->
        <section class="navBarSection">
            <div class="row p-1">
                <div class="col-6 col-md-6 p-0 ps-md-2 d-flex align-items-center hello">
                    <span class="txt-hello">Hello,</span>
                    <span class="txt-username"><?php echo $first_name; ?>!</span>
                </div>

                <div class="col-6 col-md-6  d-flex align-items-center justify-content-end">
                    <form action="/Root_2/logout.php" method="post">
                        <button type="submit" class="btn-logout">Logout</button>
                    </form>
                </div>
            </div>
        </section>
        <!--Nav Section END-->

         <!--filter START-->
         <section class="filterSection mt-3">
            <div class="row px-3">
                <!-- Search -->
                <div class="col-12 col-md-2 order-5 offset-md-4 mt-1 p-0 d-flex align-items-center justify-content-md-end align-self-end mb-1 search">
                    <i class="fa fa-search p-1"></i>
                    <input type="text" class="input-search fa-search" id="searchInput" placeholder="Search..." onkeyup="filterTable()">
                </div>

                <!-- Filter Title -->
                <div class="col-12 p-1 mt-2 order-1">
                    <span class="txt-filter">Select filter based on:</span>
                </div>

                <!-- Filter Group -->
                <div class="col-6 col-md-2 mt-1 p-1 order-2">
                    <span class="txt-researchProgress">Research Progress</span>
                    <select class="form-select custom-select" id="notificationFilter" onchange="filterTable()">
                        <option value="">Select Research Progress:</option>
                        <option value="For Revision">For Revision</option>
                        <option value="Scheduled for Research Proposal Presentation">Proposal Presentation</option>
                        <option value="Scheduled for Final Presentation">Final Presentation</option>
                        <option value="Please see comments">Please see comments</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 mt-1 p-1 order-3">
                    <span class="txt-researchStatus">Research Status</span>
                    <select class="form-select custom-select" id="statusFilter" onchange="filterTable()">
                        <option value="">Select Research Status:</option>
                        <option value="Presented">Presented</option>
                        <option value="Implemented">Implemented</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 mt-1 p-1 order-4">
                    <span class="txt-year">Year</span>
                    <select class="form-select custom-select" id="yearFilter" onchange="filterTable()">
                        <option value="">Select Year:</option>
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                    </select>
                </div>
            </div>
        </section>
        <!--filter END-->

        <!-- Table START -->
        <section class="tableSection">
            <div class="table-responsive">
                    <table class="table table-striped custom-table"  id="researchTable">
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
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Fetch and display results as table rows
                            if ($result->num_rows > 0) { 
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td class='wrap'>" . htmlspecialchars($row['formatted_date_of_submission']) . "</td>";
                                    echo "<td class='wrap'>" . htmlspecialchars($row['username']) . "</td>";
                                    echo "<td class='wrap'>" . htmlspecialchars($row['title']) . "</td>";
                                    echo "<td class='wrap'>" . htmlspecialchars($row['main_author']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['co_author_1']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['co_author_2']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['others']) . "</td>";
                                    echo "<td><a href='review_user.php?id=" . htmlspecialchars($row['id']) . "&type=research' target='_blank'>View PDF</a></td>";
                                    echo "<td><a href='review_user.php?id=" . htmlspecialchars($row['id']) . "&type=abstract' target='_blank'>View PDF</a></td>";
                                    echo "<td class='wrap'>" . htmlspecialchars($row['notification']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['formatted_sched_proposal']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['formatted_sched_final']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['research_status']) . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='13'>No submissions found.</td></tr>";
                            }

                            // Close statement properly
                            $stmt->close();
                        ?>
                        </tbody>
                    </table>
                </div>
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
        </section>
        <!-- Table END -->
    </div>
<script>
    // Filter Function
    function filterTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const notificationFilter = document.getElementById('notificationFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        const yearFilter = document.getElementById('yearFilter').value;
        const table = document.getElementById('researchTable');
        const trs = table.getElementsByTagName('tr');

        for (let i = 1; i < trs.length; i++) { // Start at 1 to skip table header
            const tds = trs[i].getElementsByTagName('td');
            let textContent = trs[i].textContent.toLowerCase();

            // Extract year from the "Date of Submission" column
            let dateSubmission = tds[0].textContent.trim(); // The first column is "Date of Submission"
            let rowYear = dateSubmission.split(', ')[1]?.slice(0, 4); // Extract year from formatted date

            // Apply search input filter (searches across the row)
            let searchMatch = textContent.includes(searchInput);

            // Apply dropdown filters (exact match)
            let notificationMatch = !notificationFilter || tds[9].textContent.trim() === notificationFilter;
            let statusMatch = !statusFilter || tds[12].textContent.trim() === statusFilter;
            let yearMatch = !yearFilter || rowYear === yearFilter; // Check if extracted year matches filter

            if (searchMatch && notificationMatch && statusMatch && yearMatch) {
                trs[i].style.display = '';
            } else {
                trs[i].style.display = 'none';
            }
        }
    }
</script>
</body>
</html>