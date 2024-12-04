<?php
session_start();
$role = $_SESSION['role'];
?>

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

/* Custom DataTables Styling */
.dataTables_wrapper .dataTables_length select {
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 6px;
    padding: 0.375rem 0.75rem;
    background-image: none;
}

.dataTables_wrapper .dataTables_filter input {
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 6px;
    padding: 0.375rem 0.75rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border: none !important;
    background: transparent !important;
    color: #000 !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #000 !important;
    color: #fff !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #333 !important;
    color: #fff !important;
}

@media (max-width: 767px) {
    body {
        padding: 1rem;
    }
    
    .content-wrapper {
        padding: 1rem;
    }
    
    .table-container {
        overflow-x: auto;
    }
}
</style>

<div class="content-wrapper">
    <a href="../index.php" class="return-btn">
        <i class="fas fa-arrow-left me-2"></i>Return
    </a>
    
    <div class="table-container">
        <table id="providerTable" class="table table-hover">
            <thead>
                <tr>
                    <th>Provider Number</th>
                    <th>Fiscal Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Status</th>
                    <th>Materials</th>
                    <th>Modify</th>
                    <th>Remove</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                include "../includes/config/conn.php";
                $db = connect();
                $query = mysqli_query($db, "SELECT * FROM vw_provider");
                while ($result = mysqli_fetch_array($query)){ ?>
                    <tr>
                        <td><?= htmlspecialchars($result['num']) ?></td>
                        <td><?= htmlspecialchars($result['fiscalName']) ?></td>
                        <td><?= htmlspecialchars($result['email']) ?></td>
                        <td><?= htmlspecialchars($result['numTel']) ?></td>
                        <td><?= htmlspecialchars($result['status']) == 1 ? 'Associated' : 'Not Associated'?></td>
                        <td><?= htmlspecialchars($result['materials']) ?></td>
                        <td>
                            <?php if ($role === 'PR' && $result['status'] == 1): ?>
                                <a href="updateProvider.php?num=<?=$result['num']?>">Modify</a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($role === 'PR' && $result['status'] == 1): ?>
                                <a href="removeProvider.php?num=<?=$result['num']?>">Remove</a>
                            <?php elseif ($role === 'PR' && $result['status'] == 0): ?>
                                <a href="rehireProvider.php?num=<?=$result['num']?>">Re-Hire</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php } mysqli_close($db); ?>
            </tbody>
        </table>
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
            { "searchable": false, "targets": 3 }
        ],
        "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
        "responsive": true
    });
});
</script>
