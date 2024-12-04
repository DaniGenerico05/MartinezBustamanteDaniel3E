<?php
session_start();
include "../includes/config/conn.php";
$db = connect();

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
        .action-btn {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            text-align: center;
            border-radius: 0.375rem;
            transition: all 0.15s ease-in-out;
            margin: 0 0.25rem;
        }
        .modify-btn {
            background-color: #0d6efd;
            color: #fff;
        }
        .modify-btn:hover {
            background-color: #0b5ed7;
            color: #fff;
        }
        .remove-btn {
            background-color: #dc3545;
            color: #fff;
        }
        .remove-btn:hover {
            background-color: #bb2d3b;
            color: #fff;
        }
        .rehire-btn {
            background-color: #198754;
            color: #fff;
        }
        .rehire-btn:hover {
            background-color: #157347;
            color: #fff;
        }
        .view-complaints-btn {
            background-color: #6c757d;
            color: #fff;
        }
        .view-complaints-btn:hover {
            background-color: #5a6268;
            color: #fff;
        }
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
        
        <div class="table-container">
            <table id="providerTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Fiscal Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Materials</th>
                        <th>Complaints</th>
                        <th>Actions</th>
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
                                <button class="action-btn view-complaints-btn" onclick="openViewComplaintsModal(<?= $result['num'] ?>, '<?= htmlspecialchars($result['fiscalName']) ?>')">View Complaints</button>
                            </td>
                            <td>
                                <?php if ($_SESSION['role'] == 'PR' && $result['status'] == 1): ?>
                                    <a href="updateProvider.php?num=<?=$result['num']?>" class="action-btn modify-btn">Modify</a>
                                    <a href="removeProvider.php?num=<?=$result['num']?>" class="action-btn remove-btn">Remove</a>
                                <?php elseif ($_SESSION['role'] == 'PR' && $result['status'] == 0): ?>
                                    <a href="rehireProvider.php?num=<?=$result['num']?>" class="action-btn rehire-btn">Re-Hire</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
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
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('#providerTable').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "pageLength": 10,
                "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
                "language": {
                    "lengthMenu": "Show _MENU_ entries",
                    "search": "Search:",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    },
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)"
                },
                "columnDefs": [
                    { "searchable": false, "targets": [4, 5] }
                ],
                "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
                "responsive": true
            });
        });

        let currentComplaints = [];
        const complaintsPerPage = 3;
        let currentPage = 1;

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
    </script>
</body>
</html>
<?php mysqli_close($db); ?>