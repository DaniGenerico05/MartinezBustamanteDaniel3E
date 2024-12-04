<?php
if (isset($_GET['num'])) {
    $orderNum = htmlspecialchars($_GET['num']); 

    include "../includes/config/conn.php";
    $db = connect();

    $query = mysqli_query($db, "SELECT * FROM vw_order WHERE num = '$orderNum'");
    $orderDetails = mysqli_fetch_assoc($query);
    mysqli_close($db);

    if (!$orderDetails) {
        echo "<p>Order not found.</p>";
        exit;
    }
} else {
    echo "<p>No order number provided.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .report-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .report-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .report-header h1 {
            margin: 0;
            font-size: 1.8rem;
            color: #333;
        }
        .report-header p {
            margin: 0;
            color: #666;
        }
        .report-section {
            margin-bottom: 15px;
        }
        .report-section h3 {
            font-size: 1.2rem;
            color: #000;
            margin-bottom: 10px;
        }
        .report-section p {
            margin: 0;
            color: #333;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .back-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <div class="report-header">
            <h1>Order Report</h1>
            <p><strong>Order Number:</strong> <?= htmlspecialchars($orderDetails['num']) ?></p>
        </div>
        <div class="report-section">
            <h3>Description</h3>
            <p><?= htmlspecialchars($orderDetails['description']) ?></p>
        </div>
        <div class="report-section">
            <h3>Employee</h3>
            <p><?= htmlspecialchars($orderDetails['employee']) ?></p>
        </div>
        <div class="report-section">
            <h3>Material</h3>
            <p><?= htmlspecialchars($orderDetails['rawMaterials']) ?></p>
        </div>
        <div class="report-section">
            <h3>Status</h3>
            <p><?= htmlspecialchars($orderDetails['status']) ?></p>
        </div>
        <div class="report-section">
            <h3>Creation Date</h3>
            <p><?= htmlspecialchars($orderDetails['creationDate']) ?></p>
        </div>
        <div class="report-section">
            <h3>Area</h3>
            <p><?= htmlspecialchars($orderDetails['area']) ?></p>
        </div>
        <a href="WOrder.php" class="back-btn">Back to Orders</a>
    </div>
</body>
</html>
