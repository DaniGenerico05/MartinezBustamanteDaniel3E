<?php
require '../includes/config/conn.php';

$db = connect();

$query_materials = "SELECT code, name FROM raw_material";
$materials = mysqli_query($db, $query_materials);

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descrp = $_POST['descrp'];
    $selectedMaterialsAndQuantities = $_POST['materials_and_quantities'];
    $employee = $_SESSION['num'];
    $area = $_SESSION['role'];

    $db->begin_transaction();

    try {
        
        $insert_query = "INSERT INTO orders (description, employee, area) VALUES (?, ?, ?)";
        $stmt = $db->prepare($insert_query);
        $stmt->bind_param("sis", $descrp, $employee, $area);
        $stmt->execute();
        $order_num = $db->insert_id;

        $materials_and_quantities = json_decode($selectedMaterialsAndQuantities, true);

        foreach ($materials_and_quantities as $item) {
            $materialCode = $item['material'];
            $quantity = $item['quantity'];

            if (is_numeric($quantity) && $quantity > 0) {
                $insert_material_query = "INSERT INTO order_material (order_num, material, quantity) VALUES (?, ?, ?)";
                $stmt = $db->prepare($insert_material_query);
                $stmt->bind_param("isi", $order_num, $materialCode, $quantity);
                $stmt->execute();
            } else {
                throw new Exception("Invalid quantity for material $materialCode");
            }
        }

        $db->commit();
        echo json_encode(['success' => true, 'message' => 'Order created successfully']);
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
    <title>Create an Order</title>
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
            margin: 0;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0;
            padding: 0;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: none;
        }

        #Return {
            padding: 20px 20px 0;
        }

        #Return a {
            background: #1a1a1a;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        #Return a:hover {
            background: #333;
            color: #fff;
        }

        .card-body {
            padding: 2rem;
        }

        h2 {
            margin-bottom: 1.5rem;
            color: #1a1a1a;
        }

        .form-label {
            color: #1a1a1a;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #eee;
            border-radius: 8px;
            padding: 0.75rem;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #2c2c2c;
            box-shadow: none;
        }

        .mb-3 > div {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1rem;
        }

        .form-check {
            background: rgba(0, 0, 0, 0.02);
            padding: 1rem;
            border-radius: 8px;
            margin: 0;
            transition: background-color 0.3s ease;
        }

        .form-check:hover {
            background: rgba(0, 0, 0, 0.04);
        }

        .btn-primary {
            background: #1a1a1a;
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            width: 100%;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background: #4b4848;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: none;
            padding: 1.5rem;
            justify-content: space-between;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        @media (max-width: 768px) {
            .mb-3 > div {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .card-body {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .mb-3 > div {
                grid-template-columns: 1fr;
            }
            
            .container {
                padding: 0 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <nav id="Return"><a href="../index.php">Return</a></nav>
            <div class="card-body">
                <form id="orderForm" method="POST">
                    <h2>Create an Order</h2>
                    <div class="mb-3">
                        <label for="descrp" class="form-label">Description</label>
                        <textarea class="form-control" name="descrp" id="descrp" placeholder="Enter order description" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Raw Materials</label>
                        <div>
                            <?php while($material = mysqli_fetch_assoc($materials)): ?>
                                <div class="form-check">
                                    <input 
                                        class="form-check-input material-checkbox" 
                                        type="checkbox" 
                                        name="selectedMaterials[]" 
                                        id="material-<?php echo $material['code']; ?>" 
                                        value="<?php echo $material['code']; ?>"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#quantityModal" 
                                        data-material="<?php echo $material['code']; ?>"
                                        data-material-name="<?php echo $material['name']; ?>" />
                                    <label class="form-check-label" for="material-<?php echo $material['code']; ?>">
                                        <?php echo $material['name']; ?>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <input type="hidden" name="materials_and_quantities" id="hiddenMaterialsAndQuantities" />

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">ADD ORDER</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quantity Modal -->
    <div class="modal fade" id="quantityModal" tabindex="-1" aria-labelledby="quantityModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quantityModalLabel">Enter Quantity</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="modalQuantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="modalQuantity" placeholder="Enter quantity" min="1" required />
                    <input type="hidden" id="modalMaterialCode" />
                    <input type="hidden" id="modalMaterialName" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveQuantity">Save</button>
                </div>
            </div>
        </div>
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
                    Order created successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Return to Home</button>
                    <button type="button" class="btn btn-primary" onclick="resetForm()">Create Another Order</button>
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
                    An error occurred while creating the order.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const saveQuantityBtn = document.getElementById('saveQuantity');
        const modalQuantityInput = document.getElementById('modalQuantity');
        const hiddenMaterialsInput = document.getElementById('hiddenMaterialsAndQuantities');
        const modalMaterialCodeInput = document.getElementById('modalMaterialCode');
        const orderForm = document.getElementById('orderForm');
        const quantityModal = new bootstrap.Modal(document.getElementById('quantityModal'));

            let selectedMaterialCode = null;
            let selectedCheckbox = null;

            document.querySelectorAll('.material-checkbox').forEach(item => {
                item.addEventListener('change', function(event) {
                    selectedMaterialCode = this.getAttribute('data-material');
                    selectedCheckbox = this;
                    modalMaterialCodeInput.value = selectedMaterialCode;
                    
                    if (this.checked) {
                        quantityModal.show();
                    } else {
                        removeMaterial(selectedMaterialCode);
                    }
                });
            });

            document.getElementById('quantityModal').addEventListener('hidden.bs.modal', function () {
                if (selectedCheckbox) {
                    if (!getMaterialQuantity(selectedMaterialCode)) {
                        selectedCheckbox.checked = false;
                    }
                }
                modalQuantityInput.value = '';
            });

            saveQuantityBtn.addEventListener('click', function () {
                const quantity = parseInt(modalQuantityInput.value);

                if (quantity && quantity > 0) {
                    updateMaterialQuantity(selectedMaterialCode, quantity);
                    quantityModal.hide();
                } else {
                    alert('Please enter a valid quantity higger than 0.');
                    selectedCheckbox.checked = false;
                }
            });

            orderForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch(window.location.href, {
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
                    console.error('Error:', error);
                    showErrorModal('An error occurred while submitting the form.');
                });
            });

            function updateMaterialQuantity(materialCode, quantity) {
                let materials = JSON.parse(hiddenMaterialsInput.value || '[]');
                const index = materials.findIndex(m => m.material === materialCode);
                
                if (index !== -1) {
                    materials[index].quantity = quantity;
                } else {
                    materials.push({ material: materialCode, quantity: quantity });
                }
                
                hiddenMaterialsInput.value = JSON.stringify(materials);
            }

            function removeMaterial(materialCode) {
                let materials = JSON.parse(hiddenMaterialsInput.value || '[]');
                materials = materials.filter(m => m.material !== materialCode);
                hiddenMaterialsInput.value = JSON.stringify(materials);
            }

            function getMaterialQuantity(materialCode) {
                let materials = JSON.parse(hiddenMaterialsInput.value || '[]');
                const material = materials.find(m => m.material === materialCode);
                return material ? material.quantity : null;
            }

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
            orderForm.reset();
            hiddenMaterialsInput.value = '';
            document.querySelectorAll('.material-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            const successModal = bootstrap.Modal.getInstance(document.getElementById('successModal'));
            if (successModal) {
                successModal.hide();
            }
        }

 
        document.querySelector('#successModal .btn-primary').addEventListener('click', resetForm);
    });
    </script>
</body>
</html>
<?php
mysqli_close($db);
?>