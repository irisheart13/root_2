<?php
session_start();
include '../../conn.php';

// Check if user is logged in as dean
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dean') {
    header("Location: index.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['username']);
$first_name = htmlspecialchars($_SESSION['first_name']);
$user_department = $_SESSION['department'];

// Department to program mapping
$departmentPrograms = [
    "CITCS" => ["BSCS", "BSIT", "ACT"],
    "CBA" => ["BSBA-HR", "BSBA-MM", "BSBA-OM"],
    "CTE" => ["BEED-ECED", "BEED-SNED", "BEED-GENED", "BSED-ENG", "BSED-FIL", "BSED-PE", "BSED-SCI", "BSED-MATH"],
    "CAS" => ["BSPSY", "BACOMM"],
    "COA" => ["BSA"],
    "CCJ" => ["BSCRIM"],
    "IPPG" => ["BAPOL", "BPA"]
];

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

if (!isset($departmentPrograms[$user_department])) {
    die("Unauthorized access");
}

$allowedPrograms = $departmentPrograms[$user_department];
$placeholders = implode(',', array_fill(0, count($allowedPrograms), '?'));

// Fetch data query
$sql = "SELECT id, 
            DATE_FORMAT(date_of_submission, '%b %d, %Y %h:%i %p') AS formatted_date_of_submission, username,
            program, title, main_author, co_author_1, co_author_2, others, 
            file_research_paper, file_abstract, notification, 
            DATE_FORMAT(sched_proposal, '%b %d, %Y') AS formatted_sched_proposal, 
            DATE_FORMAT(sched_final, '%b %d, %Y') AS formatted_sched_final, 
            research_status
        FROM tbl_fileUpload
        WHERE department = ? AND program IN ($placeholders)
        ORDER BY date_of_submission DESC
        LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
if (!$stmt) die("SQL Error: " . $conn->error);

$types = "s" . str_repeat("s", count($allowedPrograms));
$params = array_merge([$user_department], $allowedPrograms);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total records
$total_sql = "SELECT COUNT(*) FROM tbl_fileUpload WHERE department = ? AND program IN ($placeholders)";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param("s" . str_repeat("s", count($allowedPrograms)), $user_department, ...$allowedPrograms);
$total_stmt->execute();
$total_stmt->bind_result($total_rows);
$total_stmt->fetch();
$total_stmt->close();

$total_pages = max(ceil($total_rows / $limit), 1);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="dean.css" rel="stylesheet">
    <link href="/Root_2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/Root_2/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="/Root_2/dist/fontawesome-6.7.2/css/all.min.css">

    <title><?php echo $user_department; ?> </title>
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
                <div class="col-12 col-md-2 order-6 offset-md-2 mt-1 p-0 d-flex align-items-center justify-content-md-end align-self-end mb-1 search">
                    <i class="fa fa-search p-1"></i>
                    <input type="text" class="input-search fa-search" id="searchInput" placeholder="Search..." onkeyup="filterTable()">
                </div>

                <!-- Filter Title -->
                <div class="col-12 p-1 mt-2 order-1">
                    <span class="txt-filter">Select filter based on:</span>
                </div>

                <!-- Filter Group -->
                <div class="col-6 col-md-2 mt-1 p-1 order-2">
                    <span class="txt-program">Program</span>
                    <select class="form-select custom-select" id="programFilter" onchange="filterTable()">
                        <option value="">Select Program:</option>
                        <?php foreach ($allowedPrograms as $program): ?>
                            <option value="<?= htmlspecialchars($program) ?>"><?= htmlspecialchars($program) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-2 mt-1 p-1 order-3">
                    <span class="txt-researchProgress">Research Progress</span>
                    <select class="form-select custom-select" id="notificationFilter" onchange="filterTable()">
                        <option value="">Select Research Progress:</option>
                        <option value="For Revision">For Revision</option>
                        <option value="Scheduled for Research Proposal Presentation">Proposal Presentation</option>
                        <option value="Scheduled for Final Presentation">Final Presentation</option>
                        <option value="Please see comments">Please see comments</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 mt-1 p-1 order-4">
                    <span class="txt-researchStatus">Research Status</span>
                    <select class="form-select custom-select" id="statusFilter" onchange="filterTable()">
                        <option value="">Select Research Status:</option>
                        <option value="Presented">Presented</option>
                        <option value="Implemented">Implemented</option>
                    </select>
                </div>

                <div class="col-6 col-md-2 mt-1 p-1 order-5">
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


        <!--Table START-->
        <section class="tableSection">
            <div class="table-responsive">
                    <table class="table table-striped custom-table"  id="researchTable">
                        <thead>
                            <tr>
                                <th class="wrap">Date of Submission</th>
                                <th>Username</th>
                                <th>Program</th>
                                <th>Title</th>
                                <th class="wrap">Main Author</th>
                                <th class="wrap">Co-Author 1</th>
                                <th class="wrap">Co-Author 2</th>
                                <th class="wrap">More Authors</th>
                                <th class="wrap">Uploaded Research Paper</th>
                                <th class="wrap">Uploaded Abstract</th>
                                <th class="wrap">Notifications</th>
                                <th class="wrap">Schedule for Proposal Presentation</th>
                                <th class="wrap">Schedule for Final Presentation</th>
                                <th class="wrap">Research Status</th>
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
                                    echo "<td class='wrap'>" . htmlspecialchars($row['program']) . "</td>";
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
        <!--Table END-->
    </div>
<script>
    // Filter Function
    function filterTable() {
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const programFilter = document.getElementById('programFilter').value.toLowerCase();
        const yearFilter = document.getElementById('yearFilter').value;
        const notificationFilter = document.getElementById('notificationFilter').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const table = document.getElementById('researchTable');
        const trs = table.getElementsByTagName('tr');

        for (let i = 1; i < trs.length; i++) {
            const tds = trs[i].getElementsByTagName('td');
            let textContent = trs[i].textContent.toLowerCase();

            let dateText = tds[0].textContent.trim();
            let extractedYear = dateText.match(/\b(20\d{2})\b/);
            extractedYear = extractedYear ? extractedYear[1] : "";

            let searchMatch = textContent.includes(searchInput);
            let programMatch = !programFilter || tds[2].textContent.trim().toLowerCase() === programFilter;
            let yearMatch = !yearFilter || extractedYear === yearFilter;
            let notificationMatch = !notificationFilter || tds[10].textContent.trim().toLowerCase() === notificationFilter;
            let statusMatch = !statusFilter || tds[13].textContent.trim().toLowerCase() === statusFilter;

            if (searchMatch && programMatch && yearMatch && notificationMatch && statusMatch) {
                trs[i].style.display = '';
            } else {
                trs[i].style.display = 'none';
            }
        }
    }



</script>

</body>
</html>