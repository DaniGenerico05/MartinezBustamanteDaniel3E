<?php
require '../includes/config/conn.php';
$db = connect();
session_start();

// Fetch approved orders
$query_orders = "SELECT num, description FROM orders WHERE status = 'APRV'";
$orders = mysqli_query($db, $query_orders);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_num = $_POST['order_num'];
    $estimated_date = $_POST['estimated_date'];
    $materials_data = json_decode($_POST['materials_data'], true);
    $employee = $_SESSION['num'];
    
    if (!$materials_data || empty($materials_data)) {
        echo json_encode(['success' => false, 'message' => 'No materials data provided']);
        exit;
    }

    $db->begin_transaction();
    try {
        // Insert request
        $query_request = "INSERT INTO request (order_num, estimated_date, employee) VALUES (?, ?, ?)";
        $stmt_request = $db->prepare($query_request);
        $stmt_request->bind_param("isi", $order_num, $estimated_date, $employee);
        
        if (!$stmt_request->execute()) {
            throw new Exception("Error creating request: " . $stmt_request->error);
        }
        
        $request_num = $stmt_request->insert_id;
        
        // Insert request_provider
        $query_provider = "INSERT INTO request_provider (request, provider) VALUES (?, ?)";
        $stmt_provider = $db->prepare($query_provider);

        // Insert request_material
        $query_material = "INSERT INTO request_material (request, material, quantity) VALUES (?, ?, ?)";
        $stmt_material = $db->prepare($query_material);

        foreach ($materials_data as $material) {
            // Insert provider for this material
            $stmt_provider->bind_param("ii", $request_num, $material['provider']);
            if (!$stmt_provider->execute()) {
                throw new Exception("Error adding provider: " . $stmt_provider->error);
            }

            // Insert material
            $stmt_material->bind_param("isi", $request_num, $material['code'], $material['quantity']);
            if (!$stmt_material->execute()) {
                throw new Exception("Error adding material: " . $stmt_material->error);
            }
        }

        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Request created successfully']);
        
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
    <title>Create a Request</title>
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
    </style>
</head>
<body>
    <div class="card">
        <nav><a href="../index.php" class="return-btn">Return</a></nav>
        <section id="formCont" class="container mt-5">
            <div class="form-card">
                <form id="requestForm" method="POST">
                    <h2 class="mb-4">Create a Request</h2>
                    
                    <div class="mb-3">
                        <label for="order_num" class="form-label">Select Order</label>
                        <select class="form-select" name="order_num" id="order_num" required>
                            <option value="">Select an order</option>
                            <?php while($order = mysqli_fetch_assoc($orders)): ?>
                                <option value="<?php echo $order['num']; ?>">
                                    Order #<?php echo $order['num']; ?> - <?php echo $order['description']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="estimated_date" class="form-label">Estimated Date</label>
                        <input type="date" class="form-control" name="estimated_date" id="estimated_date" required>
                    </div>

                    <div id="materialsSection" style="display: none;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Requested Raw Material</th>
                                    <th>Quantity</th>
                                    <th>Provider</th>
                                    <th>Price per unit</th>
                                </tr>
                            </thead>
                            <tbody id="materialsList"></tbody>
                        </table>
                    </div>

                    <input type="hidden" name="materials_data" id="materialsData">

                    <div class="mt-4">
                        <button type="submit" class="submit-btn">Create Request</button>
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
                    Request created successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.href='../index.php'">Return to Home</button>
                    <button type="button" class="btn btn-primary" onclick="resetForm()">Create Another Request</button>
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
                    An error occurred while creating the request.
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
        const orderSelect = document.getElementById('order_num');
        const materialsSection = document.getElementById('materialsSection');
        const materialsList = document.getElementById('materialsList');
        const materialsData = document.getElementById('materialsData');
        let materials = [];

        orderSelect.addEventListener('change', function() {
            if (this.value) {
                fetch(`get_order_materials.php?order_num=${this.value}`)
                    .then(response => response.json())
                    .then(data => {
                        materials = data;
                        updateMaterialsTable();
                        materialsSection.style.display = 'block';
                    });
            } else {
                materialsSection.style.display = 'none';
            }
        });

        function updateMaterialsTable() {
            materialsList.innerHTML = '';
            materials.forEach((material, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${material.name}</td>
                    <td>${material.quantity}</td>
                    <td>
                        <select class="form-select provider-select" data-index="${index}">
                            <option value="">Select provider</option>
                        </select>
                    </td>
                    <td class="price-cell">$0.00</td>
                `;
                
                materialsList.appendChild(row);
                const providerSelect = row.querySelector('.provider-select');
                
                fetch(`get_material_providers.php?material=${material.code}`)
                    .then(response => response.json())
                    .then(providers => {
                        providers.forEach(provider => {
                            providerSelect.innerHTML += `
                                <option value='${JSON.stringify({
                                    id: provider.num, 
                                    price: provider.price
                                })}'>${provider.fiscal_name}</option>
                            `;
                        });
                    });

                providerSelect.addEventListener('change', function() {
                    try {
                        const selectedData = JSON.parse(this.value);
                        materials[index].provider = selectedData.id;
                        materials[index].price = selectedData.price;
                        row.querySelector('.price-cell').textContent = `$${selectedData.price}`;
                        materialsData.value = JSON.stringify(materials);
                    } catch(e) {}
                });
            });
            materialsData.value = JSON.stringify(materials);
        }

        document.getElementById('requestForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('createRequest.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                } else {
                    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    document.getElementById('errorModalBody').textContent = data.message;
                    errorModal.show();
                }
            });
        });
    });

    function resetForm() {
        document.getElementById('requestForm').reset();
        document.getElementById('materialsSection').style.display = 'none';
        var successModal = bootstrap.Modal.getInstance(document.getElementById('successModal'));
        successModal.hide();
    }
    </script>
</body>
</html>