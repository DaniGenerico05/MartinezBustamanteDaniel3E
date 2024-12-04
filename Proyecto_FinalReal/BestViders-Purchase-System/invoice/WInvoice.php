<?php
include "../includes/config/conn.php";
$db = connect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice List</title>
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
        
        <div class="table-container">
            <table class="table" id="invoicesTable">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Amount</th>
                        <th>Subtotal</th>
                        <th>IVA</th>
                        <th>Pay Date</th>
                        <th>Request</th>
                        <th>Provider</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT i.folio, i.amount, i.subtotal, i.iva, i.payDate, i.request, p.fiscal_name AS provider_name 
                                FROM invoice i 
                                INNER JOIN provider p ON i.provider = p.num";
                    $result = mysqli_query($db, $query);

                    while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= htmlspecialchars($row['folio']) ?></td>
                            <td><?= htmlspecialchars($row['amount']) ?></td>
                            <td><?= htmlspecialchars($row['subtotal']) ?></td>
                            <td><?= htmlspecialchars($row['iva']) ?></td>
                            <td><?= htmlspecialchars($row['payDate']) ?></td>
                            <td><?= htmlspecialchars($row['request']) ?></td>
                            <td><?= htmlspecialchars($row['provider_name']) ?></td>
                        </tr>
                    <?php } 
                    mysqli_close($db);
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script>

    <script>
    $(document).ready(function() {
        $('#invoicesTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "pageLength": 10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
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
            "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
            "responsive": true
        });
        $('.dataTables_length label').find('select').removeClass('form-select');
    });
    </script>
</body>
</html>