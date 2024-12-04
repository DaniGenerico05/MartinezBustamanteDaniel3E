<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../includes/config/conn.php';
$db = connect();

$material = $_GET['material'];

$query = "SELECT p.num, p.fiscal_name, rp.price
          FROM provider p
          JOIN raw_provider rp ON p.num = rp.provider
          WHERE rp.material = ? AND p.status = TRUE";

$stmt = $db->prepare($query);
if (!$stmt) {
    error_log("Prepare failed: " . $db->error);
    echo json_encode(['error' => 'Database error']);
    exit;
}

$stmt->bind_param("s", $material);
if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    echo json_encode(['error' => 'Query execution failed']);
    exit;
}

$result = $stmt->get_result();

$providers = [];
while ($row = $result->fetch_assoc()) {
    $providers[] = $row;
}

header('Content-Type: application/json');
echo json_encode($providers);
?>