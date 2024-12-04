<?php
require '../includes/config/conn.php';
$db = connect();
session_start();

// Handle AJAX request for request details
if (isset($_GET['action']) && $_GET['action'] == 'get_request_details' && isset($_GET['request_num'])) {
    $request_num = intval($_GET['request_num']);
    
    try {
        // Get request and order details
        $query = "SELECT r.*, o.description as order_description, 
                         CONCAT(e.firstName, ' ', e.lastName) as employee_name
                  FROM request r
                  JOIN orders o ON r.order_num = o.num
                  JOIN employee e ON r.employee = e.num
                  WHERE r.num = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("i", $request_num);
        $stmt->execute();
        $result = $stmt->get_result();
        $request_data = $result->fetch_assoc();
        
        if (!$request_data) {
            echo json_encode(['error' => 'Request not found']);
            exit;
        }
        
        // Get materials
        $materials_query = "SELECT rm.*, m.name, m.code 
                          FROM request_material rm
                          JOIN raw_material m ON rm.material = m.code
                          WHERE rm.request = ?";
        $stmt = $db->prepare($materials_query);
        $stmt->bind_param("i", $request_num);
        $stmt->execute();
        $materials_result = $stmt->get_result();
        $materials = [];
        while ($row = $materials_result->fetch_assoc()) {
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
        $stmt->bind_param("i", $request_num);
        $stmt->execute();
        $providers_result = $stmt->get_result();
        $providers = [];
        while ($row = $providers_result->fetch_assoc()) {
            $providers[] = [
                'name' => $row['name'],
                'email' => $row['email'],
                'phone' => $row['numTel']
            ];
        }
        
        $response = [
            'success' => true,
            'data' => [
                'request_date' => $request_data['request_date'],
                'estimated_date' => $request_data['estimated_date'],
                'order_num' => $request_data['order_num'],
                'order_description' => $request_data['order_description'],
                'employee_name' => $request_data['employee_name'],
                'materials' => $materials,
                'providers' => $providers
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } catch (Exception $e) {
        error_log('Error in get_request_details: ' . $e->getMessage());
        echo json_encode(['error' => 'An error occurred while fetching request details']);
        exit;
    }
}

// Fetch pending and in process requests with additional information
$query_requests = "SELECT r.num, o.description, o.num as order_num 
                   FROM request r 
                   JOIN orders o ON r.order_num = o.num 
                   WHERE r.status IN ('PEND', 'PROC')";
$requests = mysqli_query($db, $query_requests);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_num = $_POST['request_num'];
    $observations = $_POST['observations'];
    $employee = $_SESSION['num'];
    
    $db->begin_transaction();
    try {
        // Insert reception
        $query_reception = "INSERT INTO reception (observations, employee, request) VALUES (?, ?, ?)";
        $stmt_reception = $db->prepare($query_reception);
        $stmt_reception->bind_param("sii", $observations, $employee, $request_num);
        
        if (!$stmt_reception->execute()) {
            throw new Exception("Error creating reception: " . $stmt_reception->error);
        }
        
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Reception created successfully']);
    } catch (Exception $e) {
        $db->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a Reception</title>
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
            font-family: Arial, sans-serif;
        }
        .card {
            width: 100%;
            max-width: 800px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .return-btn {
            background: #1a1a1a;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin: 20px;
            transition: background-color 0.3s ease;
        }
        .return-btn:hover {
            background: #333;
            color: #fff;
            text-decoration: none;
        }
        .form-card {
            padding: 2rem;
        }
        .submit-btn {
            background: #1a1a1a;
            color: #fff;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submit-btn:hover {
            background: #333;
        }
        .request-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .request-info h4 {
            margin-bottom: 15px;
            color: #333;
        }
        .info-row {
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="card">
        <nav><a href="../index.php" class="return-btn">Return</a></nav>
        <section id="formCont" class="container mt-5">
            <div class="form-card">
                <form id="receptionForm" method="POST">
                    <h2 class="mb-4">Create a Reception</h2>
                    
                    <div class="mb-3">
                        <label for="request_num" class="form-label">Select Request</label>
                        <select class="form-select" name="request_num" id="request_num" required>
                            <option value="">Select a request</option>
                            <?php while($request = mysqli_fetch_assoc($requests)): ?>
                                <option value="<?php echo $request['num']; ?>">
                                    Request #<?php echo $request['num']; ?> - Order #<?php echo $request['order_num']; ?> - <?php echo $request['description']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div id="requestInfo" class="request-info" style="display: none;">
                        <h4>Request Information</h4>
                        <div id="requestDetails"></div>
                    </div>

                    <div class="mb-3">
                        <label for="observations" class="form-label">Observations</label>
                        <textarea class="form-control" name="observations" id="observations" rows="3"></textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="submit-btn">Create Reception</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Reception created successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Return to Home</button>
                    <button type="button" class="btn btn-primary" onclick="resetForm()">Create Another Reception</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="errorModalBody">
                    An error occurred while creating the reception.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('receptionForm');
        const requestSelect = document.getElementById('request_num');
        const requestInfo = document.getElementById('requestInfo');
        const requestDetails = document.getElementById('requestDetails');

        requestSelect.addEventListener('change', function() {
            if (this.value) {
                fetch(`?action=get_request_details&request_num=${this.value}`)
                    .then(response => response.json())
                    .then(response => {
                        if (response.error) {
                            console.error(response.error);
                            requestInfo.style.display = 'none';
                            return;
                        }
                        
                        const data = response.data;
                        const html = `
                            <div class="info-row">
                                <span class="info-label">Order:</span> #${data.order_num} - ${data.order_description}
                            </div>
                            <div class="info-row">
                                <span class="info-label">Creation Date:</span> ${data.request_date}
                            </div>
                            <div class="info-row">
                                <span class="info-label">Estimated Date:</span> ${data.estimated_date}
                            </div>
                            <div class="info-row">
                                <span class="info-label">Requested By:</span> ${data.employee_name}
                            </div>
                            <div class="info-row">
                                <span class="info-label">Materials:</span>
                                <ul class="list-unstyled ms-3">
                                    ${data.materials.map(m => 
                                        `<li>${m.name} (${m.code}) - Quantity: ${m.quantity} - Amount: $${m.amount}</li>`
                                    ).join('')}
                                </ul>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Providers:</span>
                                <ul class="list-unstyled ms-3">
                                    ${data.providers.map(p => 
                                        `<li>${p.name} (${p.email} - ${p.phone})</li>`
                                    ).join('')}
                                </ul>
                            </div>
                        `;
                        requestDetails.innerHTML = html;
                        requestInfo.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        requestInfo.style.display = 'none';
                    });
            } else {
                requestInfo.style.display = 'none';
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessModal();
                } else {
                    showErrorModal(data.message);
                }
            })
            .catch(error => {
                showErrorModal('An error occurred while submitting the form.');
            });
        });
    });

    function showSuccessModal() {
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    }

    function showErrorModal(message) {
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        document.getElementById('errorModalBody').textContent = message;
        errorModal.show();
    }

    function resetForm() {
        document.getElementById('receptionForm').reset();
        document.getElementById('requestInfo').style.display = 'none';
        bootstrap.Modal.getInstance(document.getElementById('successModal')).hide();
    }
    </script>
</body>
</html>
<?php
mysqli_close($db);
?>