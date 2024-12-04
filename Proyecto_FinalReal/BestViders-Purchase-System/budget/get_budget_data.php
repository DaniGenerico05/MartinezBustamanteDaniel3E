<?php
include "../includes/config/conn.php";
$db = connect();

$month = mysqli_real_escape_string($db, $_GET['month']);
$year = mysqli_real_escape_string($db, $_GET['year']);

$query = "SELECT b.code, b.initialAmount, b.budgetRemain, b.budgetMonth, b.budgetYear, a.name AS area_name 
          FROM budget b 
          INNER JOIN area a ON b.area = a.code 
          WHERE b.budgetMonth = $month AND b.budgetYear = $year";

$result = mysqli_query($db, $query);
$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        'code' => $row['code'],
        'initialAmount' => floatval($row['initialAmount']),
        'budgetRemain' => floatval($row['budgetRemain']),
        'budgetMonth' => $row['budgetMonth'],
        'budgetYear' => $row['budgetYear'],
        'area_name' => $row['area_name']
    ];
}

mysqli_close($db);
echo json_encode($data);