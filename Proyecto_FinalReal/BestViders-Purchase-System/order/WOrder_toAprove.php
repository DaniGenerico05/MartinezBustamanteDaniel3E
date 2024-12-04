<?php
include "../includes/config/conn.php";
$db = connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['action'] == 'approve' ? 'APRV' : 'REJD';
    $motive = isset($_POST['motive']) ? $_POST['motive'] : null;

    $query = "UPDATE orders SET status = ?, motive = ? WHERE num = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('ssi', $new_status, $motive, $order_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = $new_status == 'APRV' ? "Orden aprobada con éxito." : "Orden rechazada con éxito.";
    } else {
        $message = "Error al procesar la orden o la orden ya está procesada.";
    }

    $stmt->close();
}

$query = "SELECT o.*, GROUP_CONCAT(CONCAT(rm.name, ' (', om.quantity, ')') SEPARATOR ', ') AS materials
            FROM orders o
            LEFT JOIN order_material om ON o.num = om.order_num
            LEFT JOIN raw_material rm ON om.material = rm.code
            WHERE o.status = 'PEND'
            GROUP BY o.num";
$result = mysqli_query($db, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($db));
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background-image: url('https://4kwallpapers.com/images/wallpapers/macos-monterey-stock-black-dark-mode-layers-5k-4480x2520-5889.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 2rem; 
        }

        .content-wrapper {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 0 auto;
            max-width: 1400px;
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

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: #000;
            color: #fff;
            font-weight: 500;
            border: none;
            padding: 1rem;
        }

        .table tbody tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: rgba(0, 0, 0, 0.05);
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 6px;
            padding: 0.375rem 0.75rem;
        }

        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #000;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
            outline: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #000 !important;
            border-color: #000 !important;
            color: #fff !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #333 !important;
            border-color: #333 !important;
            color: #fff !important;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <a href="../index.php" class="return-btn">
            <i class="fas fa-arrow-left me-2"></i>Return
        </a>

        <?php if (isset($message)): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="table" id="ordersTable">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Description</th>
                        <th>Employee</th>
                        <th>Materials</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Area</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['num']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= htmlspecialchars($row['employee']) ?></td>
                            <td><?= htmlspecialchars($row['materials']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['creationDate']) ?></td> 
                            <td><?= htmlspecialchars($row['area']) ?></td>
                            <td>
                                <button class="btn btn-success btn-sm approve-btn" data-order-id="<?= htmlspecialchars($row['num']) ?>">
                                    Approve
                                </button>
                                <button class="btn btn-danger btn-sm reject-btn" data-order-id="<?= htmlspecialchars($row['num']) ?>">
                                    Reject
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectForm">
                        <input type="hidden" id="rejectOrderId" name="order_id">
                        <input type="hidden" name="action" value="reject">
                        <div class="mb-3">
                            <label for="rejectMotive" class="form-label">Motive for Rejection</label>
                            <textarea class="form-control" id="rejectMotive" name="motive" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">Confirm Rejection</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script>

    <script>
    $(document).ready(function() {
        $('#ordersTable').DataTable({
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            responsive: true
        });

        $('.approve-btn').click(function() {
            var orderId = $(this).data('order-id');
            if (confirm('Are you sure you want to approve this order?')) {
                $.post('', { order_id: orderId, action: 'approve' }, function() {
                    location.reload();
                });
            }
        });

        $('.reject-btn').click(function() {
            var orderId = $(this).data('order-id');
            $('#rejectOrderId').val(orderId);
            $('#rejectModal').modal('show');
        });

        $('#confirmReject').click(function() {
            if ($('#rejectMotive').val().trim() !== '') {
                $.post('', $('#rejectForm').serialize(), function() {
                    location.reload();
                });
            } else {
                alert('Please enter a reason for the rejection.');
            }
        });
    });
    </script>
</body>
</html>