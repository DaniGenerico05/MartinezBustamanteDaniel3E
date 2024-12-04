<?php
session_start();
include "../includes/config/conn.php";
$db = connect();

// Get the current employee's full name
$employeeQuery = "SELECT CONCAT(e.firstName, ' ', e.lastName) as fullName 
                    FROM employee e 
                    WHERE e.num = ?";
$stmt = mysqli_prepare($db, $employeeQuery);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['num']);
mysqli_stmt_execute($stmt);
$employeeResult = mysqli_stmt_get_result($stmt);
$employeeData = mysqli_fetch_assoc($employeeResult);
$employeeFullName = $employeeData['fullName'];

// Process complaint form if submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_complaint'])) {
    $providerNum = intval($_POST['providerNum']);
    $description = $_POST['complaintDescription'];
    
    // Validate description
    if (strlen($description) < 15) {
        $error = "Complaint description must be at least 15 characters long.";
    } elseif (ctype_digit($description) || ctype_punct($description)) {
        $error = "Complaint description must contain a mix of letters, numbers, and/or symbols.";
    } elseif (!preg_match('/[a-zA-Z]/', $description)) {
        $error = "Complaint description must contain at least one letter.";
    } else {
        $insertQuery = "INSERT INTO trouble (description, provider) VALUES (?, ?)";
        $insertStmt = mysqli_prepare($db, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "si", $description, $providerNum);
        
        if (mysqli_stmt_execute($insertStmt)) {
            $message = "Complaint submitted successfully.";
        } else {
            $error = "Error submitting complaint: " . mysqli_error($db);
        }
        mysqli_stmt_close($insertStmt);
    }
}

// Fetch complaints for a specific provider
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['fetch_complaints'])) {
    $providerNum = intval($_GET['provider_num']);
    $complaintsQuery = "SELECT troubleNum, troubleDate, description FROM trouble WHERE provider = ? ORDER BY troubleDate DESC";
    $complaintsStmt = mysqli_prepare($db, $complaintsQuery);
    mysqli_stmt_bind_param($complaintsStmt, "i", $providerNum);
    mysqli_stmt_execute($complaintsStmt);
    $complaintsResult = mysqli_stmt_get_result($complaintsStmt);
    
    $complaints = [];
    while ($row = mysqli_fetch_assoc($complaintsResult)) {
        $complaints[] = $row;
    }
    
    echo json_encode($complaints);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider List and Complaints</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
    min-height: 100vh;
    background-image: url('https://4kwallpapers.com/images/wallpapers/macos-monterey-stock-black-dark-mode-layers-5k-4480x2520-5889.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    display: flex;
    align-items: center;
    padding: 2rem;
}
.content-wrapper {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 15px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin: 0 auto;
    max-width: 1400px;
    width: 100%;
}
.return-btn {
    display: inline-block;
    background: #000;
    color: #fff;
    padding: 0.5rem 1.5rem;
    border-radius: 8px;
    text-decoration: none;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}
.return-btn:hover {
    background: #333;
    color: #fff;
    transform: translateX(-5px);
}
.table-container {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}
.table thead th {
    background: #000;
    color: #fff;
    font-weight: 500;
    border: none;
}
.table tbody tr:nth-child(even) {
    background-color: rgba(0, 0, 0, 0.02);
}
.action-buttons {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}
.complaint-btn, .view-complaints-btn {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
    min-width: 120px;
}
.complaint-btn:hover, .view-complaints-btn:hover {
    background-color: #5a6268;
    border-color: #545b62;
    color: white;
}

/* Dark theme for view complaints modal */
.modal-content {
    background-color: #1E1E1E;
    color: #FFFFFF;
}
.modal-header {
    border-bottom: 1px solid #404040;
}
.modal-footer {
    border-top: 1px solid #404040;
}
.complaint-card {
    background: #2D2D2D;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    margin-bottom: 1rem;
    padding: 1.5rem;
    border: 1px solid #404040;
}
.complaint-card h6 {
    color: #A0A0A0;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
}
.complaint-card .complaint-title {
    color: #FFFFFF;
    font-size: 1.1rem;
    margin-bottom: 1rem;
}
.complaint-pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 1.5rem;
}
.complaint-pagination button {
    padding: 0.5rem 1rem;
    border: 1px solid #404040;
    background: #2D2D2D;
    color: #FFFFFF;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.complaint-pagination button:hover {
    background: #808080;
}
.complaint-pagination button.active {
    background: #121212;
    color: #fff;
    border-color: #808080;
}
.btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}
    </style>
