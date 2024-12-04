<?php
require '../includes/config/conn.php';
$db = connect();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

file_put_contents('debug.log', date('Y-m-d H:i:s') . " - POST data: " . print_r($_POST, true) . "\n\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add_provider') {
        $fiscal_name = $_POST['fiscalName'];
        $email = $_POST['email'];
        $numTel = $_POST['numTel'];
        $materials = isset($_POST['materials']) ? $_POST['materials'] : [];

        // Check for duplicate entries
        $check_query = "SELECT * FROM provider WHERE fiscal_name = ? OR email = ? OR numTel = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bind_param("sss", $fiscal_name, $email, $numTel);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $duplicate_entry = $check_result->fetch_assoc();
            $error_message = "";
            if ($duplicate_entry['fiscal_name'] == $fiscal_name) {
                $error_message = "A provider with this fiscal name already exists.";
            } elseif ($duplicate_entry['email'] == $email) {
                $error_message = "A provider with this email already exists.";
            } elseif ($duplicate_entry['numTel'] == $numTel) {
                $error_message = "A provider with this phone number already exists.";
            }
            echo json_encode(['success' => false, 'message' => $error_message]);
            exit;
        }

        $db->begin_transaction();

        try {
            $query = "INSERT INTO provider (fiscal_name, email, numTel) VALUES (?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("sss", $fiscal_name, $email, $numTel);
            $stmt->execute();
            $provider_id = $db->insert_id;

            if (!empty($materials)) {
                $query = "INSERT INTO raw_provider (provider, material) VALUES (?, ?)";
                $stmt = $db->prepare($query);
                foreach ($materials as $material) {
                    $stmt->bind_param("is", $provider_id, $material);
                    $stmt->execute();
                }
            }

            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Provider added successfully']);
        } catch (Exception $e) {
            $db->rollback();
            file_put_contents('debug.log', date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n\n", FILE_APPEND);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

$query_materials = "SELECT code, name FROM raw_material ORDER BY name";
$materials = $db->query($query_materials);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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

        .card-container {
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
        }

        .form-card {
            padding: 2rem;
        }

        .materials-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.5rem;
        }
    </style>
</head>
<body>
<div class="card-container">
    <a href="../index.php" class="return-btn">
        <i class="fas fa-arrow-left me-2"></i>Return
    </a>
    <div class="form-card">
        <form id="providerForm">
            <h2 class="mb-4">Provider Information</h2>

            <div class="form-group mb-3">
                <label for="fiscalName">Fiscal Name</label>
                <input type="text" name="fiscalName" id="fiscalName" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="numTel">Phone Number</label>
                <input type="tel" name="numTel" id="numTel" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label>Raw Materials</label>
                <div class="materials-list">
                    <?php while ($material = $materials->fetch_assoc()): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="materials[]" 
                               value="<?= htmlspecialchars($material['code']) ?>" 
                               id="material_<?= htmlspecialchars($material['code']) ?>">
                        <label class="form-check-label" for="material_<?= htmlspecialchars($material['code']) ?>">
                            <?= htmlspecialchars($material['name']) ?>
                        </label>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="button-container mt-4">
                <button type="submit" class="btn btn-dark w-100">Add Provider</button>
            </div>
        </form>
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
                Provider added successfully!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Return to Home</button>
                <button type="button" class="btn btn-primary" onclick="resetForm()">Add Another Provider</button>
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
                An error occurred while adding the provider.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('providerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'add_provider');

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
        showErrorModal('An error occurred. Please try again.');
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
    document.getElementById('providerForm').reset();
    bootstrap.Modal.getInstance(document.getElementById('successModal')).hide();
}
</script>
</body>
</html>
<?php
mysqli_close($db);
?>