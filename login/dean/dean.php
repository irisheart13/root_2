<?php
session_start(); 
include '../../conn.php'; 

// Check if user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dean') {
    header("Location: index.php");
    exit();
}

$user_name = htmlspecialchars($_SESSION['username']);
$user_department = $_SESSION['department']; 

// Define department-program mapping
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

if (isset($departmentPrograms[$user_department])) {
    $allowedPrograms = $departmentPrograms[$user_department];

    // Create placeholders for 'IN' clause (e.g., ?, ?, ?)
    $placeholders = implode(',', array_fill(0, count($allowedPrograms), '?'));

    // Prepare SQL query
    $sql = "SELECT id, 
                DATE_FORMAT(date_of_submission, '%b %d, %Y %h:%i %p') AS formatted_date_of_submission, 
                program, title, main_author, co_author_1, co_author_2, others, file_research_paper, file_abstract, notification, 
                DATE_FORMAT(sched_proposal, '%b %d, %Y') AS formatted_sched_proposal, 
                DATE_FORMAT(sched_final, '%b %d, %Y') AS formatted_sched_final, 
                research_status, edit_access
            FROM tbl_fileUpload 
            WHERE department = ? AND program IN ($placeholders)
            ORDER BY date_of_submission DESC 
            LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // Build parameter type string dynamically
        $types = "s" . str_repeat("s", count($allowedPrograms)) . "ii";  // department (s) + programs (s * n) + limit (i) + offset (i)
        
        // Build parameters array
        $params = array_merge([$user_department], $allowedPrograms, [$limit, $offset]);

        // Bind parameters dynamically
        $stmt->bind_param($types, ...$params);

        // Execute the statement
        $stmt->execute();
        $result = $stmt->get_result();

    } else {
        die("SQL Error: " . $conn->error);
    }

    // Fetch total records for pagination
    $total_sql = "SELECT COUNT(*) FROM tbl_fileUpload WHERE department = ? AND program IN ($placeholders)";
    $total_stmt = $conn->prepare($total_sql);

    if ($total_stmt) {
        // Bind dynamically for total count query
        $total_stmt->bind_param($types, ...$params);
        $total_stmt->execute();
        $total_stmt->bind_result($total_rows);
        $total_stmt->fetch();
        $total_stmt->close();
    } else {
        $total_rows = 0;
    }

    $total_pages = max(ceil($total_rows / $limit), 1);
} else {
    die("Unauthorized access");
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="program_head.css" rel="stylesheet">
    <link href="/Root_2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/Root_2/dist/js/bootstrap.bundle.min.js"></script>

    <title><?php echo $user_department; ?> </title>
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

        <section class="tableSection">
            <div class="table-responsive">
                    <table class="table table-striped custom-table"  id="researchTable">
                        <thead>
                            <tr>
                                <th class="wrap">Date of Submission</th>
                                <th>Program</th>
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
            <!--Uploaded File Dashboard END-->
        </section>
    </div>
<script>
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