</head>
<body>
<div class="content-wrapper">
        <a href="../index.php" class="return-btn">
            <i class="fas fa-arrow-left me-2"></i>Return
        </a>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="table-container">
            <table id="providerTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Fiscal Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Materials</th>
                        <th style="width: 250px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = mysqli_query($db, "SELECT * FROM vw_provider_assoc");
                    while ($result = mysqli_fetch_array($query)){ ?>
                        <tr>
                            <td><?= htmlspecialchars($result['fiscalName']) ?></td>
                            <td><?= htmlspecialchars($result['email']) ?></td>
                            <td><?= htmlspecialchars($result['numTel']) ?></td>
                            <td><?= htmlspecialchars($result['materials']) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn complaint-btn btn-sm" onclick="openComplaintModal(<?= $result['num'] ?>, '<?= htmlspecialchars($result['fiscalName']) ?>')">New Complaint</button>
                                    <button class="btn view-complaints-btn btn-sm" onclick="openViewComplaintsModal(<?= $result['num'] ?>, '<?= htmlspecialchars($result['fiscalName']) ?>')">View Complaints</button>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- New Complaint Modal -->
    <div class="modal fade" id="complaintModal" tabindex="-1" aria-labelledby="complaintModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="complaintModalLabel">Submit Complaint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" id="complaintForm" onsubmit="return validateComplaint()">
                    <div class="modal-body">
                        <input type="hidden" id="providerNum" name="providerNum">
                        <div class="mb-3">
                            <label for="providerName" class="form-label">Provider:</label>
                            <input type="text" class="form-control" id="providerName" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="employeeName" class="form-label">Employee:</label>
                            <input type="text" class="form-control" id="employeeName" value="<?= htmlspecialchars($employeeFullName) ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="complaintDescription" class="form-label">Complaint Description:</label>
                            <textarea class="form-control" id="complaintDescription" name="complaintDescription" rows="3" required></textarea>
                            <div id="complaintError" class="invalid-feedback"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="submit_complaint" class="btn btn-primary">Submit Complaint</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Complaints Modal -->
    <div class="modal fade" id="viewComplaintsModal" tabindex="-1" aria-labelledby="viewComplaintsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewComplaintsModalLabel">View Complaints</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 id="viewComplaintsProviderName" class="mb-4"></h6>
                    <div id="complaintsContainer"></div>
                    <div id="complaintsPagination" class="complaint-pagination"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#providerTable').DataTable({
                "order": [[0, "asc"]],
                "pageLength": 10,
                "dom": '<"top"f>rt<"bottom"ip>',
                "language": {
                    "search": "Search:",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                }
            });
        });

        let currentComplaints = [];
        const complaintsPerPage = 3; // Changed from 5 to 3
        let currentPage = 1;

        function openComplaintModal(providerNum, fiscalName) {
            document.getElementById('providerNum').value = providerNum;
            document.getElementById('providerName').value = fiscalName;
            var modal = new bootstrap.Modal(document.getElementById('complaintModal'));
            modal.show();
        }

        function openViewComplaintsModal(providerNum, fiscalName) {
            document.getElementById('viewComplaintsProviderName').textContent = 'Provider: ' + fiscalName;
            currentPage = 1;
            fetchComplaints(providerNum);
            var modal = new bootstrap.Modal(document.getElementById('viewComplaintsModal'));
            modal.show();
        }

        function fetchComplaints(providerNum) {
            fetch(`?fetch_complaints=1&provider_num=${providerNum}`)
                .then(response => response.json())
                .then(data => {
                    currentComplaints = data;
                    displayComplaints();
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('complaintsContainer').innerHTML = 
                        '<div class="complaint-card"><p>Error fetching complaints. Please try again.</p></div>';
                });
        }

        function displayComplaints() {
            const container = document.getElementById('complaintsContainer');
            const paginationContainer = document.getElementById('complaintsPagination');
            container.innerHTML = '';
            
            if (currentComplaints.length === 0) {
                container.innerHTML = '<div class="complaint-card"><p>No complaints found for this provider.</p></div>';
                paginationContainer.innerHTML = '';
                return;
            }

            const startIndex = (currentPage - 1) * complaintsPerPage;
            const endIndex = startIndex + complaintsPerPage;
            const pageComplaints = currentComplaints.slice(startIndex, endIndex);

            pageComplaints.forEach(complaint => {
                const card = document.createElement('div');
                card.className = 'complaint-card';
                card.innerHTML = `
                    <div class="complaint-title">Complaint #${complaint.troubleNum}</div>
                    <h6>Complaint Date: ${formatDate(complaint.troubleDate)}</h6>
                    <h6>Complaint Description: ${complaint.description}</h6>
                `;
                container.appendChild(card);
            });

            // Update pagination
            const totalPages = Math.ceil(currentComplaints.length / complaintsPerPage);
            let paginationHTML = '';
            
            for (let i = 1; i <= totalPages; i++) {
                paginationHTML += `
                    <button onclick="changePage(${i})" class="${i === currentPage ? 'active' : ''}">${i}</button>
                `;
            }
            paginationContainer.innerHTML = paginationHTML;
        }

        function changePage(page) {
            currentPage = page;
            displayComplaints();
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function validateComplaint() {
            var description = document.getElementById('complaintDescription').value;
            var errorElement = document.getElementById('complaintError');
            
            if (description.length < 15) {
                errorElement.textContent = "Complaint description must be at least 15 characters long.";
                errorElement.style.display = 'block';
                return false;
            }
            
            if (/^\d+$/.test(description) || /^[^\w\s]+$/.test(description)) {
                errorElement.textContent = "Complaint description must contain a mix of letters, numbers, and/or symbols.";
                errorElement.style.display = 'block';
                return false;
            }
            
            if (!/[a-zA-Z]/.test(description)) {
                errorElement.textContent = "Complaint description must contain at least one letter.";
                errorElement.style.display = 'block';
                return false;
            }
            
            errorElement.style.display = 'none';
            return true;
        }
    </script>
</body>
</html>
<?php
mysqli_close($db);
?>