<?php
require '../includes/config/conn.php';
$db = connect();
header('Content-Type: application/json');
if (isset($_GET['provider'])) {
    $provider_id = $_GET['provider'];
        $query = "SELECT rm.code, rm.name 
                FROM raw_material rm
                INNER JOIN raw_provider rp ON rm.code = rp.material
                WHERE rp.provider = ?
                ORDER BY rm.name";
    
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $provider_id);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $materials = [];
        
        while ($row = $result->fetch_assoc()) {
            $materials[] = [
                'code' => $row['code'],
                'name' => $row['name']
            ];
        }
        
        echo json_encode($materials);
    } else {
        echo json_encode(['error' => 'Failed to fetch materials']);
    }
} else {
    echo json_encode(['error' => 'Provider ID not specified']);
}
$db->close();
?>

