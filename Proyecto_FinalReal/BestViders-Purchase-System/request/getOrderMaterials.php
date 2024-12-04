<?php
require '../includes/config/conn.php';
$db = connect();

$order_num = $_GET['order_num'];

$query = "SELECT om.material, rm.name, om.quantity
          FROM order_material om
          JOIN raw_material rm ON om.material = rm.code
          WHERE om.order_num = ?";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $order_num);
$stmt->execute();
$result = $stmt->get_result();

$materials = [];
while ($row = $result->fetch_assoc()) {
    $materials[] = $row;
}

echo json_encode($materials);