<?php
require '../includes/config/conn.php';
$db = connect();

$provider = $_GET['provider'];
$order_num = $_GET['order_num'];

$query = "SELECT om.material, rm.name, om.quantity, rp.price
          FROM order_material om
          JOIN raw_material rm ON om.material = rm.code
          JOIN raw_provider rp ON om.material = rp.material
          WHERE om.order_num = ? AND rp.provider = ?";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $order_num, $provider);
$stmt->execute();
$result = $stmt->get_result();

$materials = [];
while ($row = $result->fetch_assoc()) {
    $materials[] = $row;
}

echo json_encode($materials);