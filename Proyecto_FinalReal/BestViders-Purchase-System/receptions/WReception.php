<?php
require '../includes/config/conn.php';
$db = connect();

// Define title after DB connection
$pageTitle = "Reception History";

// Pagination
$items_per_page = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Get total number of receptions
$total_query = "SELECT COUNT(*) as total FROM reception";
$total_result = mysqli_query($db, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_pages = ceil($total_row['total'] / $items_per_page);

// Get receptions with related information
$query = "SELECT r.num as reception_num, r.observations, r.receptionDate,
                 req.num as request_num, req.request_date, req.estimated_date,
                 CONCAT(e.firstName, ' ', e.lastName) as employee_name,
                 CONCAT(req_e.firstName, ' ', req_e.lastName) as requester_name
          FROM reception r
          JOIN request req ON r.request = req.num
          JOIN employee e ON r.employee = e.num
          JOIN employee req_e ON req.employee = req_e.num
          ORDER BY r.receptionDate DESC
          LIMIT ?, ?";

// Use prepared statement
$stmt = $db->prepare($query);
if ($stmt === false) {
    die("Error preparing statement: " . $db->error);
}

$stmt->bind_param("ii", $offset, $items_per_page);
$stmt->execute();
$receptions = $stmt->get_result();

// Handle AJAX request for reception details
if(isset($_GET['action']) && $_GET['action'] == 'get_details' && isset($_GET['reception_num'])) {
    $reception_num = intval($_GET['reception_num']);
    
    // Get detailed reception information
    $details_query = "SELECT r.num as reception_num, r.observations, r.receptionDate,
                            req.num as request_num, req.request_date, req.estimated_date,
                            CONCAT(e.firstName, ' ', e.lastName) as employee_name,
                            CONCAT(req_e.firstName, ' ', req_e.lastName) as requester_name
                     FROM reception r
                     JOIN request req ON r.request = req.num
                     JOIN employee e ON r.employee = e.num
                     JOIN employee req_e ON req.employee = req_e.num
                     WHERE r.num = ?";
    
    $stmt = $db->prepare($details_query);
    if ($stmt === false) {
        die("Error preparing details statement: " . $db->error);
    }
    $stmt->bind_param("i", $reception_num);
    $stmt->execute();
    $result = $stmt->get_result();
    $reception_data = $result->fetch_assoc();
    
    // Get materials
    $materials_query = "SELECT rm.*, m.name, m.code 
                       FROM request_material rm
                       JOIN raw_material m ON rm.material = m.code
                       WHERE rm.request = ?";
    $stmt = $db->prepare($materials_query);
    if ($stmt === false) {
        die("Error preparing materials statement: " . $db->error);
    }
    $stmt->bind_param("i", $reception_data['request_num']);
    $stmt->execute();
    $materials_result = $stmt->get_result();
    $materials = [];
    while($row = $materials_result->fetch_assoc()) {
        $materials[] = [
            'code' => $row['code'],
            'name' => $row['name'],
            'quantity' => $row['quantity'],
            'amount' => $row['amount']
        ];
    }
    
    // Get providers
    $providers_query = "SELECT p.*, p.fiscal_name as name
                       FROM request_provider rp
                       JOIN provider p ON rp.provider = p.num
                       WHERE rp.request = ?";
    $stmt = $db->prepare($providers_query);
    if ($stmt === false) {
        die("Error preparing providers statement: " . $db->error);
    }
    $stmt->bind_param("i", $reception_data['request_num']);
    $stmt->execute();
    $providers_result = $stmt->get_result();
    $providers = [];
    while($row = $providers_result->fetch_assoc()) {
        $providers[] = [
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['numTel']
        ];
    }
    
    $response = [
        'reception' => $reception_data,
        'materials' => $materials,
        'providers' => $providers
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background-image: url('https://4kwallpapers.com/images/wallpapers/macos-monterey-stock-black-dark-mode-layers-5k-4480x2520-5889.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .main-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 2rem;
        }

        .return-btn {
            background: #1a1a1a;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .return-btn:hover {
            background: #333;
            color: #fff;
            text-decoration: none;
        }

        .page-title {
            color: #1a1a1a;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
            text-align: center;
        }

        .reception-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .reception-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }

        .reception-card:hover {
            transform: translateY(-5px);
        }

        .reception-card h3 {
            color: #1a1a1a;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .reception-info {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .view-details-btn {
            background: #1a1a1a;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            width: 100%;
            margin-top: 1rem;
            transition: background-color 0.3s ease;
        }

        .view-details-btn:hover {
            background: #333;
        }

        .pagination {
            justify-content: center;
            gap: 0.5rem;
        }

        .pagination .page-link {
            background: #1a1a1a;
            border: none;
            color: #fff;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .pagination .page-link:hover {
            background: #333;
        }

        .pagination .active .page-link {
            background: #444;
        }

        .modal-content {
            background: #fff;
            border-radius: 15px;
        }

        .modal-header {
            border-bottom: 1px solid #eee;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid #eee;
            padding: 1.5rem;
        }

        .detail-section {
            margin-bottom: 1.5rem;
        }

        .detail-section h4 {
            color: #1a1a1a;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            margin-bottom: 0.5rem;
        }

        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #666;
        }

        .detail-value {
            color: #333;
        }

        @media (max-width: 992px) {
            .reception-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .reception-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="section-card">
            <a href="../index.php" class="return-btn">Return</a>
            
            <h1 class="page-title"><?php echo $pageTitle; ?></h1>
            
            <div class="reception-grid">
                <?php 
                if ($receptions && $receptions->num_rows > 0) {
                    while($reception = $receptions->fetch_assoc()): 
                ?>
                    <div class="reception-card">
                        <h3>Reception #<?php echo htmlspecialchars($reception['reception_num']); ?></h3>
                        <div class="reception-info">
                            <strong>Request:</strong> #<?php echo htmlspecialchars($reception['request_num']); ?>
                        </div>
                        <div class="reception-info">
                            <strong>Received by:</strong> <?php echo htmlspecialchars($reception['employee_name']); ?>
                        </div>
                        <div class="reception-info">
                            <strong>Reception Date:</strong> <?php echo date('Y-m-d', strtotime($reception['receptionDate'])); ?>
                        </div>
                        <div class="reception-info">
                            <strong>Request Date:</strong> <?php echo date('Y-m-d', strtotime($reception['request_date'])); ?>
                        </div>
                        <button class="view-details-btn" 
                                onclick="viewReceptionDetails(<?php echo $reception['reception_num']; ?>)">
                            View Details
                        </button>
                    </div>
                <?php 
                    endwhile;
                } else {
                    echo '<p class="text-center">No receptions found.</p>';
                }
                ?>
            </div>
            
            <!-- Pagination -->
            <nav aria-label="Reception history pagination">
                <ul class="pagination">
                    <?php if($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page-1; ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page+1; ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Reception Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewReceptionDetails(receptionNum) {
            fetch(`?action=get_details&reception_num=${receptionNum}`)
                .then(response => response.json())
                .then(data => {
                    const modalContent = document.getElementById('modalContent');
                    
                    let materialsHtml = data.materials.map(m => 
                        `<div class="detail-item">
                            <span class="detail-value">${m.name} (${m.code}) - Quantity: ${m.quantity} - Amount: $${m.amount}</span>
                        </div>`
                    ).join('');
                    
                    let providersHtml = data.providers.map(p => 
                        `<div class="detail-item">
                            <span class="detail-value">${p.name} (${p.email} - ${p.phone})</span>
                        </div>`
                    ).join('');
                    
                    modalContent.innerHTML = `
                        <div class="detail-section">
                            <h4>Reception Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Reception Number:</span>
                                <span class="detail-value">#${data.reception.reception_num}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Reception Date:</span>
                                <span class="detail-value">${data.reception.receptionDate}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Received By:</span>
                                <span class="detail-value">${data.reception.employee_name}</span>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h4>Request Information</h4>
                            <div class="detail-item">
                                <span class="detail-label">Request Number:</span>
                                <span class="detail-value">#${data.reception.request_num}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Request Date:</span>
                                <span class="detail-value">${data.reception.request_date}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Estimated Date:</span>
                                <span class="detail-value">${data.reception.estimated_date}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Requested By:</span>
                                <span class="detail-value">${data.reception.requester_name}</span>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h4>Materials</h4>
                            ${materialsHtml}
                        </div>
                        
                        <div class="detail-section">
                            <h4>Providers</h4>
                            ${providersHtml}
                        </div>
                        
                        <div class="detail-section">
                            <h4>Observations</h4>
                            <div class="detail-item">
                                <span class="detail-value">${data.reception.observations || 'No observations'}</span>
                            </div>
                        </div>
                    `;
                    
                    const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
                    modal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading reception details');
                });
        }
    </script>
</body>
</html>
<?php
$stmt->close();
mysqli_close($db);
?>