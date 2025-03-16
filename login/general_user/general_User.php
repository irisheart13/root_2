<?php
    session_start(); 
    include '../../conn.php'; 

    // Check if user is logged in
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
        header("Location: index.php");
        exit();
    }

    $user_name = htmlspecialchars($_SESSION['username']);
    $program = htmlspecialchars($_SESSION['program']);

    // Pagination
    $limit = 5;
    $page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch user data with pagination
    $sql = "SELECT id, DATE_FORMAT(date_of_submission, '%b %d, %Y %h:%i %p') AS formatted_date_of_submission, title, main_author, co_author_1, co_author_2, others, file_research_paper, file_abstract, notification, 
            DATE_FORMAT(sched_proposal, '%b %d, %Y') AS formatted_sched_proposal, 
            DATE_FORMAT(sched_final, '%b %d, %Y') AS formatted_sched_final, 
            research_status, edit_access
            FROM tbl_fileUpload 
            WHERE username = ? 
            ORDER BY date_of_submission DESC 
            LIMIT $limit OFFSET $offset";

    $tbl_fileUpload_query = $conn->prepare($sql);
    $tbl_fileUpload_query->bind_param("s", $user_name);
    $tbl_fileUpload_query->execute();
    $tbl_fileUpload_query->store_result();
    $tbl_fileUpload_query->bind_result($id, $date_of_submission, $title, $main_author, $co_author_1, $co_author_2, $others, $file_research_paper, $file_abstract, $notification, $sched_proposal, $sched_final, $research_status, $edit_access);

    // Total records for pagination
    $total_query = $conn->prepare("SELECT COUNT(*) FROM tbl_fileUpload WHERE username = ?");
    $total_query->bind_param("s", $user_name);
    $total_query->execute();
    $total_query->store_result();
    $total_query->bind_result($total_rows);
    $total_query->fetch();
    $total_query->close();

    $total_pages = ($total_rows > 0) ? ceil($total_rows / $limit) : 1;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="general_User.css" rel="stylesheet">
    <link href="/Root_2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="/Root_2/dist/js/bootstrap.bundle.min.js"></script>

    <title><?php echo $program; ?></title>
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

        <!--Form Section START-->
        <section class="formSection">
            <form method="POST" enctype="multipart/form-data" action="submitFile_user.php">
                <div class="row">
                    <!--Research title START-->
                    <div class="col-12 col-md-9 mx-md-auto">
                        <div class="row">
                            <div class="col-4 col-md-3 d-flex align-items-center justify-content-center p-0 custom-label">
                                <span class="wrap">Research Title</span>
                            </div>
                            <div class="col-8 col-md-9 d-flex align-items-center p-0 custom-input">
                                <input type="text" name="title" id="title" placeholder="TITLE" class="form-control  rounded-0 custom-inputBox" value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>" required>
                            </div>
                        </div> 
                    </div>
                    <!--Research title END-->

                    <!--Main Author START-->
                    <div class="col-12 col-md-9 mx-md-auto">
                        <div class="row">
                            <div class="col-4 col-md-3 d-flex align-items-center justify-content-center p-0 custom-label">
                                <span class="wrap">Main Author</span>
                            </div>
                            <div class="col-8 col-md-9 d-flex align-items-center p-0 custom-input">
                                <input type="text" name="main_author" id="main_author" placeholder="Last Name , First Name M.I" class="form-control  rounded-0 custom-inputBox" value="<?php echo isset($main_author) ? htmlspecialchars($main_author) : ''; ?>" required>
                            </div>
                        </div> 
                    </div>
                    <!--Main Author END-->

                    <!--Co-Author 1 START-->
                    <div class="col-12 col-md-9 mx-md-auto">
                        <div class="row">
                            <div class="col-4 col-md-3 d-flex align-items-center justify-content-center p-0 custom-label">
                                <span class="wrap">Co-author</span>
                            </div>
                            <div class="col-8 col-md-9 d-flex align-items-center p-0 custom-input">
                                <input type="text" name="co_author_1" id="co_author_1" placeholder="Last Name , First Name M.I" class="form-control  rounded-0 custom-inputBox" value="<?php echo isset($co_author_1) ? htmlspecialchars($co_author_1) : ''; ?>" >
                            </div>
                        </div> 
                    </div>
                    <!--Co-Author 1 END-->

                    <!--Co-Author 2 START-->
                    <div class="col-12 col-md-9 mx-md-auto">
                        <div class="row">
                            <div class="col-4 col-md-3 d-flex align-items-center justify-content-center p-0 custom-label">
                                <span class="wrap">Co-author</span>
                            </div>
                            <div class="col-8 col-md-9 d-flex align-items-center p-0 custom-input">
                                <input type="text" name="co_author_2" id="co_author_2" placeholder="Last Name , First Name M.I" class="form-control  rounded-0 custom-inputBox" value="<?php echo isset($co_author_2) ? htmlspecialchars($co_author_2) : ''; ?>" >
                            </div>
                        </div> 
                    </div>
                    <!--Co-Author 2 END-->

                    <!--Others START-->
                    <div class="col-12 col-md-9 mx-md-auto">
                        <div class="row">
                            <div class="col-4 col-md-3 d-flex align-items-center justify-content-center p-0 custom-label">
                                <span class="wrap">More Author</span>
                            </div>
                            <div class="col-8 col-md-9 d-flex align-items-center p-0 custom-input">
                                <textarea type="text" name="others" id="others" placeholder="Add here..." class="form-control  rounded-0 custom-textBox" value="<?php echo isset($others) ? htmlspecialchars($others) : ''; ?>" ></textarea>
                            </div>
                        </div> 
                    </div>
                    <!--Others 2 END-->

                    <!--Completed Research Paper START-->
                    <div class="col-12 col-md-9 mx-md-auto">
                        <div class="row">
                            <div class="col-12 col-md-6 d-flex align-items-center justify-content-center p-0 custom-label">
                                <span class="wrap">Attach completed research paper</span>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-center p-0 custom-input">
                                <input type="file" name="file_research_paper" id="file_research_paper" class="form-control custom-fileUpload" <?php echo isset($_POST['submission_id']) ? '' : 'required'; ?> required>
                            </div>
                        </div> 
                    </div>
                    <!--Completed Research Paper END-->

                    <!--Abstract START-->
                    <div class="col-12 col-md-9 mx-md-auto">
                        <div class="row">
                            <div class="col-12 col-md-6 d-flex align-items-center justify-content-center p-0 custom-label">
                                <span class="wrap">Attach abstract</span>
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-center p-0 custom-input">
                                <input type="file" name="file_abstract" id="file_abstract" class="form-control custom-input" <?php echo isset($_POST['submission_id']) ? '' : 'required'; ?> required>
                            </div>
                        </div> 
                    </div>
                    <!--Abstract END-->

                    <!--Submit START-->
                    <div class="col-12 col-md-9 mx-md-auto">
                        <div class="row">
                            <div class="col-4 mx-auto col-md-2 mx-md-auto p-0 d-flex align-items-center justify-content-center">
                                <button type="submit" name="submit" class="btn-submit">SUBMIT</button>
                             </div>
                        </div> 
                    </div>
                    <!--Submit END-->
                </div>
            </form>
        </section>
        <!--Form Section END-->

        <section class="tableSection">
            <div class="table-responsive">
                    <table class="table table-striped custom-table"  id="researchTable">
                        <thead>
                            <tr>
                                <th class="wrap">Date of Submission</th>
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
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            // Fetch and display results as table rows
                            if ($tbl_fileUpload_query->num_rows > 0) {
                                $tbl_fileUpload_query->bind_result($id, $date_of_submission, $title, $main_author, $co_author_1, $co_author_2, $others, $file_research_paper, $file_abstract, $notification, $sched_proposal, $sched_final, $research_status, $edit_access);
                                
                                while ($tbl_fileUpload_query->fetch()) {
                                    echo "<tr>";
                                    echo "<td class='wrap'>" . htmlspecialchars($date_of_submission) . "</td>";
                                    echo "<td class='wrap'>" . htmlspecialchars($title) . "</td>";
                                    echo "<td class='wrap'>" . htmlspecialchars($main_author) . "</td>";
                                    echo "<td>" . htmlspecialchars($co_author_1) . "</td>";
                                    echo "<td>" . htmlspecialchars($co_author_2) . "</td>";
                                    echo "<td>" . htmlspecialchars($others) . "</td>";
                                    echo "<td><a href='review_user.php?id=" . htmlspecialchars($id) . "&type=research' target='_blank'>View PDF</a></td>";
                                    echo "<td><a href='review_user.php?id=" . htmlspecialchars($id) . "&type=abstract' target='_blank'>View PDF</a></td>";
                                    echo "<td class='wrap'>" . htmlspecialchars($notification) . "</td>";
                                    echo "<td>" . htmlspecialchars($sched_proposal) . "</td>";
                                    echo "<td>" . htmlspecialchars($sched_final) . "</td>";
                                    echo "<td>" . htmlspecialchars($research_status) . "</td>";

                                    echo "<td>
                                            <button class='btn btn-warning btn-edit'
                                                data-id='" . htmlspecialchars($id) . "'
                                                data-title='" . htmlspecialchars($title) . "'
                                                data-main-author='" . htmlspecialchars($main_author) . "'
                                                data-co-author-1='" . htmlspecialchars($co_author_1) . "'
                                                data-co-author-2='" . htmlspecialchars($co_author_2) . "'
                                                data-others='" . htmlspecialchars($others) . "'
                                                data-research-paper='" . htmlspecialchars($file_research_paper) . "'
                                                data-abstract='" . htmlspecialchars($file_abstract) . "' " 
                                                . ($edit_access ? "" : "disabled") . ">
                                                Edit
                                            </button>
                                        </td>";

                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='13'>No submissions found.</td></tr>";
                            }

                            $tbl_fileUpload_query->close();
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
            
            <!-- Edit Modal START -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Submission</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editForm" enctype="multipart/form-data">
                                <input type="hidden" id="edit_id">
                                
                                <!-- Title -->
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">Title</label>
                                    <input type="text" name="title" class="form-control" id="edit_title">
                                </div>

                                <!-- Authors -->
                                <div class="mb-3">
                                    <label for="edit_main_author" class="form-label">Main Author</label>
                                    <input type="text" name="main_author" class="form-control" id="edit_main_author">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_co_author_1" class="form-label">Co-Author 1</label>
                                    <input type="text" name="co_author_1" class="form-control" id="edit_co_author_1">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_co_author_2" class="form-label">Co-Author 2</label>
                                    <input type="text" name="co_author_2" class="form-control" id="edit_co_author_2">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_others" class="form-label">More Authors</label>
                                    <input type="text" name="others" class="form-control" id="edit_others">
                                </div>

                                <!-- File Uploads -->
                                <div class="mb-3">
                                    <label for="edit_research_paper" class="form-label">Upload New Research Paper</label>
                                    <input type="file" name="new_research_paper" class="form-control" id="edit_research_paper">
                                </div>
                                <div class="mb-3">
                                    <label for="edit_abstract" class="form-label">Upload New Abstract</label>
                                    <input type="file" name="new_abstract" class="form-control" id="edit_abstract">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="saveChanges">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        <!-- Edit Modal END -->
        </section>
    </div>
<script>
    //Script for edit modal
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".btn-edit").forEach(button => {
            button.addEventListener("click", function () {
                // Populate input fields, ensuring empty values remain blank
                document.getElementById("edit_id").value = this.dataset.id || "";
                document.getElementById("edit_title").value = this.dataset.title || "";
                document.getElementById("edit_main_author").value = this.dataset.mainAuthor || "";
                document.getElementById("edit_co_author_1").value = this.dataset['co-author-1'] || "";
                document.getElementById("edit_co_author_2").value = this.dataset['co-author-2'] || "";
                document.getElementById("edit_others").value = this.dataset.others || "";

                // Show the modal
                let editModal = new bootstrap.Modal(document.getElementById("editModal"));
                editModal.show();
            });
        });

        document.getElementById("saveChanges").addEventListener("click", function () {
            let form = document.getElementById("editForm");
            let formData = new FormData(form);
            formData.append("id", document.getElementById("edit_id").value);

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "update_user.php", true);
            xhr.onload = function () {
                if (xhr.status == 200) {
                    alert(xhr.responseText);
                    location.reload();
                } else {
                    alert("Error updating submission.");
                }
            };
            xhr.send(formData);
        });
    });

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